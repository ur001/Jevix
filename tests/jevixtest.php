<?php
/**
 * Тестовый пример работы Jevix-а
 *
 * За помощь в написании примера спасибо Александру (http://vaart.habrahabr.ru, indrid@mail.ru)
 */

require('../src/functions.php');
require('../src/Jevix.php');

$jevix = new Jevix();

//Конфигурация

// 1. Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
$jevix->cfgAllowTags(array('a', 'img', 'i', 'b', 'u', 'em', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'pre', 'acronym', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'adabracut', 'br', 'code', 'video'));

// 2. Устанавливаем коротие теги. (не имеющие закрывающего тега)
$jevix->cfgSetTagShort(array('br','img'));

// 3. Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
$jevix->cfgSetTagPreformatted(array('pre'));

// 4. Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
$jevix->cfgSetTagCutWithContent(array('script', 'object', 'iframe', 'style'));

// 5. Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
$jevix->cfgAllowTagParams('a', array('title', 'href'));
$jevix->cfgAllowTagParams('img', array('src', 'alt' => '#text', 'title', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int'));


// 6. Устанавливаем параметры тегов являющиеся обязяательными. Без них вырезает тег оставляя содержимое.
$jevix->cfgSetTagParamsRequired('img', 'src');
$jevix->cfgSetTagParamsRequired('a', 'href');

// 7. Устанавливаем теги которые может содержать тег контейнер
//    cfgSetTagChilds($tag, $childs, $isContainerOnly, $isChildOnly)
//       $isContainerOnly : тег является только контейнером для других тегов и не может содержать текст (по умолчанию false)
//       $isChildOnly : вложенные теги не могут присутствовать нигде кроме указанного тега (по умолчанию false)
//$jevix->cfgSetTagChilds('ul', 'li', true, false);

// 8. Устанавливаем атрибуты тегов, которые будут добавлятся автоматически
$jevix->cfgSetTagParamDefault('a', 'rel', null, true);
//$jevix->cfgSetTagParamsAutoAdd('a', array('rel' => 'nofollow'));
//$jevix->cfgSetTagParamsAutoAdd('a', array('name'=>'rel', 'value' => 'nofollow', 'rewrite' => true));

$jevix->cfgSetTagParamDefault('img', 'width',  '300px');
$jevix->cfgSetTagParamDefault('img', 'height', '300px');
//$jevix->cfgSetTagParamsAutoAdd('img', array('width' => '300', 'height' => '300'));
//$jevix->cfgSetTagParamsAutoAdd('img', array(array('name'=>'width', 'value' => '300'), array('name'=>'height', 'value' => '300') ));

// 9. Устанавливаем автозамену
$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)'), array('±', '©', '®'));

// 10. Включаем или выключаем режим XHTML. (по умолчанию включен)
$jevix->cfgSetXHTMLMode(true);

// 11. Включаем или выключаем режим замены переноса строк на тег <br/>. (по умолчанию включен)
$jevix->cfgSetAutoBrMode(true);

// 12. Включаем или выключаем режим автоматического определения ссылок. (по умолчанию включен)
$jevix->cfgSetAutoLinkMode(true);

// 13. Отключаем типографирование в определенном теге
$jevix->cfgSetTagNoTypography('code');

// 14. Ставим колбэк
$jevix->cfgSetTagCallback('h6', 'test_callback');
function test_callback($content){
	return mb_strtoupper($content, 'UTF-8');
}

// 15. Автозамена тегов video на iframe с youtube
$jevix->cfgSetAutoPregReplace(
	array(
		'/<video>http:\/\/(?:www\.|)youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+)(&.+)?<\/video>/Ui',
		'/<video>http:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)(&.+)?<\/video>/Ui',
	),
	array(
		'<iframe width="700" height="394" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
		'<iframe width="700" height="394" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
	)
);

//Парсинг

// Исходный текст
$text = 'Обработка "кавычек" и "вложенных "друг в друга" кавычек".
Расстановка пробелов после запятых,двоеточия,знаков вопроса , и восклицания !Круто?
А после точек - нет.....Зато,парсер понимает тире,отличает его от-дефиса и знает про многоточие!!!!!
Не больше 3-х восклицательных знаков подряд (c).
В этих случаях лишние пробелы не появятся: 2,35%,смайлики ;-? или :-> (да, html теги фильтровать мы умеем)
- диалог +/-5<br>
- првет,лишние br нам не страшны
- автозамена ссылок с http:// и www: www.habrahabr.ru, http://google.com

BEGIN XSS test <img src=hhh="onclick="alert(document.cookie)> END XSS test

переходим к <тегам>
<a>список требуемых</a>
список <b>разрешённых</b> и <notag>запрещённых тегов</notag>, их параметров и другие настройки можно задать в <a href="#" rel="123">конфигурации</a>
защита от XSS <script>alert("XSS")</script>
<code>в теге "code" (c) ничего не - типогрфируем</code>
<pre>в теге "pre"
<b>всё</b> преобразуем в HTML сущности. Оставляем пробелы      и переносы строк</pre>
<ol><li>Этот список</li><li>&nbsp;думаю станет</li><li>&nbsp;просто текстом.</li></ol>
<ul><li>А этот список</li><li>должен</li><li>отобразится нормально.</li></ul>
<img src="www.jpg" width="javascript:alert(1)" height="13" title="у этой картинки ширина будет 300px" onload="alert(1)" hspace="50%"/>
<h6>Этот текст должен обработаться callback-функцией</h6>
(c)2008 ur001(r)
<pre><pre>Проверка вложенных преформатированных тегов</pre></pre>
Знаки препинания на конце автоматических url: http://jevix.ru!
Вставка видео <video>http://www.youtube.com/watch?v=iDkrSD8fOBo</video> через автозамену регулярками

Проверка прохождения url
<a href="javascript:alert(1)">XSS</a>
<img src="yandex.st/morda-logo/i/logo.svg" />
<img src="//yandex.st/morda-logo/i/logo.svg" />
<img src="http://yandex.st/morda-logo/i/logo.svg" />
<img src="https://yandex.st/morda-logo/i/logo.svg" />
<a href="path/">Относительная ссылка</a>
<a href="./path/">Относительная ссылка</a>
<a href="../path">Относительная ссылка наверх</a>
<a href="/path/">Относительная ссылка от корня</a>
<a href="//yandex.st/morda-logo/i/logo.svg">Ссылка ipv6</a>
<a href="yandex.st">Ссылка без указания протокола</a>
<a href="mailto:mail@yandex.ru">Почта с указанием mailto</a>
<a href="mail@yandex.ru">Почта без указания mailto</a>
<a href="ur001.ru">а ещё парсер сам закрывает теги
';

// Переменная, в которую будут записыватся ошибки
$errors = null;

// Парсим
$res = $jevix->parse($text, $errors);
print "результат: \n-------------\n$res\n-------------\n";
print "ошибки: \n";
print_r($errors);

// после парсинга должна получиться такая ботва:
/*-------------
Обработка «кавычек» и «вложенных „друг в друга“ кавычек».<br/>
Расстановка пробелов после запятых, двоеточия, знаков вопроса, и восклицания! Круто?<br/>
А после точек — нет… Зато, парсер понимает тире, отличает его от-дефиса и знает про многоточие!!!<br/>
Не больше 3-х восклицательных знаков подряд ©.<br/>
В этих случаях лишние пробелы не появятся: 2,35%, смайлики ;-? или :-&gt; (да, html теги фильтровать мы умеем)<br/>
 — диалог ±5<br/>
 — првет, лишние br нам не страшны<br/>
 — автозамена ссылок с http:// и www: <a href="http://www.habrahabr.ru" rel="">www.habrahabr.ru</a>, <a href="http://google.com" rel="">google.com</a><br/>
<br/>
BEGIN XSS test <img src="hhh=&quot;onclick=&quot;alert(document.cookie)" width="300px" height="300px"/>END XSS test<br/>
<br/>
переходим к &lt;тегам&gt;<br/>
список требуемых<br/>
список <b>разрешённых</b> и запрещённых тегов, их параметров и другие настройки можно задать в <a href="#" rel="">конфигурации</a><br/>
защита от XSS <br/>
<code>в теге &quot;code&quot; © ничего не - типогрфируем</code><br/>
<pre>в теге &quot;pre&quot;
&lt;b&gt;всё&lt;/b&gt; преобразуем в HTML сущности. Оставляем пробелы      и переносы строк</pre><br/>
<ol><li>Этот список</li><li> думаю станет</li><li> просто текстом.</li></ol><br/>
<ul><li>А этот список</li><li>должен</li><li>отобразится нормально.</li></ul><br/>
<img src="www.jpg" height="13" title="у этой картинки ширина будет 300px" width="300px"/><br/>
<h6>ЭТОТ ТЕКСТ ДОЛЖЕН ОБРАБОТАТЬСЯ CALLBACK-ФУНКЦИЕЙ</h6><br/>
©2008 ur001®<br/>
<pre>&lt;pre&gt;Проверка вложенных преформатированных тегов&lt;/pre&gt;</pre><br/>
Знаки препинания на конце автоматических url: <a href="http://jevix.ru!" rel="">jevix.ru!</a><br/>
Вставка видео через автозамену регулярками<br/>
<br/>
Проверка прохождения url<br/>
XSS<br/>
<img src="http://yandex.st/morda-logo/i/logo.svg" width="300px" height="300px"/><br/>
<img src="//yandex.st/morda-logo/i/logo.svg" width="300px" height="300px"/><br/>
<img src="http://yandex.st/morda-logo/i/logo.svg" width="300px" height="300px"/><br/>
<img src="https://yandex.st/morda-logo/i/logo.svg" width="300px" height="300px"/><br/>
<a href="http://path/" rel="">Относительная ссылка</a><br/>
<a href="./path/" rel="">Относительная ссылка</a><br/>
<a href="../path" rel="">Относительная ссылка наверх</a><br/>
<a href="/path/" rel="">Относительная ссылка от корня</a><br/>
<a href="//yandex.st/morda-logo/i/logo.svg" rel="">Ссылка ipv6</a><br/>
<a href="http://yandex.st" rel="">Ссылка без указания протокола</a><br/>
<a href="mailto:mail@yandex.ru" rel="">Почта с указанием mailto</a><br/>
<a href="mailto:mail@yandex.ru" rel="">Почта без указания mailto</a><br/>
<a href="http://ur001.ru" rel="">а ещё парсер сам закрывает теги<br/>
</a>
-------------
ошибки:
Array
(
    [0] => Array
        (
            [message] => Недопустимое значение для атрибута тега img width=javascript:alert(1). Ожидалось число
            [pos] => 1306
            [ch] =>
            [line] => 0
            [str] =>
<h6>Эт
        )

    [1] => Array
        (
            [message] => Недопустимое значение для атрибута тега img hspace=50%. Ожидалось число
            [pos] => 1306
            [ch] =>
            [line] => 0
            [str] =>
<h6>Эт
        )

    [2] => Array
        (
            [message] => Попытка вставить JavaScript в URI
            [pos] => 1743
            [ch] =>
            [line] => 0
            [str] =>
<img s
        )

)
-------------*/
?>
