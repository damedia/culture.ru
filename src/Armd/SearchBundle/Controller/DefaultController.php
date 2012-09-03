<?php

namespace Armd\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    const OBJECT_TYPE_NEWS = 1;
    const OBJECT_TYPE_LECTURE = 2;
    const OBJECT_TYPE_ATLAS = 3;

    /**
     * @Route("/test")
     * @Template()
     */
    public function testAction()
    {
        $search = $this->container->get('search.sphinxsearch.search');
        $res = $search->search('Организатор', array('News' => array()));

    }

//    /**
//     * @Route("/result/{searchQuery}/page/{page}", requirements={"page" = "\d+"}, name="armd_search_results")
//     * @Template()
//     */
    /**
     * @Route("/result/", name="armd_search_results")
     * @Template()
     */
    public function searchResultsAction() //$searchQuery, $page
    {
        $router = $this->container->get('router');
        $perPage = 20;

        $page = $this->getRequest()->get('page', 1);
        $words = $this->getRequest()->get('search_query');

        $searchResults = array();
        if (!empty($words)) {

            $search = $this->container->get('search.sphinxsearch.search');
            $searchParams = array(
                'All' => array(
                    'result_offset' => ($page - 1) * $perPage,
                    'result_limit' => $perPage,
                    'sort_mode' => '@relevance DESC, @weight DESC, date_from DESC'
                )
            );
            $res = $search->search($words, $searchParams);

            if (!empty($res['All']['matches'])) {
                $newsRepo = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News');
                $lectureRepo = $this->getDoctrine()->getManager()->getRepository('ArmdLectureBundle:Lecture');
                $atlasObjectRepo = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object');
                $imageProvider = $this->get('sonata.media.provider.image');

                foreach ($res['All']['matches'] as $id => $data) {
                    if (isset($data['attrs']['object_type'])) {

                        if ($data['attrs']['object_type'] == self::OBJECT_TYPE_NEWS) {
                            $article = $newsRepo->find($id);
                            if (!empty($article)) {
                                $searchResult = array(
                                    'object' => array(
                                        'url' => $router->generate('armd_news_item_by_category',
                                            array('category' => 'news', 'id' => $id)),
                                        'date' => $article->getDate(),
                                        'title' => $article->getTitle(),
                                        'announce' => $article->getAnnounce()
                                    ),
                                    'section' => array(
                                        'name' => 'Новости',
                                    )
                                );

                                if ($article->getImage()) {
                                    $searchResult['object']['imageUrl'] = $imageProvider->generatePublicUrl($article->getImage(), 'news_list');
                                }

                                $searchResults[] = $searchResult;
                            }

                        }
                        elseif ($data['attrs']['object_type']  == self::OBJECT_TYPE_LECTURE) {

                            $lecture = $lectureRepo->find($id);
                            if (!empty($lecture)) {
                                $searchResult = array(
                                    'object' => array(
                                        'url' => $router->generate('armd_lecture_view',
                                            array('id' => $id)),
                                        'date' => $lecture->getCreatedAt(),
                                        'title' => $lecture->getTitle(),
                                        'announce' => ''
                                    ),
                                    'section' => array(
                                        'name' => $lecture->getLectureSuperType()->getCode() == 'LECTURE_SUPER_TYPE_LECTURE' ? 'Лекции' : 'Трансляции',
                                    )
                                );

                                if ($lecture->getLectureVideo()) {
                                    $searchResult['object']['imageUrl'] = $imageProvider->generatePublicUrl($lecture->getLectureVideo()->getImageMedia(), 'news_list');
                                }

                                $searchResults[] = $searchResult;
                            }

                        }
                        elseif ($data['attrs']['object_type'] == self::OBJECT_TYPE_ATLAS) {

                            $atlasObject = $atlasObjectRepo->find($id);
                            if (!empty($atlasObject)) {
                                $searchResult = array(
                                    'object' => array(
                                        'url' => $router->generate('armd_atlas_default_object_view',
                                            array('id' => $id)),
                                        'date' => null,
                                        'title' => $atlasObject->getTitle(),
                                        'announce' => $atlasObject->getAnnounce()
                                    ),
                                    'section' => array(
                                        'name' => 'Атлас',
                                    )
                                );

                                if ($atlasObject->getPrimaryImage()) {
                                    $searchResult['object']['imageUrl'] = $imageProvider->generatePublicUrl($atlasObject->getPrimaryImage(), 'news_list');
                                }

                                $searchResults[] = $searchResult;
                            }
                        }
                    }
                }

            }
        }

        // use $pagination only to display page navigation bar because data is already cut
        $paginator = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate($searchResults, $page, $perPage);
        $pagination->setTotalItemCount($res['All']['total']);

        return array(
            'searchResults' => $searchResults,
            'searchQuery' => $words,
            'pagination' => $pagination
        );
    }
}
