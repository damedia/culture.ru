<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\TvigleVideoBundle\Extension;
use Twig_Extension;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;
use Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TvigleVideoTwigExtension extends Twig_Extension
{
    protected $ruMonthes = array(1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    protected $ruWeekDays = array(0 => 'воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

//    public function getFilters()
//    {
//        return array(
//            'localDate' => new Twig_Filter_Method($this, 'localDateFilter'),
//        );
//    }

    public function getFunctions()
    {
        return array(
            'armd_tvigle_video_player' => new \Twig_Function_Method($this, 'videoPlayerFunction')
        );
    }

//    public function localDateFilter($datetime, $format)
//    {
//        $ruMonth = $this->ruMonthes[$datetime->format('n')];
//        $format = str_replace('F', $ruMonth, $format);
//        $ruWeekDay = $this->ruWeekDays[$datetime->format('w')];
//        $format = str_replace('l', $ruWeekDay, $format);
//        $dateStr = date($format, $datetime->getTimestamp());
//        return $dateStr;
//    }

    public function videoPlayerFunction(TvigleVideo $video, $width, $height)
    {
        if($video->getSwf()) {
            $html = '
                <object id="v991e13289f9da885507781e166b83740"
                    classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                    codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"
                    width="'. $width . '" height="' . $height . '"
                    align="middle">
                    <param name="allowFullScreen" value="true"></param>
                    <param name="allowscriptaccess" value="always"></param>
                    <param name="movie" value="' . $video->getSwf() . '"></param>
                    <embed src="' . $video->getSwf() . '"
                        width="' . $width . '" height="' . $height . '"
                        allowfullscreen="true" allowscriptaccess="always"
                        type="application/x-shockwave-flash"
                        pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>
            ';
        } else {
            $html = 'No swf';
        }
        return $html;
    }


    public function getName()
    {
        return 'armd_tvigle_video_twig_extension';
    }
}
