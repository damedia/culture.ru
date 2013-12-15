<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Damedia\SpecialProjectBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpecialProjectHelper {
    public function getTwigTemplatesPath(\AppKernel $kernel) {
        return $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');
    }

    public function getBlockNamesFromString($string) { // rename this method!!!
        $result = array();

        preg_match_all('/{{\sBlocks\.(\w+)\|?(\w+)?\s}}/im', $string, $matches);

        if (!isset($matches[1])) {
        	return $result;
        }
        
        // Can't use array_flip() here because I want to set empty string values
        foreach ($matches[1] as $placeholder) {
        	$result[$placeholder] = '';
        }

        return $result;
    }
    
    public function collectPageBreadcrumbs(Controller $controller, Page $page, &$result = array()) {
    	if (count($result) == 0) {
            $result = $this->createInitialBreadcrumbsArray($controller);
    	}
    	
    	$parentPage = $page->getParent();
    	
    	if ($parentPage) {
    		$this->collectPageBreadcrumbs($controller, $parentPage, $result);
    		$result[] = $this->createBreadcrumbItem($controller->generateUrl('damedia_special_project_view', array('slug' => $parentPage->getSlug())), $parentPage->getTitle());
    	}
    	
    	return;
    }

    public function renderSpecialProjectPage(Controller $controller, Page $page, $errorMessage = 'Ошибка генерации страницы!') {
        if (!$page) {
            return $controller->render('DamediaSpecialProjectBundle:Default:error.html.twig',
                array('error' => $errorMessage));
        }

        $title = $page->getTitle();
        $template = $page->getTemplate();
        $twigFileName = $template->getTwigFileName();

        $breadcrumbs = array();
        $this->collectPageBreadcrumbs($controller, $page, $breadcrumbs);
        $breadcrumbs[] = $this->createBreadcrumbItem($controller->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())), $page->getTitle(), true);

        $appKernel = $controller->get('kernel');
        $twigTemplatesPath = $this->getTwigTemplatesPath($appKernel);
        $fileContent = @file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // throw new TemplateNotFoundException;
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        if ($fileContent === false) {
            return $controller->render('DamediaSpecialProjectBundle:Default:error.html.twig',
                array('error' => 'Не найден файл шаблона <span class="variable">'.$twigFileName.'</span> для страницы <span class="variable">'.$title.'</span>!'));
        }

        $twigService = $controller->get('twig');
        $blocksPlaceholders = $this->getBlockNamesFromString($fileContent);
        foreach ($page->getBlocks() as $block) {
            $placeholder = $block->getPlaceholder();

            if (!isset($blocksPlaceholders[$placeholder])) {
                continue;
            }

            foreach ($block->getChunks() as $chunk) {
                $blocksPlaceholders[$placeholder] .= $controller->get('string_twig_loader')->render($twigService, $chunk->getContent());
            }
        }

        return $controller->render('DamediaSpecialProjectBundle:Templates:'.$twigFileName,
            array('PageTitle' => $title,
                  'Stylesheet' => $page->getStylesheet(),
                  'Javascript' => $page->getJavascript(),
                  'Breadcrumbs' => $breadcrumbs,
                  'Blocks' => $blocksPlaceholders,
                  'News' => $page->getNews()));
    }


    public function createInitialBreadcrumbsArray(Controller $controller) {
        $result = array();
        $result[] = $this->createBreadcrumbItem('/', 'Главная');
        $result[] = $this->createBreadcrumbItem($controller->generateUrl('damedia_special_project_list'), 'Спецпроекты');

        return $result;
    }

    public function createBreadcrumbItem($href, $caption, $selected = false) {
        return array('href' => (string)$href, 'caption' => (string)$caption, 'selected' => (boolean)$selected);
    }
}
?>