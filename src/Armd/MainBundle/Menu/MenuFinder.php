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
            $this->findByUri($child, $uri);
        }
        return false;
    }
}