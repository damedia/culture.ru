<?php
namespace Damedia\SpecialProjectBundle\Service;

use Symfony\Component\HttpKernel\Kernel;

class SpecialProjectHelper {
    public function getTwigTemplatesPath(\AppKernel $kernel) {
        return $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');
    }

    public function getBlockNamesFromString($string) {
        $result = array();

        preg_match_all('/{{\sBlocks\.(\w+)\|?(\w+)?\s}}/im', $string, $matches);

        if (isset($matches[1])) {
            $result = $matches[1];
        }

        return $result;
    }

    /*
    public function createBlockTextareaHtml($blockName, $content = '') {
        $blockName = (string)$blockName;

        if ($blockName == '') {
            return '';
        }

        return '<div class="createPage_blockTitle">'.$blockName.'</div>
                <textarea class="createPage_blockTextarea" name="'.$blockName.'">'.$content.'</textarea>';
    }
    */
}
?>