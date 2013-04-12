<?php

namespace Armd\ExternalSearchBundle\SearchEngine;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class Yandex
{
    /**
     * @var int
     */
    private $searchId;

    public function __construct($searchId)
    {
        $this->searchId = $searchId;
    }

    /**
     * @return int
     */
    public function getSearchId()
    {
        return $this->searchId;
    }
}
