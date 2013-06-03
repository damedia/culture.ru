<?php

namespace Armd\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Armd\MainBundle\Menu\Builder as MenuBuilder;

class BuildCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var int
     */
    protected $urlsPerFile = 10000;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('armd:sitemap:build')
            ->setDescription('Build sitemap.xml')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $urls = $this->getUrls();
        $this->dumpXml($urls);
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        if (!$this->doctrine) {
            $this->doctrine = $this->getContainer()->get('doctrine');
        }

        return $this->doctrine;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getEntityManager();
        }

        return $this->em;
    }

    /**
     * @return string
     */
    protected function getDefaultLocale()
    {
        return $this->getContainer()->getParameter('armd_sitemap.default_locale');
    }

    /**
     * @return array
     */
    protected function getLocales()
    {
        $locales = $this->getContainer()->getParameter('armd_sitemap.locales');

        if (!$locales) {
            $locales = array($this->getDefaultLocale());
        }

        return $locales;
    }

    /**
     * @return array
     */
    protected function getUrls()
    {
        if ($menu = $this->getMenu()) {
            $urls = $this->getMenuUrls($menu);
        }

        $urls[] = array(
            'loc' => $this->getHost(),
            'lastmod' => null
        );

        $urls = array_unique($urls, SORT_REGULAR);
        
        usort($urls, function($a, $b) {
            return strcmp($a['loc'], $b['loc']);
        });

        return $urls;
    }

    /**
     * @return \Knp\Menu\MenuItem
     */
    protected function getMenu()
    {
        $locales       = $this->getLocales();
        $defaultLocale = $this->getDefaultLocale();
        $menuBuilder   = $this->getContainer()->get('armd_main.menu_builder');
        
        $request = new Request();
        $request->setLocale($defaultLocale);
        $menu = $menuBuilder->createMainMenu($request);

        if ($locales) {
            foreach ($locales as $locale) {
                if ($locale !== $defaultLocale) {
                    $request = new Request();
                    $request->setLocale($locale);
                
                    if ($localeMenu = $menuBuilder->createMainMenu($request) and $localeChildren = $localeMenu->getChildren()) {
                        foreach ($localeChildren as $child) {
                            $child->setParent(null);
                            $child->setUri('/' .$locale .$child->getUri());
                            $menu->addChild($child);
                        }
                    }
                }
            }
        }
        
        return $menu;
    }

    /**
     * @param \Knp\Menu\MenuItem $menuItem
     * @return array
     */
    protected function getMenuUrls($menuItem)
    {
        $urls = array();

        if ($childs = $menuItem->getChildren()) {
            $router = $this->getContainer()->get('router');

            foreach ($childs as $child) {
                if ($childUri = $child->getUri()) {
                    $urls[] = array(
                        'loc' => $this->getAbsoluteUrl($childUri),
                        'lastmod' => null
                    );
                }

                try {
                    $route = $router->match($child->getUri());

                } catch (ResourceNotFoundException $e) {
                    $route = null;
                }

                if ($route) {
                    if (isset($route['_controller'])) {
                        list($class, $action) = explode('::', $route['_controller']);
                        
                        // If getItemsSitemap method exists, get items
                        if (method_exists($class, 'getItemsSitemap')) {
                            $controller = new $class();
                            $controller->setContainer($this->getContainer());

                            // Get params
                            $params = array_intersect_key($route, array_flip(array_filter(array_keys($route), function($paramKey) {
                                return substr($paramKey, 0, 1) !== '_';
                            })));

                            if ($items = $controller->getItemsSitemap($action, $params)) {
                                foreach ($items as $i) {
                                    if ($i) {
                                        $i['loc'] = $this->getAbsoluteUrl($i['loc']);
                                        $urls[] = $i;
                                    }
                                }
                            }

                            unset($controller, $params, $items);
                        }
                    }

                    if ($child->getChildren()) {
                        $childs = $this->getMenuUrls($child);

                        foreach ($childs as $c) {
                            $c['loc'] = $this->getAbsoluteUrl($c['loc']);
                            $urls[] = $c;
                        }

                        unset($childs);
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * Get content of sitemap-index.xml or sitemap.xml
     * @param array $data
     * @param bool $index
     * @return string
     */
    protected function renderXml($data, $index = false)
    {
        $engine  = $this->getContainer()->get('templating');
        $content = $engine->render('ArmdSitemapBundle:Sitemap:sitemap' .($index ? 'index' : '') .'.xml.twig', $data);

        return $content;
    }

    /**
     * Save sitemap-index.xml and sitemap-X.xml
     * @param array $urls
     */
    protected function dumpXml($urls)
    {
        $webRootDir = $this->getContainer()->get('kernel')->getRootDir() .'/../web';
        $sitemapWebDir = '/' . $this->getContainer()->getParameter('armd_sitemap.directory');
        $sitemapDir = $webRootDir . $sitemapWebDir;

        // Remove old sitemap files.
        $finder     = new Finder();
        $filesystem = new Filesystem();
        $filesystem->remove($finder->files()->name('sitemap*.xml')->in($sitemapDir));

        // Make new sitemap files
        $urlsPerFile = $this->getContainer()->getParameter('armd_sitemap.urls_per_file');
        $sitemaps    = array();
        $index       = 1;

        while ($fileUrls = array_splice($urls, 0, $urlsPerFile)) {
            $sitemapUrl = $sitemapWebDir . '/sitemap-' .$index .'.xml';
            $sitemapFile = $webRootDir . $sitemapUrl;

            file_put_contents(
                $sitemapFile,
                $this->renderXml(array('urls' => $fileUrls))
            );

            $sitemaps[] = array(
                'url' => $this->getAbsoluteUrl($sitemapUrl),
                'date' => new \DateTime()
            );

            $index += 1;
        }

        file_put_contents(
            $sitemapDir .'/sitemap-index.xml',
            $this->renderXml(array('sitemaps' => $sitemaps), true)
        );
    }

    /**
     * Absolute URL from relative.
     * @param string $url
     */
    protected function getAbsoluteUrl($url)
    {
        $urlParts = parse_url($url);

        if (!isset($urlParts['host'])) {
            $url = $this->getHost() .$url;
        }

        return $url;
    }

    /**
     * @return string
     */
    protected function getHost()
    {
        return $this->getContainer()->getParameter('armd_sitemap.scheme') .'://' .$this->getContainer()->getParameter('armd_sitemap.host');
    }
}
