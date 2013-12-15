<?php
namespace Armd\DCXBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class SearchType extends AbstractType
{

    public function buildForm( FormBuilderInterface $builder, array $options ) {
        $builder->add( 'Search','search')
        ;
    }

    function getName() {
        return 'SearchType';
    }
}