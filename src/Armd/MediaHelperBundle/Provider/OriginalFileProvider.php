<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\MediaHelperBundle\Provider;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\FileProvider;

/**
 * Переопределение для сохранения реального имени файла (актуально для материалов для скачивания (stuff)).
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class OriginalFileProvider extends FileProvider
{
    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     *
     * @return string
     */
    protected function generateReferenceName(MediaInterface $media)
    {
        // Список контекстов, когда будет необходимость, можно будет вынести в конструктор.
        if ($media->getContext() == 'stuff') {
            return $media->getName();
        }

        return parent::generateReferenceName($media);
    }
}
