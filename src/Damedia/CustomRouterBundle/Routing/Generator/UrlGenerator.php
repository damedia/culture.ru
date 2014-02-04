<?php

namespace Damedia\CustomRouterBundle\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator {
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute) {
        if (isset($defaults['_url'])) {
            return $defaults['_url'];
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }
}