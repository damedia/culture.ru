<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Kernel;

class SpecialProjectHelper {
    public function getTwigTemplatesPath(\AppKernel $kernel) {
        return $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');
    }

    public function getBlockNamesFromString($string) { // rename this method!!!
        $result = array();

        preg_match_all('/{{\sBlocks\.(\w+)\|?(\w+)?\s}}/im', $string, $matches);

        if (!isset($matches[1])) {
        	return $result;
        }
        
        // Can't use array_flip() here because I want to set empty string values
        foreach ($matches[1] as $placeholder) {
        	$result[$placeholder] = '';
        }

        return $result;
    }
}
?>