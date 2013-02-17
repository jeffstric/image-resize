<?php

$DEFAULT_IMAGE = 'sites/image/system/default.png';
$ERROR_IMAGE = 'sites/image/system/error.jpg';

//default
/*
  $illegalSize = array(
  //EXAMPLE: array(10,20)  mean width 10px, height 20px
  //NOTE: if the size is not match , first array will  be the default size
  array( 50 , 50 ) ,
  array( 100 , 100 ) ,
  array( 200 , 200 ) ,
  array( 400 , 400 ) ,
  );
 */
//advance
$illegalSizeAdv = array(
    'commonPath' => 'sites/image/' ,
    'illegalSize' => array(
	'group1' => array(
	    array( 50 , 50 ) ,
	    array( 100 , 100 ) ,
	) ,
	'group2' => array(
	    array( 200 , 200 ) ,
	    array( 400 , 400 ) ,
	) ,
    ) ,
    'illegalSizeDefault' => array(
	array( 50 , 50 ) ,
	array( 100 , 100 ) ,
	array( 200 , 200 ) ,
	array( 400 , 400 ) ,
    ) ,
);