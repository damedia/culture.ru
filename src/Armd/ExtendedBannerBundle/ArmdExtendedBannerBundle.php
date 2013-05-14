<?php

namespace Armd\ExtendedBannerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArmdExtendedBannerBundle extends Bundle
{
    public function getParent()
    {
        return 'ArmdBannerBundle';
    }
}
