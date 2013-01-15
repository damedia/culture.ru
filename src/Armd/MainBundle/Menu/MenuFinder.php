<?php
namespace Armd\MainBundle\Menu;

use Knp\Menu\MenuItem;

class MenuFinder
{
    public function findByUri(MenuItem $menu, $uri)
    {
        foreach($menu->getChildren() as $child) {
            /** @var MenuItem $child */
            if($child->getUri() === $uri) {
                return $child;
            }
            $childResult = $this->findByUri($child, $uri);
            if ($childResult) {
                return $childResult;
            }
        }
        return false;
    }
}