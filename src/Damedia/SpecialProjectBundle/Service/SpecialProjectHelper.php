<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Damedia\SpecialProjectBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpecialProjectHelper {
    public function getTwigTemplatesPath(\AppKernel $kernel) {
        return $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');
    }

    public function getBlockNamesFromString($string) { // rename this method!!!
        $result = array();

        preg_match_all('/{{\sBlocks\.(\w+)\|?(\w+)?\s}}/im', $string, $matches);

        if (!isset($matches[1])) {
        	return $result;
        }
        
        // Can't use array_flip() here because I want to set empty string values
        foreach ($matches[1] as $placeholder) {
        	$result[$placeholder] = '';
        }

        return $result;
    }
    
    public function collectPageBreadcrumbs(Controller $controller, Page $page, &$result = array()) { //duplication when adding array element
    	if (count($result) == 0) {
    		$result[] = array('href' => '/', 'caption' => 'Главная', 'selected' => false);
    		$result[] = array('href' => $controller->generateUrl('damedia_special_project_list'), 'caption' => 'Спецпроекты', 'selected' => false);
    	}
    	
    	$parentPage = $page->getParent();
    	
    	if ($parentPage) {
    		$this->collectPageBreadcrumbs($controller, $parentPage, $result);
    		$result[] = array('href' => $controller->generateUrl('damedia_special_project_view', array('slug' => $parentPage->getSlug())), 'caption' => $parentPage->getTitle(), 'selected' => false);
    	}
    	
    	return;
    }
}
?>