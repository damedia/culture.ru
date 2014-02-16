<?php

namespace Armd\OnlineTranslationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\Response;
use Gregwar\Captcha\CaptchaBuilder;
use Armd\OnlineTranslationBundle\Entity\Notification;

class DefaultController extends Controller {
    protected function getFormatDate(\DateTime $date) {
        $months = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $days = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
        
        return $date->format('j') . ' ' . $months[$date->format('n') - 1] . ', ' . $days[$date->format('N') - 1];
    }
    
    protected function getNotificationPeriods() {
        return array(
            4320 => '3 дня',
            1440 => '1 день',
            360 => '6 часов',
            180 => '3 часа',
            60 => '1 час',
            30 => '30 мин',
            10 => '10 мин'
        );
    }

    /**
     * @Route("/", name="armd_online_translation", options={"expose"=true})
     * @Template("ArmdOnlineTranslationBundle:Default:index.html.twig")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $broadcastsRepository = $em->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation');

        $entity = $broadcastsRepository->findOneBy(array('published' => 1));

        /*
        $archiveTranslations = $this->get('armd_lecture.manager.lecture')->findObjects(array(
            LectureManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC'),
            LectureManager::CRITERIA_LIMIT => 12,
            LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array('LECTURE_SUPER_TYPE_VIDEO_TRANSLATION')
        ));
        */

        return array(
            'entity' => $entity
        );
    }

    /**
     * @Template("ArmdOnlineTranslationBundle:Default:mainpageWidget.html.twig")
     */
    public function mainpageWidgetAction() {
        $em = $this->getDoctrine()->getManager();
        $broadcastsRepository = $em->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation');

        $entity = $broadcastsRepository->findOneBy(array('published' => 1));
        
        if (!$entity) {
            return new Response();
        }

        return array(
            'entity' => $entity
        );
    }

    /**
     * @Route("/set-notification", name="armd_online_translation_set_notification", options={"expose"=true})
     */
    public function setNotificationAction() {
        try {
            $request = $this->getRequest();
            $email = trim($request->request->get('email', ''));
            $period = trim($request->request->get('period', ''));
            $id = trim($request->request->get('id', ''));
            $captcha = trim($request->request->get('captcha', ''));

            $doctrine = $this->getDoctrine();
            $notificationRepository = $doctrine->getRepository('ArmdOnlineTranslationBundle:Notification');
            
            if (!$id || !$captcha) {
                return new Response('invalid_parameters');
            }
            
            if ($captcha != $this->getRequest()->getSession()->get('online-translation-captcha')) {
                return new Response('invalid_captcha');
            }
            
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new Response('invalid_email');
            }

            $notificationPeriods = $this->getNotificationPeriods();
            if (!$period || !isset($notificationPeriods[$period])) {
                return new Response('invalid_period');
            }
            
            $already = $notificationRepository->findBy(array(
                'onlineTranslation' => $id,
                'email' => $email,
                'period' => $period
            ));
            
            if ($already) {
                return new Response('ok');
            }
            
            $entity = new Notification();
            $em = $doctrine->getManager();
            $onlineTranslation = $em->find('\Armd\OnlineTranslationBundle\Entity\OnlineTranslation', $id);
            $entity->setOnlineTranslation($onlineTranslation);
            $entity->setEmail($email);
            $entity->setPeriod($period);
            $em->persist($entity);
            $em->flush();
            
            return new Response('ok');
        }
        catch (\Exception $e) {
            return new Response('error');
        }
    }

    /**
     * @Route("/captcha", name="armd_online_translation_captcha", options={"expose"=true})
     */
    public function getCaptchaAction() {
        $builder = new CaptchaBuilder;
        $builder->setDistortion(false);
        $builder->build();
        $this->getRequest()->getSession()->set('online-translation-captcha', $builder->getPhrase());
        
        return new Response($builder->get(), 200, array('Content-type' => 'image/jpeg'));
    }
}
