<?php
/**
 * Тестовый пример работы Jevix-а
 *
 * За помощь в написании примера спасибо Александру (http://vaart.habrahabr.ru, indrid@mail.ru)
 */

require('../src/functions.php');
require('../src/Jevix.php');

$jevix = new Jevix();
$jevix->cfgSetAllowedProtocols(array('', 'http', 'https'), false, '#domain');
$jevix->cfgAllowTags(array('iframe'));
$jevix->cfgSetTagIsEmpty(array('iframe'));
// 2. Устанавливаем коротие теги. (не имеющие закрывающего тега)
$jevix->cfgAllowTagParams('iframe',
	array(
		'src' => array(
			'#domain' => array(
				'youtube.com',
				'test.com' => 'embed/[\w\d\-_]+$',
                'example.com' => '[\w\d\-_]+$'
			)
		),
		'width' => '#int',
		'height' => '#int'
	)
);
// 9. Устанавливаем автозамену
$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)'), array('±', '©', '®'));

// 10. Включаем или выключаем режим XHTML. (по умолчанию включен)
$jevix->cfgSetXHTMLMode(true);

// 11. Включаем или выключаем режим замены переноса строк на тег <br/>. (по умолчанию включен)
$jevix->cfgSetAutoBrMode(true);

// 12. Включаем или выключаем режим автоматического определения ссылок. (по умолчанию включен)
$jevix->cfgSetAutoLinkMode(true);
//Парсинг

// Исходный текст
$text = '<iframe src="https://youtube.com/embed/4fMLHBiQlKM" width="100" height="100"></iframe>
<iframe src="https://test.com/embed/4fMLHBiQlKM" width="100" height="100"></iframe>
<iframe src="https://example.com/embed/4fMLHBiQlKM" width="100" height="100"></iframe>
<iframe src="https://example.org/embed/4fMLHBiQlKM" width="100" height="100"></iframe>
';

// Переменная, в которую будут записыватся ошибки
$errors = null;

// Парсим
$res = $jevix->parse($text, $errors);
print "результат: \n-------------\n$res\n-------------\n";
print "ошибки: \n";
print_r($errors);
/**
результат:
-------------
<iframe src="https://youtube.com/embed/4fMLHBiQlKM" width="100" height="100"></iframe><br/>
<iframe src="https://test.com/embed/4fMLHBiQlKM" width="100" height="100"></iframe><br/>
<iframe width="100" height="100"></iframe><br/>
<iframe width="100" height="100"></iframe><br/>

-------------
ошибки:
Array
(
[0] => Array
(
[message] => Недопустимое значение для атрибута тега iframe src=https://example.com/embed/4fMLHBiQlKM
[pos] => 259
[ch] =>
[line] => 0
[str] =>
<ifram
)

[1] => Array
(
[message] => Недопустимое значение для атрибута тега iframe src=https://example.org/embed/4fMLHBiQlKM
[pos] => 347
[ch] =>
[line] => 0
[str] =>

)

)
 */
