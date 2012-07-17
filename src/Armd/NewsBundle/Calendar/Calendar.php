<?php

namespace Armd\NewsBundle\Calendar;

class Calendar
{
    protected $date;
    
    public function __construct(\DateTime $date)
    {
        $this->date = $date;
    }
    
    public function getNextMonth()
    {
        return $this->date;
    }
    
    public function getPrevMonth()
    {
        return $this->date;
    }
    
    
    static function get(\DateTime $date)
    {
      // Вычисляем число дней в текущем месяце
      $dayofmonth = date('t', $date->getTimestamp());
      // Счётчик для дней месяца
      $day_count = 1;
    
      // 1. Первая неделя
      $num = 0;
      for($i = 0; $i < 7; $i++)
      {
        // Вычисляем номер дня недели для числа
        $dayofweek = date('w',
                          mktime(0, 0, 0, date('m', $date->getTimestamp()), $day_count, date('Y', $date->getTimestamp())));
        // Приводим к числа к формату 1 - понедельник, ..., 6 - суббота
        $dayofweek = $dayofweek - 1;
        if($dayofweek == -1) $dayofweek = 6;
    
        if($dayofweek == $i)
        {
          // Если дни недели совпадают,
          // заполняем массив $week
          // числами месяца
            $week[$num][$i]['day'] = $day_count;
            $week[$num][$i]['isWeekend'] = $day_count > 4;
          $day_count++;
        }
        else
        {
            $week[$num][$i]['day'] = null;
            $week[$num][$i]['isWeekend'] = $day_count > 4;
        }
      }
    
      // 2. Последующие недели месяца
      while(true)
      {
        $num++;
        for($i = 0; $i < 7; $i++)
        {
            $week[$num][$i]['day'] = $day_count;
            $week[$num][$i]['isWeekend'] = $day_count > 4;
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
}