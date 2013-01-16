<?php

namespace Armd\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\SearchBundle\Model\SearchEnum;

class DefaultController extends Controller
{

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
        $perPage = 20;
        $page = $this->getRequest()->get('page', 1);
        $words = $this->getRequest()->get('search_query');

        $searchResults = array();
        $pagination = false;
        if (!empty($words)) {

            $search = $this->container->get('search.sphinxsearch.search');
            $searchParams = array(
                'All' => array(
                    'result_offset' => ($page - 1) * $perPage,
                    'result_limit' => $perPage,
                    'sort_mode' => '@relevance DESC, @weight DESC, date_from DESC',
                    'filters' => array(
                        array(
                            'attribute' => 'published',
                            'values' => array(1)
                        )
                    )

                )
            );
            $res = $search->search($words, $searchParams);
            if (!empty($res['All']['matches'])) {
                foreach ($res['All']['matches'] as $id => $data) {
                    if (isset($data['attrs']['object_type'])) {
                        $searchResult = false;

                        if ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_NEWS) {
                            $searchResult = $this->getNewsInfo($id - SearchEnum::START_INDEX_NEWS);
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_LECTURE) {
                            $searchResult = $this->getLectureInfo($id - SearchEnum::START_INDEX_LECTURE);

                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_ATLAS) {
                            $searchResult = $this->getAtlasObjectInfo($id - SearchEnum::START_INDEX_ATLAS);
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_VIRTUAL_MUSEUM) {
                            $searchResult = $this->getVirtualMuseumInfo($id - SearchEnum::START_INDEX_VIRTUAL_MUSEUM);
                        }

                        if (!empty($searchResult)) {
                            $searchResults[] = $searchResult;
                        }
                    }
                }
                // use $pagination only to display page navigation bar because data is already cut
                $paginator = $this->container->get('knp_paginator');
                $pagination = $paginator->paginate($searchResults, $page, $perPage);
                $pagination->setTotalItemCount($res['All']['total']);
            }
        }

        return array(
            'searchResults' => $searchResults,
            'searchQuery' => $words,
            'pagination' => $pagination
        );
    }

    protected function getAtlasObjectInfo($id)
    {
        $atlasObject = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')->find($id);
        $objectInfo = false;
        if (!empty($atlasObject)) {
            $objectInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_atlas_default_object_view',
                        array('id' => $id)
                    ),
                    'date' => null,
                    'title' => strip_tags($atlasObject->getTitle()),
                    'announce' => $atlasObject->getAnnounce()
                ),
                'section' => array(
                    'name' => 'Атлас',
                )
            );

            if ($atlasObject->getPrimaryImage()) {
                $image = $atlasObject->getPrimaryImage();
            } elseif (count($atlasObject->getImages()) > 0) {
                $images = $atlasObject->getImages();
                $image = $images[0];
            } else {
                $image = false;
            }

            if (!empty($image)) {
                $objectInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                    $image,
                    'atlas_searchAllResult'
                );
            }
        }

        return $objectInfo;
    }

    protected function getLectureInfo($id)
    {
        $lecture = $this->getDoctrine()->getManager()->getRepository('ArmdLectureBundle:Lecture')->find($id);
        $lectureInfo = false;
        if (!empty($lecture)) {
            $lectureInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_lecture_view',
                        array('id' => $id)
                    ),
                    'date' => $lecture->getCreatedAt(),
                    'title' => strip_tags($lecture->getTitle()),
                    'announce' => ''
                ),
                'section' => array(
                    'name' => $lecture->getLectureSuperType()->getCode(
                    ) == 'LECTURE_SUPER_TYPE_LECTURE' ? 'Лекции' : 'Трансляции',
                )
            );

            if ($lecture->getLectureVideo()) {
                $lectureInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                    $lecture->getLectureVideo()->getImageMedia(),
                    'lecture_searchAllResult'
                );
            }
        }

        return $lectureInfo;
    }

    protected function getNewsInfo($id)
    {
        $article = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->find($id);
        $articleInfo = false;
        if (!empty($article)) {
            $articleInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_news_item_by_category',
                        array('category' => 'news', 'id' => $id)
                    ),
                    'date' => $article->getDate(),
                    'title' => strip_tags($article->getTitle()),
                    'announce' => $article->getAnnounce()
                ),
                'section' => array(
                    'name' => 'Новости',
                )
            );

            if ($article->getImage()) {
                $articleInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                    $article->getImage(),
                    'news_searchAllResult'
                );
            }
        }

        return $articleInfo;
    }


    protected function getVirtualMuseumInfo($id)
    {
        $museum = $this->getDoctrine()->getManager()->getRepository('ArmdMuseumBundle:Museum')->find($id);
        $museumInfo = false;
        if (!empty($museum)) {
            $museumInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_news_item_by_category',
                        array('category' => 'news', 'id' => $id)
                    ),
                    'date' => null,
                    'title' => strip_tags($museum->getTitle()),
                    'announce' => '',
                    'imageUrl' => false
                ),
                'section' => array(
                    'name' => 'Виртуальные музеи',
                )
            );

            if ($museum->getImage()) {
                $museumInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                    $museum->getImage(),
                    'museum_searchAllResult'
                );
            }
        }

        return $museumInfo;
    }
}
