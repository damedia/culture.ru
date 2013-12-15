<?php
namespace Damedia\SpecialProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class NewsSelectType extends AbstractType {
    private $pageId;

    public function __construct($pageId) {
        $this->pageId = $pageId;
    }

    public function getParent() {
        return 'entity';
    }

    public function getName() {
        return 'special_project_news_select';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $pageId = $this->pageId;

        $resolver->setDefaults(array(
            'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
            'multiple' => true,
            'class' => 'Armd\NewsBundle\Entity\News',
            'query_builder' => function(EntityRepository $er) use ($pageId) {
                return $er->createQueryBuilder('g')
                          ->innerJoin('g.projects', 'p')
                          ->where('p.id = :id')
                          ->setParameter('id', $pageId);
            }));
    }
}
?>