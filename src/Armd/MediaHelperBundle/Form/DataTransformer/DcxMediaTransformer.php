<?php
namespace Armd\MediaHelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Armd\Application\Sonata\MediaBundle\Entity\Media;

class DcxMediaTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     *
     * @param  Media|null $media
     * @return Media|null
     */
    public function transform($media)
    {
        
        if (null === $media) {
            return null;
        }
        return array(
            'imageFile' => null,
            'dcxId' => null,
            'currentMediaId' => $media->getId(),
        );
    }

    /**
     *
     * @param  array $params
     *
     * @return Media|null
     *
     * @throws TransformationFailedException if object (params) is not found.
     */
    public function reverseTransform($params)
    {
        if (empty($params['currentMediaId'])) {
            return null;
        }

        $media = $this->om
            ->getRepository('ApplicationSonataMediaBundle:Media')
            ->findOneBy(array('id' => $params['currentMediaId']))
        ;
        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'An media with currentMediaId "%s" does not exist!',
                $params['currentMediaId']
            ));
        }
        if ($params['dcxId']){
            $media->setBinaryContent($params['dcxId']); 
            $media->setContext($params['context']);
            $media->setProviderName('sonata.media.provider.dcx');
            $this->om->persist($media);
        }

        if ($params['imageFile']) {
            $media->setBinaryContent($params['imageFile']); 
            $media->setContext($params['context']);
            $media->setProviderName('sonata.media.provider.image');
            $this->om->persist($media);
        }


        return $media;
    }
}

