<?php

namespace Armd\SubscriptionBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware;

use Armd\SubscriptionBundle\Entity\MailingList;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class ContentFillerFactory extends ContainerAware
{
    /**
     * @param \Armd\SubscriptionBundle\Entity\MailingList $mailingList
     *
     * @return \Armd\SubscriptionBundle\Service\ContentFillerInterface[]
     */
    public function getContentFillersFor(MailingList $mailingList)
    {
        if ($mailingList->getType() == MailingList::TYPE_NEW_NEWS) {
            return array(
                $this->container->get('armd_subscription.content_filler.new_news'),
            );
        } elseif ($mailingList->getType() == MailingList::TYPE_NEW_CONTENT) {
            return array(
                $this->container->get('armd_subscription.content_filler.new_news'),
                $this->container->get('armd_subscription.content_filler.new_lectures'),
                $this->container->get('armd_subscription.content_filler.new_atlas_objects'),
            );
        } else {
            return array();
        }
    }
}
