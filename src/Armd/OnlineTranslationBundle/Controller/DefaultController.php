<?php

namespace Armd\OnlineTranslationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{  
    protected function getFormatDate(\DateTime $date)
    {
        $months = array('Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря');
        $days = array('понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', 'воскресенье');
        
        return $date->format('j') . ' ' . $months[$date->format('n') - 1] . ', ' . $days[$date->format('N') - 1];
    }
   
    protected function getCountdown(\DateTime $date)
    {
        $now = new \DateTime();
        
        if ($date > $now) {
            $diff = $date->diff(new \DateTime());
            
            if (in_array(intval(mb_substr($diff->d, -1)), array(0, 5, 6, 7, 8, 9))
                || in_array(intval(mb_substr($diff->d, -2)), array(11, 12, 13, 14, 15, 16, 17, 18, 19))
            ) {
                $dStr = 'дней';
            } elseif (intval(mb_substr($diff->d, -1)) == 1) {
                $dStr = 'день';
            } else {
                $dStr = 'дня';
            }
        
            return $diff->d . ' ' . $dStr . ' ' . $diff->h . ' час ' . $diff->i . ' мин';
        } else {
            return '0 дней 0 час 0 мин';
        }
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
            $params['countdown'] = $this->getCountdown($entity->getDate());
            $params['notificationPeriods'] = $this->getNotificationPeriods();
        } else {
            return new Response();
        }
                   
        return $this->render('ArmdOnlineTranslationBundle:Default:index.html.twig',
            array(
                'entity' => $entity,
                'params' => $params
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
        $params['countdown'] = $this->getCountdown($entity->getDate());
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
            
            if (!$id) {
                return new Response('invalid parameters');
            }
            
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new Response('invalid email');
            }

            if (!$period || !isset($this->getNotificationPeriods()[$period])) {
                return new Response('invalid period');
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
}
