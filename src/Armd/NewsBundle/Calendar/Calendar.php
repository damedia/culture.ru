<?php

namespace Armd\NewsBundle\Calendar;

class Calendar
{
    protected $date;
    
    public function __construct(\DateTime $date)
    {
        $this->date = $date;
    }
        
    public function get()
    {
        $date = clone($this->date);
        
        return array(
            'years'     => array(
                'prev'      => $date->setTimestamp($this->date->getTimestamp())->sub(new \DateInterval('P1Y'))->format('Y'),
                'current'   => $this->date->format('Y'),
                'next'      => $date->setTimestamp($this->date->getTimestamp())->add(new \DateInterval('P1Y'))->format('Y'),
            ),
            'months'    => array(
                'prev'      => $date->setTimestamp($this->date->getTimestamp())->sub(new \DateInterval('P1M'))->format('m'),
                'current'   => $this->date->format('m'),
                'next'      => $date->setTimestamp($this->date->getTimestamp())->add(new \DateInterval('P1M'))->format('m'),
            ),
            'weeks'     => $this->getWeeks()
        );
    }
    
    function getWeeks()
    {
      // Вычисляем число дней в текущем месяце
      $dayofmonth = $this->date->format('t');
      // Счётчик для дней месяца
      $day_count = 1;
    
      // 1. Первая неделя
      $num = 0;
      for($i = 0; $i < 7; $i++)
      {
        // Вычисляем номер дня недели для числа
        $dayofweek = date('w', mktime(0, 0, 0, $this->date->format('m'), $day_count, $this->date->format('Y')));
        // Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
        $dayofweek = $dayofweek - 1;
        if($dayofweek == -1) $dayofweek = 6;
    
        if($dayofweek == $i)
        {
          // Если дни недели совпадают,
          // заполняем массив $week
          // числами месяца
            $week[$num][$i]['day'] = $day_count;
            $week[$num][$i]['isWeekend'] = $i > 4;
            $day_count++;
        }
        else
        {
            $week[$num][$i]['day'] = null;
            $week[$num][$i]['isWeekend'] = $i > 4;
        }
      }
    
      // 2. Последующие недели месяца
      while(true)
      {
        $num++;
        for($i = 0; $i < 7; $i++)
        {
            $week[$num][$i]['day'] = $day_count;
            $week[$num][$i]['isWeekend'] = $i > 4;
          $day_count++;
          // Если достигли конца месяца - выходим
          // из цикла
          if($day_count > $dayofmonth) break;
        }
        // Если достигли конца месяца - выходим
        // из цикла
        if($day_count > $dayofmonth) break;
      }
      
      return $week;        
    }
    
    protected function getDay($day)
    {
        return array(
            'day'       => $day,
            'isWeeend'  => $day > 4,
            'isToday'   => ($day == $this->date->format('d') && $this->date->format('m') == date('m')),
        );
    }
}