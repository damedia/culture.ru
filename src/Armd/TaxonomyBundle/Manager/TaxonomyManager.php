<?php

namespace Armd\TaxonomyBundle\Manager;

use Doctrine\ORM\EntityManager;
use Armd\TaxonomyBundle\Entity\Tag;
use Armd\TaxonomyBundle\Entity\TagContentReference;

class TaxonomyManager
{
    protected $em;
        
    function createTags(EntityManager $em, $entity)
    {
        $this->em = $em;    
        
        foreach(explode(',', $entity->getTags()) as $name)
        {
            if ($name) {
                $this->createReference($this->getTagByName($name), $entity);
            }            
        }
        
        foreach(explode(',', $entity->getPersonalTag()) as $name)
        {
            if ($name) {
                $this->createReference($this->getTagByName($entity->getPersonalTag()), $entity, true);
            }            
        }        

        $this->em->flush();        
    }
    
    function deleteTags(EntityManager $em, $entity)
    {
        $this->em = $em;    
    
        $this->deleteReferences($entity);    
    }
    
    function updateTags(EntityManager $em, $entity)
    {   
        $this->deleteTags($em, $entity);        
        $this->createTags($em, $entity);
    }    
    
    function getTagByName($name)
    {
        $entity = $this->em->getRepository('ArmdTaxonomyBundle:Tag')->findOneByName($this->getCanonicalName($name));
        
        return (null !== $entity) ? $entity : $this->createTag($name);
    }
    
    function createTag($name)
    {
        $entity = new Tag();
        $entity->setName($this->getCanonicalName($name));
        
        $this->em->persist($entity);
        
        return $entity;
    }
    
    function createReference(Tag $tag, $content, $is_personal = false)
    {
        $entity = new TagContentReference();
        $entity->setTag($tag);
        $entity->setContent($content->getContent());
        $entity->setPersonal($is_personal);        
        
        $this->em->persist($entity);
        
        return $entity;    
    }
    
    function deleteReferences($content)
    {
        return $this->em->createQuery('delete from ArmdTaxonomyBundle:TagContentReference r where r.content = :content')
            ->setParameter('content', $content->getContent())
            ->execute();
    }
    
    function getCanonicalName($name) 
    { 
        $lower = array('ё','й','ц','у','к','е','н','г', 'ш','щ','з','х','ъ','ф','ы','в', 'а','п','р','о','л','д','ж','э', 'я','ч','с','м','и','т','ь','б','ю'); 
        $upper = array('Ё','Й','Ц','У','К','Е','Н','Г', 'Ш','Щ','З','Х','Ъ','Ф','Ы','В', 'А','П','Р','О','Л','Д','Ж','Э', 'Я','Ч','С','М','И','Т','Ь','Б','Ю'); 
    
        return str_replace($upper, $lower, strtolower(trim($name))); 
    }         
}