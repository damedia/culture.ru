<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\MkCommentBundle\Model;

use Armd\MkCommentBundle\Entity\Thread;

interface CommentableInterface
{
    /**
     * Set thread
     *
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return
     */
    public function setThread(Thread $thread = null);

    /**
     * Get thread
     *
     * @return \FOS\CommentBundle\Model\ThreadInterface
     */
    public function getThread();
}