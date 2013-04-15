<?php

namespace Armd\ExternalSearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SearchController extends Controller
{
    /**
     * @Route("/", name="armd_external_search_results")
     * @Template
     */
    public function resultListAction()
    {
        $yandexSearchId = $this->container->get('armd_external_search.search_engine.yandex')->getSearchId();

        return array(
            'yandexSearchId' => $yandexSearchId,
        );
    }
}
