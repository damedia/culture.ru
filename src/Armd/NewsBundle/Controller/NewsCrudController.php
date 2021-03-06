<?php
namespace Armd\NewsBundle\Controller;

use Armd\AdminHelperBundle\Controller\BaseCrudController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NewsCrudController extends BaseCrudController
{
    public function batchActionShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function ($object) {
                $object->setShowOnMain(true);
            });
    }

    public function batchActionNotShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function ($object) {
                $object->setShowOnMain(false);
            });
    }
}