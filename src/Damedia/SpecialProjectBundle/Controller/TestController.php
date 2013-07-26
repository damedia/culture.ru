<?php 

namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Templating\Helper\MediaHelper;

class TestController extends Controller
{
	public function getImagesJsonAction() {
	
		$mediaManager = $this->container->get('sonata.media.manager.media');
		$request = $this->get('request');
		$result=$mediaManager->findBy(array('context'=>'news', 'providerName'=>'sonata.media.provider.image'));
		$json=array();
		$i=0;
		foreach($result as $media) {
			$json[]=array('id'=>$media->getId(), 'label'=>$this->path($media->getId(), 'thumbnail'));
			$i++;
			if ($i>10)
				break;
		};
		print_r($json);
		// return json_encode($json);
		// return $this->renderJson($result);
		/*
		 *
		*  $media = new Media();
		$mediaFile = new UploadedFile($image, $image);
		$media->setBinaryContent($mediaFile);
		$media->setContext('paper_archive');
		$media->setTitle($title);
		$media->setProviderName('sonata.media.provider.image');
		$mediaManager = $this->container->get('sonata.media.manager.media');
		$mediaManager->save($media);
		return $media;
		*/
	
	}
	public function path($id, $format) {
		return $this->getTtwigMediaExtension()->path($id, $format);
	}
	
	/**
	 * @return TwigExtension
	 */
	protected function getTtwigMediaExtension()
	{
		$twigMediaExtension = $this->container->get('sonata.media.twig.extension');
		$twigMediaExtension->initRuntime($this->get('twig'));
	
		return $twigMediaExtension;
	}
	
	
}