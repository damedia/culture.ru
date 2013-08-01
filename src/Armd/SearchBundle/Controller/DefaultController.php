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
        $menu = $this->get('armd_main.menu.main');
        $menu->setCurrentUri(
            $this->get('router')->generate('armd_main_homepage')
        );

        $perPage = 50;
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
                )
            );
            $res = $search->search($words, $searchParams);

            if (!empty($res['All']['matches'])) {
                $searchResultNews = array();
                $searchResultLecture = array();
                $searchResultAtlas = array();
                $searchResultVirtualMuseum = array();
                $searchResultLesson = array();
                $searchResultPerfomance = array();
                $searchResultTheater = array();

                foreach ($res['All']['matches'] as $id => $data) {
                    if (isset($data['attrs']['object_type'])) {
                        if ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_NEWS) {
                            $searchResult = $this->getNewsInfo($id - SearchEnum::START_INDEX_NEWS);
                            if ($searchResult) {
                                $searchResultNews[] = $searchResult;
                            }
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_LECTURE) {
                            $searchResult = $this->getLectureInfo($id - SearchEnum::START_INDEX_LECTURE);
                            if ($searchResult) {
                                $searchResultLecture[] = $searchResult;
                            }

                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_ATLAS) {
                            $searchResult = $this->getAtlasObjectInfo($id - SearchEnum::START_INDEX_ATLAS);
                            if ($searchResult) {
                                $searchResultAtlas[] = $searchResult;
                            }
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_VIRTUAL_MUSEUM) {
                            $searchResult = $this->getVirtualMuseumInfo($id - SearchEnum::START_INDEX_VIRTUAL_MUSEUM);
                            if ($searchResult) {
                                $searchResultVirtualMuseum[] = $searchResult;
                            }
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_LESSON) {
                            $searchResult = $this->getLessonInfo($id - SearchEnum::START_INDEX_LESSON);
                            if ($searchResult) {
                                $searchResultLesson[] = $searchResult;
                            }
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_PERFOMANCE) {
                            $searchResult = $this->getPerfomanceInfo($id - SearchEnum::START_INDEX_PERFOMANCE);
                            if ($searchResult) {
                                $searchResultPerfomance[] = $searchResult;
                            }
                        } elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_THEATER) {
                            $searchResult = $this->getTheaterInfo($id - SearchEnum::START_INDEX_THEATER);
                            if ($searchResult) {
                                $searchResultTheater[] = $searchResult;
                            }
                        }
                    }
                }

                $searchResults = array_merge(
                    $searchResultAtlas,
                    $searchResultVirtualMuseum,
                    $searchResultLecture,
                    $searchResultNews,
                    $searchResultLesson,
                    $searchResultPerfomance,
                    $searchResultTheater
                );
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
                    'name' => $lecture->getLectureSuperType()->getName()
                )
            );

            $mediaImage = false;
            if ($lecture->getLectureVideo()) {
                $mediaImage = $lecture->getLectureVideo()->getImageMedia();
            } elseif ($lecture->getTrailerVideo()) {
                $mediaImage = $lecture->getTrailerVideo()->getImageMedia();
            } elseif ($lecture->getMediaLectureVideo()) {
                $mediaImage = $lecture->getMediaLectureVideo();
            } elseif ($lecture->getMediaTrailerVideo()) {
                $mediaImage = $lecture->getMediaTrailerVideo();
            }

            $lectureInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                $mediaImage,
                'lecture_searchAllResult'
            );
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
                    'date' => $article->getNewsDate(),
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
                    'url' => $museum->getUrl(),
                    'date' => null,
                    'title' => strip_tags($museum->getTitle()),
                    'announce' => '',
                    'imageUrl' => false,
                    'target_blank' => true
                ),
                'section' => array(
                    'name' => 'Виртуальные туры',
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
    
    protected function getLessonInfo($id)
    {
        $article = $this->getDoctrine()->getManager()->getRepository('ArmdMuseumBundle:Lesson')->find($id);
        $articleInfo = false;
        if (!empty($article)) {
            $articleInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_lesson_item',
                        array('id' => $id)
                    ),
                    'date' => null,
                    'title' => strip_tags($article->getTitle()),
                    'announce' => $article->getAnnounce()
                ),
                'section' => array(
                    'name' => 'Музейное образование',
                )
            );

            if ($article->getImage()) {
                $articleInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                    $article->getImage(),
                    'lesson_searchAllResult'
                );
            }
        }

        return $articleInfo;
    }    

    protected function getPerfomanceInfo($id)
    {
        $perfomance = $this->getDoctrine()->getManager()->getRepository('ArmdPerfomanceBundle:Perfomance')->find($id);
        $perfomanceInfo = false;
        if (!empty($perfomance)) {
            $perfomanceInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_perfomance_item',
                        array('id' => $id)
                    ),
                    'date' => $perfomance->getCreatedAt(),
                    'title' => strip_tags($perfomance->getTitle()),
                    'announce' => '',
                ),
                'section' => array(
                    'name' => 'Спектакль',
                )                
            );

            $mediaImage = false;
            if ($perfomance->getPerfomanceVideo()) {
                $mediaImage = $perfomance->getPerfomanceVideo()->getImageMedia();
            } elseif ($perfomance->getImage()) {
                $mediaImage = $perfomance->getImage();
            } 

            $perfomanceInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                $mediaImage,
                'perfomance_searchAllResult'
            );
        }

        return $perfomanceInfo;
    }    
    
    protected function getTheaterInfo($id)
    {
        $theater = $this->getDoctrine()->getManager()->getRepository('ArmdTheaterBundle:Theater')->find($id);
        $theaterInfo = false;
        
        if (!empty($theater)) {
            $theaterInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate(
                        'armd_theater_item',
                        array('id' => $id)
                    ),
                    'date' => $theater->getCreatedAt(),
                    'title' => strip_tags($theater->getTitle()),
                    'announce' => '',
                ),
                'section' => array(
                    'name' => 'Театр',
                )                
            );

            $mediaImage = false;
            
            if ($perfomance->getImage()) {
                $mediaImage = $theater->getImage();
            } 

            $perfomanceInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl(
                $mediaImage,
                'perfomance_searchAllResult'
            );
        }

        return $theaterInfo;
    }
}
