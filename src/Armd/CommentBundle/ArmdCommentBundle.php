<?php

namespace Armd\CommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArmdCommentBundle extends Bundle
{
    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     *
     * @api
     */
    public function getParent()
    {
        return 'FOSCommentBundle';
    }
}
