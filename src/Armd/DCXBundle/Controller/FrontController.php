<?php
/*
 * This is controller using for test dcx
 */

namespace Armd\DCXBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
 
use Armd\DCXBundle\Form\SearchType;

use JMS\Serializer\SerializerBuilder;

class FrontController extends Controller
{
    /**
    * @Route( "/test", name="ajax_search")
    * @Template()
    */
    public function indexAction()
    {
        //need for set public info after download file
        // $dcx = $this->get('dcx.client');
        // $dcx->sendPublicate('doc69f38hw2ayay5czozv', date(DATE_W3C));
        
        // $dcx = $this->get('dcx.client');
        // $res = $dcx->getDoc('doc6czek64lvexoqoaeqhn');
        // $res->getNeedImage('original','Образы России');
        // var_dump($res->story_documents);
        // die();
        $request = $this->getRequest();
        $form = $this->createForm( new SearchType() );
        if ( $request->isMethod( 'POST' ) ) {
            $form->bind( $request );
            if ( $form->isValid() ) {
                $data = $form->getData();
                if (isset($data['Search'])){
                    $dcx = $this->get('dcx.client');
                    $res = $dcx->getDoc($data['Search']);
                    $is_correct = is_object($res) ? $res->checkFileName('original','Образы России') : $res;
                    $serializer = SerializerBuilder::create()->build();
                    // if($is_correct !== false && !is_string($is_correct)){
                        $data = $serializer->serialize($res, 'json');
                    // }
                    // else{
                    //     $is_correct = ($is_correct === false) ? 'Изображение с заданным DC ID не имеет правильного варианта для выгрузки' : $is_correct;
                    //     $data = $serializer->serialize(array('info' => $is_correct), 'json');
                    // }
                    $headers = array('Content-Type' => 'aplication/json');
                }
            }
            return new Response($data, 200, $headers);
        }
        return array('search_form' => $form->createView());
    }

    /**
    * @Route( "/test/get_image/{subpath}.{type}",
    * name="ajax_get_image",
    * requirements={"subpath"=".*", "type"="flv|mp4|jpg|png|gif|mpeg"})
    */
    public function getFileAction($subpath,$type)
    {   
        $dcx = $this->get('dcx.client');
        $file= $dcx->getFile($subpath.'.'.$type);
        switch ($type) {
            case 'flv':
                $content_type = 'video/flv';
                break;
            case 'mp4':
                $content_type = 'video/mp4';
                break;
            case 'mpeg':
                $content_type = 'video/mpeg';
                break;
            case 'gif':
                $content_type = 'image/gif';
                break;
            case 'png':
                $content_type = 'image/png';
                break;
            case 'jpg':
                $content_type = 'image/jpg';
                break;
        }
        $headers = array(
            'Content-Type' => $content_type,
            'Content-Disposition' => 'inline;filename="file69auv5npbva5ppdrzw.jpg"'
        );
        return new Response($file, 200, $headers);
    }
}
