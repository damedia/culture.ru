<?php

namespace Armd\ExtendedNewsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArmdExtendedNewsBundle extends Bundle
{
    public function getParent()
    {
        return 'ArmdNewsBundle';
    }
}
