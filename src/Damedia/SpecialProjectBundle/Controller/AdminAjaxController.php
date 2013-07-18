<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CoreController;

class AdminAjaxController extends CoreController {
    public function getTemplateBlocksFormAction() {
        $response = array('content' => '');
        $request = $this->get('request');
        $templateId = $request->request->get('templateId');

        if ($templateId != '') {
            $template = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Template')->find($templateId);
            $twigFileName = $template->getTwigFileName();

            $helper = $this->container->get('special_project_helper');
            $twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
            $fileContent = file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);
            $blocksArray = $helper->getBlockNamesFromString($fileContent);

            $formBuilder = $this->createFormBuilder();
            foreach ($blocksArray as $blockName) {
                $formBuilder->add($blockName, 'textarea',
                                  array('required' => false,
                                        'attr' => array('class' => 'createPage_blockTextarea')));
            }
            $form = $formBuilder->getForm();

            $response['content'] = $this->renderView('DamediaSpecialProjectBundle:Admin:pageAdmin_ajax_templateBlocksForm.html.twig',
                                                     array('twigFileName' => $twigFileName,
                                                           'form' => $form->createView()));
        }
        else {
            $response['content'] = 'Template ID has not been sent!';
        }

        return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }
}
?>