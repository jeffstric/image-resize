<?php

/**
 *  author  :   jeffstric
 *  email   :   jeffstricg@gmail.com
 *  date    :   2013-2-22
 *  time    :   20:43:32
 **/
$a = 'filename___s_10x10___p_f1fp7.jpg';

preg_match('%^(?<filename>[\w_-]+)___s_(\d+)x(\d+)___p_(\w+)\.(jpg|jpeg|png|gif)$%', $a,$matches);

var_dump($matches);