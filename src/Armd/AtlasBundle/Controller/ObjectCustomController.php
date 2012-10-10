<?php
namespace Armd\AtlasBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ObjectCustomController extends CRUDController
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST'))
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $objectRepo = $em->getRepository('ArmdAtlasBundle:Object');
        $statusRepo = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
        $userRepo   = $em->getRepository('ArmdUserBundle:User');
        $filter = $this->getRequest()->get('filter');
        if (! $filter)
            $filter = array(
                'title' => '',
                'status' => '',
                'createdBy' => '',
                'updatedBy' => '',
            );

        $objects = $objectRepo->filterModerating($filter);

        $statusList = $statusRepo->findBy(array(), array('id'=>'ASC'));
        $userList = $userRepo->findBy(array(), array('username'=>'ASC'));

        return $this->render('ArmdAtlasBundle:Admin:objectcustom-list.html.twig', array(
            'action'   => 'list',
            'objects'  => $objects,
            'userList' => $userList,
            'statusList' => $statusList,
            'filter' => $filter,
        ));
    }

    public function showAction($id = null)
    {
        if (false === $this->admin->isGranted('SHOW'))
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getManager();
        $objectRepo = $em->getRepository('ArmdAtlasBundle:Object');
        $statusRepo = $em->getRepository('ArmdAtlasBundle:ObjectStatus');

        $object = $objectRepo->find($id);
        if (! $object)
            throw new NotFoundHttpException(sprintf('Unable to find the object with id : %s', $id));

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $statusId = (int) $request->get('status');
            $reason = trim($request->get('reason'));

            $statusEntity = $statusRepo->find($statusId);
            if (! $statusEntity)
                throw new NotFoundHttpException(sprintf('Unable to find the object status with id : %s', $statusId));

            $object->setStatus($statusEntity);

            if ($statusId==2 || $statusId==3) {
                $object->setReason($reason);
            } else {
                $object->setReason(''); // очищаем причину модерации
            }

            try {
                $em->persist($object);
                $em->flush();
            } catch (\Exception $e) {
                print $e->getMessage();
            }

            if ($statusId==2 || $statusId==3) {
                $emailFrom = 'no-reply@mk.local.armd.ru';
                $emailTo = $object->getCreatedBy()->getEmail();
                if ($statusId == 2) {
                    $subject = 'Заявка одобрена';
                    $template = 'ArmdAtlasBundle:Admin:emailObjectStatusApprove.txt.twig';
                } elseif ($statusId == 3) {
                    $subject = 'Заявка отклонена';
                    $template = 'ArmdAtlasBundle:Admin:emailObjectStatusRefuse.txt.twig';
                }
                $body = $this->renderView($template, array(
                    'username' => $object->getCreatedBy()->getUsername(),
                    'reason' => $reason,
                ));
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($emailFrom)
                    ->setTo($emailTo)
                    ->setBody($body)
                ;
                $this->get('mailer')->send($message);
            }

            $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');
            return new RedirectResponse($this->admin->generateObjectUrl('show', $object));
        }

        $statusList = $statusRepo->findAll();

        return $this->render('ArmdAtlasBundle:Admin:objectcustom-show.html.twig', array(
            'action'   => 'show',
            'object'   => $object,
            'statusList' => $statusList,
        ));
    }

}