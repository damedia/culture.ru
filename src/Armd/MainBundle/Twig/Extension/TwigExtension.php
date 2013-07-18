<?php

namespace Armd\MainBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigExtension extends Twig_Extension
{

    public function getFunctions() {
        return array(

            'has_change_history' => new Twig_Function_Method($this, 'isChangeHistorySavable')

        );
    }

 
    public function isChangeHistorySavable($entity)
    {
        if ($entity instanceof \Armd\MainBundle\Model\ChangeHistorySavableInterface)
            return true;
            
        return false;
    } 
    
   /**
     * @return string
     */
    public function getName()
    {
        return 'armd_main_twig_extension';
    }
    
}