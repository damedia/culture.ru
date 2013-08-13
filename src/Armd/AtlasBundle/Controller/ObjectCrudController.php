<?php
namespace Armd\AtlasBundle\Controller;

use Armd\AdminHelperBundle\Controller\BaseCrudController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ObjectCrudController extends BaseCrudController
{
    public function batchActionPublish(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function ($object) {
                $object->setPublished(true);
            });
    }

    public function batchActionUnpublish(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setPublished(false);
            });
    }

    public function batchActionShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMainAsRecommended(true);
            });
    }

    public function batchActionNotShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMainAsRecommended(false);
            });
    }

    public function batchActionShowOnMainAsNovel(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMainAsNovel(true);
            });
    }

    public function batchActionNotShowOnMainAsNovel(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMainAsNovel(false);
            });
    }

}