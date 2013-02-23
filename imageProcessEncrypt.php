<?php

/**
 *  author  :   jeffstric
 *  email   :   jeffstricg@gmail.com
 *  date    :   2013-2-22
 *  time    :   19:50:18
 * */
class ImageProcessEncrypt
{

    static $salt = 'jeffLoveTiny';

    static public function encrypt( $str )
    {
	return substr(self::encryptCumstom($str . self::$salt) , 0 , 8);
    }

    static public function checkPW( $pw , $str )
    {
	return $pw == self::encrypt($str);
    }

    static private function encryptCumstom( $str )
    {
	return md5($str);
    }

}

