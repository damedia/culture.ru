<?php
namespace Damedia\SpecialProjectBundle\Service;

class StringTwigLoader {
	public function render(\Twig_Environment $twigService, $string, array $parameters = array()) {
		$twig = clone $twigService;
		$loader = new \Twig_Loader_String();
		$twig->setLoader($loader);
		
		return $twig->render($string, $parameters);
	}
}
?>