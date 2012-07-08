<?php

namespace Armd\CommentBundle\Acl;

use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Acl\AclCommentManager as BaseAclCommentManager;
use FOS\CommentBundle\Model\CommentInterface;

/**
 * Wraps a real implementation of CommentManagerInterface and
 * performs Acl checks with the configured Comment Acl service.
 *
 * @author Tim Nagel <tim@nagel.com.au
 */
class AclCommentManager extends BaseAclCommentManager
{
    /**
     * The CommentManager instance to be wrapped with ACL.
     *
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * Constructor.
     *
     * @param CommentManagerInterface $commentManager The concrete CommentManager service
     * @param CommentAclInterface $commentAcl The Comment Acl service
     * @param ThreadAclInterface $threadAcl The Thread Acl service
     */
    public function __construct(CommentManagerInterface $commentManager, CommentAclInterface $commentAcl, ThreadAclInterface $threadAcl)
    {
        parent::__construct($commentManager, $commentAcl, $threadAcl);

        $this->commentManager = $commentManager;
    }

    /**
     * @param \FOS\CommentBundle\Model\CommentInterface $comment
     */
    public function updateComment(CommentInterface $comment)
    {
        var_dump($comment->getScore());
        $this->commentManager->saveComment($comment);
    }
}
