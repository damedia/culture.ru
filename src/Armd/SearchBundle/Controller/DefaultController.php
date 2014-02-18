<?php

namespace Armd\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\SearchBundle\Model\SearchEnum;

class DefaultController extends Controller {
    /**
     * @Route("/result/", name="armd_search_results")
     * @Template("ArmdSearchBundle:Default:searchResults.html.twig")
     */
    public function searchResultsAction() {
        $menu = $this->get('armd_main.menu.main');
        $router = $this->get('router');
        $menu->setCurrentUri($router->generate('armd_main_homepage'));

        $perPage = 50;
        $request = $this->getRequest();
        $page = $request->get('page', 1);
        $words = $request->get('search_query');

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
                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_LECTURE) {
                            $searchResult = $this->getLectureInfo($id - SearchEnum::START_INDEX_LECTURE);
                            if ($searchResult) {
                                $searchResultLecture[] = $searchResult;
                            }

                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_ATLAS) {
                            $searchResult = $this->getAtlasObjectInfo($id - SearchEnum::START_INDEX_ATLAS);
                            if ($searchResult) {
                                $searchResultAtlas[] = $searchResult;
                            }
                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_VIRTUAL_MUSEUM) {
                            $searchResult = $this->getVirtualMuseumInfo($id - SearchEnum::START_INDEX_VIRTUAL_MUSEUM);
                            if ($searchResult) {
                                $searchResultVirtualMuseum[] = $searchResult;
                            }
                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_LESSON) {
                            $searchResult = $this->getLessonInfo($id - SearchEnum::START_INDEX_LESSON);
                            if ($searchResult) {
                                $searchResultLesson[] = $searchResult;
                            }
                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_PERFOMANCE) {
                            $searchResult = $this->getPerfomanceInfo($id - SearchEnum::START_INDEX_PERFOMANCE);
                            if ($searchResult) {
                                $searchResultPerfomance[] = $searchResult;
                            }
                        }
                        elseif ($data['attrs']['object_type'] == SearchEnum::OBJECT_TYPE_THEATER) {
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

    protected function getAtlasObjectInfo($id) {
        $objectInfo = false;
        $em = $this->getDoctrine()->getManager();
        $atlasObjectsRepository = $em->getRepository('ArmdAtlasBundle:Object');

        $atlasObject = $atlasObjectsRepository->find($id);

        if (!empty($atlasObject)) {
            $objectInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_atlas_default_object_view', array('id' => $id)),
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
            }
            elseif (count($atlasObject->getImages()) > 0) {
                $images = $atlasObject->getImages();
                $image = $images[0];
            }
            else {
                $image = false;
            }

            if (!empty($image)) {
                $objectInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($image, 'atlas_searchAllResult');
            }
        }

        return $objectInfo;
    }

    protected function getLectureInfo($id) {
        $lectureInfo = false;
        $em = $this->getDoctrine()->getManager();
        $lecturesRepository = $em->getRepository('ArmdLectureBundle:Lecture');

        $lecture = $lecturesRepository->find($id);

        if (!empty($lecture)) {
            $lectureInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_lecture_view', array('id' => $id)),
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
            }
            elseif ($lecture->getTrailerVideo()) {
                $mediaImage = $lecture->getTrailerVideo()->getImageMedia();
            }
            elseif ($lecture->getMediaLectureVideo()) {
                $mediaImage = $lecture->getMediaLectureVideo();
            }
            elseif ($lecture->getMediaTrailerVideo()) {
                $mediaImage = $lecture->getMediaTrailerVideo();
            }

            $lectureInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($mediaImage, 'lecture_searchAllResult');
        }

        return $lectureInfo;
    }

    protected function getNewsInfo($id) {
        $articleInfo = false;
        $em = $this->getDoctrine()->getManager();
        $newsRepository = $em->getRepository('ArmdNewsBundle:News');

        $article = $newsRepository->find($id);

        if (!empty($article)) {
            $articleInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_news_item_by_category', array('category' => 'news', 'id' => $id)),
                    'date' => $article->getNewsDate(),
                    'title' => strip_tags($article->getTitle()),
                    'announce' => $article->getAnnounce()
                ),
                'section' => array(
                    'name' => 'Новости',
                )
            );

            if ($article->getImage()) {
                $articleInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($article->getImage(), 'news_searchAllResult');
            }
        }

        return $articleInfo;
    }


    protected function getVirtualMuseumInfo($id) {
        $museumInfo = false;
        $em = $this->getDoctrine()->getManager();
        $museumsRepository = $em->getRepository('ArmdMuseumBundle:Museum');

        $museum = $museumsRepository->find($id);

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
                $museumInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($museum->getImage(), 'museum_searchAllResult');
            }
        }

        return $museumInfo;
    }
    
    protected function getLessonInfo($id) {
        $articleInfo = false;
        $em = $this->getDoctrine()->getManager();
        $lessonsRepository = $em->getRepository('ArmdMuseumBundle:Lesson');

        $article = $lessonsRepository->find($id);

        if (!empty($article)) {
            $articleInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_lesson_item', array('id' => $id)),
                    'date' => null,
                    'title' => strip_tags($article->getTitle()),
                    'announce' => $article->getAnnounce()
                ),
                'section' => array(
                    'name' => 'Музейное образование',
                )
            );

            if ($article->getImage()) {
                $articleInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($article->getImage(), 'lesson_searchAllResult');
            }
        }

        return $articleInfo;
    }    

    protected function getPerfomanceInfo($id) {
        $performanceInfo = false;
        $em = $this->getDoctrine()->getManager();
        $performancesRepository = $em->getRepository('ArmdPerfomanceBundle:Perfomance');

        $performance = $performancesRepository->find($id);

        if (!empty($performance)) {
            $performanceInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_perfomance_item', array('id' => $id)),
                    'date' => $performance->getCreatedAt(),
                    'title' => strip_tags($performance->getTitle()),
                    'announce' => '',
                ),
                'section' => array(
                    'name' => 'Спектакль',
                )                
            );

            $mediaImage = false;

            if ($performance->getPerfomanceVideo()) {
                $mediaImage = $performance->getPerfomanceVideo()->getImageMedia();
            }
            elseif ($performance->getImage()) {
                $mediaImage = $performance->getImage();
            } 

            $performanceInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($mediaImage, 'perfomance_searchAllResult');
        }

        return $performanceInfo;
    }    
    
    protected function getTheaterInfo($id) {
        $theaterInfo = false;
        $em = $this->getDoctrine()->getManager();
        $theatersRepository = $em->getRepository('ArmdTheaterBundle:Theater');

        $theater = $theatersRepository->find($id);
        
        if (!empty($theater)) {
            $theaterInfo = array(
                'object' => array(
                    'url' => $this->get('router')->generate('armd_theater_item', array('id' => $id)),
                    'date' => $theater->getCreatedAt(),
                    'title' => strip_tags($theater->getTitle()),
                    'announce' => '',
                ),
                'section' => array(
                    'name' => 'Театр',
                )                
            );

            $mediaImage = false;
            
            if ($theater->getImage()) {
                $mediaImage = $theater->getImage();
            }

            $theaterInfo['object']['imageUrl'] = $this->get('sonata.media.provider.image')->generatePublicUrl($mediaImage, 'perfomance_searchAllResult');
        }

        return $theaterInfo;
    }
}