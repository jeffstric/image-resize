<?php

/**
 * author:jeffstric
 * email:jeffstricg@gmail.com
 * blog:jeffsc.info
 * datetime:2012-6-27, 20:28:03
 * */
$filePrefix = substr(__FILE__ , 0 , strlen(__FILE__) - 4);
$resizeIniFile = $filePrefix . '_inc.php';
$fileFormat = $filePrefix . '_fileNameFormat.php';
require $resizeIniFile;
require $fileFormat;


if ( isset($encryptFile) && file_exists($encryptFile) ) {
    //require encryptFile
    require dirname(__FILE__) . DIRECTORY_SEPARATOR . $encryptFile;
}
if ( class_exists('ImageProcessEncrypt') ) {
    $encryptMode = TRUE;
} else {
    $encryptMode = FALSE;
}

define('IMAGES_IMPROVE_DIR_NAME' , 'imagesi');
define('IMG_DIR_RELATIVE' , '');
define('IMG_DIR_ABSOLUTE' , dirname(__FILE__));
define('DEFAULT_IMAGE' , IMG_DIR_ABSOLUTE . DIRECTORY_SEPARATOR . $DEFAULT_IMAGE);
define('ERROR_IMAGE' , IMG_DIR_ABSOLUTE . DIRECTORY_SEPARATOR . $ERROR_IMAGE);

class imageProcess
{

    private static $path = IMG_DIR_ABSOLUTE;
    private static $c1 = 255; // int 0-255 Red
    private static $c2 = 255; // int 0-255 Green
    private static $c3 = 255; // int 0-255 Red
    private static $imagetype = array(
	1 => 'gif' ,
	2 => 'jpeg' ,
	3 => 'png' ,
    );
    private static $legalType = array( 1 , 2 , 3 );

    public static function reseizeImage( $imageFrom , $widthTo = 100 , $heightTo = 100 , $pw = FALSE )
    {

	$path = self::$path;
	$target = $path . DIRECTORY_SEPARATOR . $imageFrom;

	if ( !file_exists($target) ) {
	    // use defualt image replace imageFrom
	    $target = ImageProcessFileNameFormat::getName(DEFAULT_IMAGE , $widthTo , $heightTo , $pw);

	    if ( file_exists($target) ) {
		header("Content-type:image/png");
		echo file_get_contents($target);
		exit(0);
	    } else {
		$target = DEFAULT_IMAGE;
	    }
	}

	$outputFile = ImageProcessFileNameFormat::getName($target , $widthTo , $heightTo , $pw);

	list($width , $height , $type) = getimagesize($target);

	$imFrom = false;

	$imageCreateFromFunName = 'imagecreatefrom' . self::$imagetype[ $type ];
	if ( in_array($type , self::$legalType) )
	    $imFrom = @$imageCreateFromFunName($target);

	if ( $imFrom ) {
	    $imTo = imagecreatetruecolor($widthTo , $heightTo);
	    $color = imagecolorallocate($imTo , self::$c1 , self::$c2 , self::$c3);
	    imagefill($imTo , 0 , 0 , $color);

	    $whichRadio = false;
	    $radio = self::getRadio($width , $height , $widthTo , $heightTo , $whichRadio);

	    $xTo = 0;
	    $yTo = 0;

	    //judge whether the image need reseize
	    if ( $radio ) {
		if ( $whichRadio == 'h' ) {
		    $widthWant = round($width * $radio);
		    $xTo = ceil(($widthTo - $widthWant ) / 2);

		    $widthTo = $widthWant;
		} else {
		    $heightWant = round($height * $radio);
		    $yTo = ceil(($heightTo - $heightWant ) / 2);

		    $heightTo = $heightWant;
		}
	    } else {

		$xTo = round(($widthTo - $width ) / 2);
		$yTo = round(($heightTo - $height ) / 2);

		$widthTo = $width;
		$heightTo = $height;
	    }

	    $copyResult = imagecopyresampled($imTo , $imFrom , $xTo , $yTo , 0 , 0 , $widthTo , $heightTo , $width , $height);

	    if ( $copyResult ) {
		$imageType = array( 1 => 'gif' , 2 => 'jpg' , 3 => 'png' );

		header("Content-type:image/" . $imageType[ $type ]);
		$imageFunName = 'image' . self::$imagetype[ $type ];

		if ( in_array($type , self::$legalType) ) {
		    $imageFunName($imTo);
		    if ( !file_exists($outputFile) ) {
			$imageFunName($imTo , $outputFile);
		    }
		    $imageFunName($imTo);
		}

		imagedestroy($imFrom);
		imagedestroy($imTo);
	    }
	}
    }

    /**
     * 
     * @param type $widthFrom
     * @param type $heightFrom
     * @param type $widthTo
     * @param type $heightTo
     * @param 'w'  'h' or  false $whichRadio  'w'  mean compress pictrure by width radio  ,
     * 	    the same to 'h' (height), false mean we needn't  compress the picture
     * @return int or false   false mean we needn't  compress the picture
     */
    private static function getRadio( $widthFrom , $heightFrom , $widthTo , $heightTo , &$whichRadio = '' )
    {
	$radioWidth = $widthTo / $widthFrom;
	$radioHeigt = $heightTo / $heightFrom;

	$radio = false;
	$whichRadio = false;

	//judge whether the target picture is smaller than source picture  ,if so ,we need to compress;
	if ( ($heightFrom > $heightTo) || ($widthFrom > $widthTo) ) {

	    if ( $radioWidth < $radioHeigt ) {
		$radio = $radioWidth;
		$whichRadio = 'w';
	    } else {
		$radio = $radioHeigt;
		$whichRadio = 'h';
	    }
	}

	return $radio;
    }

}

$scriptUrl = isset($_SERVER[ 'SCRIPT_URL' ]) ? $_SERVER[ 'SCRIPT_URL' ] : $_SERVER[ 'REQUEST_URI' ];
if ( isset($_SERVER[ 'SCRIPT_URL' ]) ) {
    $scriptUrl = $_SERVER[ 'SCRIPT_URL' ];
} else {
    $pos = strrpos($_SERVER[ 'REQUEST_URI' ] , '?');
    if ( $pos === FALSE ) {
	$scriptUrl = $_SERVER[ 'REQUEST_URI' ];
    } else {
	$scriptUrl = substr($_SERVER[ 'REQUEST_URI' ] , 0 , $pos);
    }
}

$lastPos = strrpos($scriptUrl , '/');
$imagePath = substr($scriptUrl , 0 , $lastPos);
// remove IMAGES_IMPROVE_DIR_NAME
$imagePath = substr($imagePath , strlen(IMAGES_IMPROVE_DIR_NAME) + 2);

try {
//jude path is allow 
    if ( strpos($imagePath , $allowResizePath) === 0 ) {
	$imageFileName = substr($scriptUrl , $lastPos + 1);
	$pregPattern = ImageProcessFileNameFormat::getPattern();

	$pattern = ($encryptMode) ? $pregPattern[ 1 ] : $pregPattern[ 0 ];

	if ( preg_match($pattern , $imageFileName , $match) ) {

	    $fileName = $match[ 'file' ] . '.' . $match[ 'type' ];
	    $widthTo = $match[ 'w' ];
	    $heightTo = $match[ 'h' ];
	    $pw = (isset($match[ 'pw' ])) ? $match[ 'pw' ] : FALSE;

	    if ( $encryptMode ) {
		$checkStr = $fileName . $widthTo . $heightTo;
		if ( !ImageProcessEncrypt::checkPW($pw , $checkStr) ) {
		    throw new Exception('pw is illegal');
		}
	    }

	    if ( !isset($illegalSize) || !is_array($illegalSize) ) {
		if ( isset($illegalSizeAdv) && is_array($illegalSizeAdv) && isset($illegalSizeAdv[ 'commonPath' ]) && isset($illegalSizeAdv[ 'illegalSizeDefault' ]) && isset($illegalSizeAdv[ 'illegalSize' ]) ) {
		    $hasGroupSize = FALSE;
		    foreach ( $illegalSizeAdv[ 'illegalSize' ] as $key => $value ) {
			$groupPath = $illegalSizeAdv[ 'commonPath' ] . $key;
			if ( strpos($imagePath , $groupPath) === 0 ) {
			    $illegalSize = $illegalSizeAdv[ 'illegalSize' ][ $key ];
			    $hasGroupSize = TRUE;
			    break;
			}
		    }
		    if ( !$hasGroupSize )
			$illegalSize = $illegalSizeAdv[ 'illegalSizeDefault' ];
		} else {
		    throw new Exception('$illegalSize in config file is missing or illgal ');
		}
	    }

	    if ( !in_array(array( $widthTo , $heightTo ) , $illegalSize) ) {
		list($widthTo , $heightTo) = array_shift($illegalSize);
	    }

	    $imageFrom = $imagePath . DIRECTORY_SEPARATOR . $fileName;

	    imageProcess::reseizeImage($imageFrom , $widthTo , $heightTo , $pw);
	} else {
	    throw new Exception('the file format is illegal');
	}
    } else {
	throw new Exception('path is not allow to image resize');
    }
} catch ( Exception $e ) {
    $debugModePw = isset($debugModePw) ? $debugModePw : 'debug';
    if ( $_GET[ 'debug' ] == $debugModePw ) {
	echo $e->getMessage();
	die();
    }

    header("Content-type:image/jpg");
    echo file_get_contents(ERROR_IMAGE);
}
