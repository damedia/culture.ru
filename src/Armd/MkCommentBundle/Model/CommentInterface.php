<?php

namespace Armd\MkCommentBundle\Model;

use FOS\CommentBundle\Model\CommentInterface as BaseInterface;

interface CommentInterface extends BaseInterface
{
    const STATE_PROCESSING = 4;
    
    const SECTION_LECTURE     = 'lecture';
    const SECTION_LESSON      = 'lesson';
    const SECTION_ROUTE       = 'route';
    const SECTION_THEATER     = 'theater';
    const SECTION_PERFORMANCE = 'performance';
    const SECTION_NEWS        = 'news';
}
