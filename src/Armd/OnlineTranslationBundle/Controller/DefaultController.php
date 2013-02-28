<?php

namespace Armd\OnlineTranslationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\Response;
use Gregwar\Captcha\CaptchaBuilder;

class DefaultController extends Controller
{  
    protected function getFormatDate(\DateTime $date)
    {
        $months = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $days = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
        
        return $date->format('j') . ' ' . $months[$date->format('n') - 1] . ', ' . $days[$date->format('N') - 1];
    }
    
    protected function getNotificationPeriods()
    {
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
    
    protected function getActiveTranslation()
    {
        $entities = $this->getDoctrine()
            ->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation')
            ->findBy(array('published' => 1), array('date' => 'DESC'), 1);
        
        if (isset($entities[0])) {
            $entity = $entities[0];           
        } else {
            $entity = false;
        }
        
        return $entity;
    }
    
    public function indexAction()
    {
        $params = array();      
        $entity = $this->getActiveTranslation();
        
        if ($entity) {        
            $params['date'] = $this->getFormatDate($entity->getDate());
            $params['notificationPeriods'] = $this->getNotificationPeriods();
        }

        $archiveTranslations = $this->get('armd_lecture.manager.lecture')->findObjects(array(
                LectureManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC'),
                LectureManager::CRITERIA_LIMIT => 12,
                LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array('LECTURE_SUPER_TYPE_VIDEO_TRANSLATION')
            ));
                  
        return $this->render('ArmdOnlineTranslationBundle:Default:index.html.twig',
            array(
                'entity' => $entity,
                'params' => $params,
                'archiveTranslations' => $archiveTranslations
            )
        );
    }

    public function homepageWidgetAction()
    {
        $params = array();      
        $entity = $this->getActiveTranslation();
        
        if (!$entity) {
            return new Response();
        }
        
        $params['date'] = $this->getFormatDate($entity->getDate());
        $params['notificationPeriods'] = $this->getNotificationPeriods();
        
        return $this->render('ArmdOnlineTranslationBundle:Default:homepage_widget.html.twig',
            array(
                'entity' => $entity,
                'params' => $params
            )
        );
    }
    
    public function setNotificationAction()
    {              
        try {
            $request = $this->getRequest();
            $email = trim($request->request->get('email', ''));
            $period = trim($request->request->get('period', ''));
            $id = trim($request->request->get('id', ''));
            $captcha = trim($request->request->get('captcha', ''));
            
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
            
            $already = $this->getDoctrine()
                ->getRepository('ArmdOnlineTranslationBundle:Notification')
                ->findBy(array(
                    'onlineTranslation' => $id,
                    'email' => $email,
                    'period' => $period
                ));
            
            if ($already) {
                return new Response('ok');
            }
            
            $entity = new \Armd\OnlineTranslationBundle\Entity\Notification();
            $entity->setOnlineTranslation(
                $this->getDoctrine()
                    ->getEntityManager()
                    ->find('\Armd\OnlineTranslationBundle\Entity\OnlineTranslation', $id)
            );
            $entity->setEmail($email);
            $entity->setPeriod($period);
            $this->getDoctrine()->getEntityManager()->persist($entity);
            $this->getDoctrine()->getEntityManager()->flush();
            
            return new Response('ok');
            
        } catch (\Exception $e) {
            
        }
        
        return new Response('error');
    }
    
    public function getCaptchaAction()
    {
        $builder = new CaptchaBuilder;
        $builder->setDistortion(false);
        $builder->build();
        $this->getRequest()->getSession()->set('online-translation-captcha', $builder->getPhrase());
        
        return new Response($builder->get(), 200, array('Content-type' => 'image/jpeg'));
    }
}
