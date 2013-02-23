<?php

/**
 *  author  :   jeffstric
 *  email   :   jeffstricg@gmail.com
 *  date    :   2013-2-23
 *  time    :   23:26:43
 * */
class ImageProcessFileNameFormat
{

    private static $fileNamePregPattern = array(
	'%^(?<file>[\w_-]+)___s_(?<w>\d+)x(?<h>\d+)\.(?<type>jpg|jpeg|png|gif)$%' ,
	'%^(?<file>[\w_-]+)___s_(?<w>\d+)x(?<h>\d+)___p_(?<pw>\w+)\.(?<type>jpg|jpeg|png|gif)$%'
    );

    public static function getPattern()
    {
	return self::$fileNamePregPattern;
    }

    public static function getName( $originName , $width , $height , $pw )
    {
	$fileArr = explode('.' , $originName);
	$p = ($pw) ? '___p_' . $pw : '';
	$extent = array_pop($fileArr);
	return join('.' , $fileArr) . '___s_' . $width . 'x' . $height . $p . '.' . $extent;
    }

}