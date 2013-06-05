<?php

namespace Armd\UserBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Armd\UserBundle\Entity\ViewedContent;
use Sonata\UserBundle\Model\UserInterface;

class ViewedContentExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'add_viewed_content' => new \Twig_Function_Method($this, 'addViewedContent')
        );
    }

    /**
     * Add viewed content
     * @param Entity $entity
     * @param string $url
     * @param string $title
     */
    public function addViewedContent($entity, $url, $title = null)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if (is_object($user) and $user instanceof UserInterface) {
            $entityClassParts = array_diff(
                explode('\\', get_class($entity)),
                array('')
            );
            $entityClass = $entityClassParts[0] .$entityClassParts[1] .':' .$entityClassParts[count($entityClassParts) - 1];
            
            $viewedContent = $this->getEntityRepository()->findOneBy(array(
                'user'        => $user,
                'entityId'    => $entity->getId(),
                'entityClass' => $entityClass
            ));

            if (!$viewedContent) {
                $viewedContent = new ViewedContent();
                $viewedContent
                    ->setUser($user)
                    ->setEntityId($entity->getId())
                    ->setEntityClass($entityClass)
                ;
            }

            $viewedContent
                ->setDate(new \DateTime())
                ->setEntityTitle($title ?: (string) $entity)
                ->setEntityUrl($url)
            ;
            
            $em = $this->getEntityManager();
            $em->persist($viewedContent);
            $em->flush();
        }
    }

    /**
     * Get entity manager
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container
            ->get('doctrine')
                ->getEntityManager();
    }

    /**
     * Get entity repository
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->container
            ->get('doctrine')
                ->getRepository('ArmdUserBundle:ViewedContent');
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    function getName()
    {
        return 'armd_user_viewed_content_extension';
    }
}