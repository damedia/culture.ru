<?php

namespace Armd\MkCommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArmdMkCommentBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSCommentBundle';
    }
}
