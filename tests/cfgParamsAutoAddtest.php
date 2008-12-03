<?php

/**
 * this test suite is created for debugging mechanis of autoadding parameters to html-elements
 */

require ('jevix.class.php');
// This test text does not consist any russian symbols for easily debugging
$text = '<a href="http://ya.ru" rel="search">Yandex</a><img src="/test.jpg">';
$jevix = new Jevix();
$jevix->cfgSetXHTMLMode(false);
$jevix->cfgAllowTags(array('a', 'img'));
$jevix->cfgSetTagShort('img');
$jevix->cfgAllowTagParams('a', array('href', 'rel'));
$jevix->cfgAllowTagParams('img', array('src', 'alt', 'title'));
$jevix->cfgSetTagParamsRequired('a', 'href');
$jevix->cfgSetTagParamsRequired('img', 'src');
$jevix->cfgSetTagParamsAutoAdd('a', array('rel' => 'nofollow'));
$jevix->cfgSetTagParamsAutoAdd('img', array('alt' => ''));

$errors = array();
echo $jevix->parse($text, $errors);
?>