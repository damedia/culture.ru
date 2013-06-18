<?php
namespace Armd\MkCommentBundle\Model;

interface NoticeInterface
{
    const T_NONE   = 0;
    const T_REPLY  = 1;
    const T_THREAD = 2;
    const T_ALL    = 3;
}