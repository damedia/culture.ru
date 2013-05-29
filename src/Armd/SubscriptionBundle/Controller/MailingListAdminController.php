<?php
namespace Armd\SubscriptionBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController;

class MailingListAdminController extends CRUDController
{
    public function toggleEnabledAction($id)
    {
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $object->setEnabled(!$object->isEnabled());
        $this->admin->update($object);
        $this->get('session')->setFlash('sonata_flash_success', $this->admin->trans(
                                                                    $object->isEnabled()?'Mailing List was enabled':'Mailing list was disabled', 
                                                                    array(),
                                                                    'messages'
                                                                )
                               );

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
