<?php

namespace Armd\TaxonomyBundle\Model;

interface TaxonomyInterface
{
    function getPersonalTag();
    
    function setPersonalTag($personalTag);

    function getTags();
    
    function setTags($tags);
}
