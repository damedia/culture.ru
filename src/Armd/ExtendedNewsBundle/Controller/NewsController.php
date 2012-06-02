<?php

namespace Armd\ExtendedNewsBundle\Controller;

use Armd\NewsBundle\Controller\NewsController as BaseController;

class NewsController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    function getListRepository()
    {
        return parent::getListRepository()
            ->addImage()
        ;
    }
}
