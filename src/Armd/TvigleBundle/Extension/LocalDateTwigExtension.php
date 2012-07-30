<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\TvigleBundle\Extension;
use Twig_Extension;
use Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocalDateTwigExtension extends Twig_Extension
{
    protected $ruMonthes = array(1=>'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    protected $ruWeekDays = array(0=>'воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');

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

    public function getFilters()
    {
        return array(
            'localDate' => new Twig_Filter_Method($this, 'localDateFilter'),
        );
    }

    public function localDateFilter($datetime, $format)
    {
        $ruMonth = $this->ruMonthes[$datetime->format('n')];
        $format = str_replace('F', $ruMonth, $format);
        $ruWeekDay = $this->ruWeekDays[$datetime->format('w')];
        $format = str_replace('l', $ruWeekDay, $format);
        $dateStr = date($format, $datetime->getTimestamp());
        return $dateStr;
    }

    public function getName()
    {
        return 'armd_cms_rudate_twig_extension';
    }
}
