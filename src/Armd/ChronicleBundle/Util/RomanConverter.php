<?php

namespace Armd\ChronicleBundle\Util;

class RomanConverter
{
    protected static $romans = array(
        'M'     => 1000,
        'CM'    => 900,
        'D'     => 500,
        'CD'    => 400,
        'C'     => 100,
        'XC'    => 90,
        'L'     => 50,
        'XL'    => 40,
        'X'     => 10,
        'IX'    => 9,
        'V'     => 5,
        'IV'    => 4,
        'I'     => 1,        
    );
    
    protected static $numbers = array(
        0       => '0',
        1       => 'I',
        5       => 'V',
        10      => 'X',
        50      => 'L',
        100     => 'C',
        500     => 'D',
        1000    => 'M',
    );
    
    public static function toRoman($number)
    {
        $result = '';
        $n = intval($number);

        foreach (self::$romans as $roman => $num) {
            $matches = intval($n / $num);        
            $result .= str_repeat($roman, $matches);        
            $n = $n % $num;
        }

        return $result;
    }
    
    public static function toNumber($roman)
    {
        $result = $state = 0;
        $len = strlen($roman) - 1;

        while ($len >= 0) {
            
            foreach (self::$numbers as $number => $letter)
            {
                if (strtoupper($roman[$len]) != $letter) {
                    continue;
                }
                
                if ($state > $number) {
                    $result -= $number;
                } else {
                    $result += $number;
                    $state = $number;
                }                
            }
            
            $len--;
        }

        return($result);
    }
}
