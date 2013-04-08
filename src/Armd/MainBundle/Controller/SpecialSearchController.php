<?php

namespace Armd\MainBundle\Controller;

use Armd\SearchBundle\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SpecialSearchController extends DefaultController {
	
    /**
     * @Template("ArmdMainBundle:Special:search_results.html.twig")
     */
    public function searchResultsAction() //$searchQuery, $page
    {
    	return parent::searchResultsAction();
    }
    
    protected function getAtlasObjectInfo($id)
    {
    	
    	$objectInfo = parent::getAtlasObjectInfo($id);
    	$objectInfo['object']['url'] = $this->get('router')->generate(
                      					  'armd_main_special_russian_images_item',
                        					array('id' => $id)
                    					);
    	
    	return $objectInfo;
    }
    
	protected function getNewsInfo($id)  {
		
    	$objectInfo = parent::getNewsInfo($id);
    	$objectInfo['object']['url'] = $this->get('router')->generate(
                        					'armd_main_special_news_item_by_category',
                        					array('category' => 'news', 'id' => $id)
                        				);
    	
    	return $objectInfo;		
	}
}