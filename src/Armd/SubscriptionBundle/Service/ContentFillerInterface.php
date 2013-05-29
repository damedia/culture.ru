<?php

namespace Armd\SubscriptionBundle\Service;

use Armd\SubscriptionBundle\Entity\Issue;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
interface ContentFillerInterface
{
    function getContentFor(Issue $issue);
}
