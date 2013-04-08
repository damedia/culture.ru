<?php

namespace Armd\MediaHelperBundle\Generator;

use Sonata\MediaBundle\Generator\DefaultGenerator;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * Переопределение для более уникального пути (актуально для файлов, которые сохраняются под реальными
 * именами (см. OriginalFileProvider)).
 *
 * P.S. Для пущей кошерности можно не наследовать, а сделать композицию потом.
 *
 * @author Alexey Shockov <alexey@shockov.com>
 */
class OriginalGenerator extends DefaultGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generatePath(MediaInterface $media)
    {
        $path = parent::generatePath($media);

        // Список контекстов, когда будет необходимость, можно будет вынести в конструктор.
        if ($media->getContext() == 'stuff') {
            $path .= '/'.$media->getCreatedAt()->getTimestamp();
        }

        return $path;
    }
}
