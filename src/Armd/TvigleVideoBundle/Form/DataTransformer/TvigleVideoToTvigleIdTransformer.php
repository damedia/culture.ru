<?php
namespace Armd\TvigleVideoBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;


class TvigleVideoToTvigleIdTransformer implements DataTransformerInterface
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
     * Transforms an object (video) to a string (tvigleId).
     *
     * @param  TvigleVideo|null $video
     * @return string
     */
    public function transform($video)
    {
        if (null === $video) {
            return "";
        }

        return $video->getTvigleId();
    }

    /**
     * Transforms a string (tvigleId) to an object (video).
     *
     * @param  string $tvigleId
     * @return TvigleVideo|null
     */
    public function reverseTransform($tvigleId)
    {
        if (!$tvigleId) {
            return null;
        }

        $video = $this->om
            ->getRepository('ArmdTvigleVideoBundle:TvigleVideo')
            ->findOneBy(array('tvigleId' => $tvigleId))
        ;

        if (null === $video) {
            $video = new TvigleVideo();
            $video->setTvigleId($tvigleId);
        }

        return $video;
    }
}
{

}