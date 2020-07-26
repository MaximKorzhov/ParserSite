<?php

require_once ('ParserTest3.php');

$file = 'bookData.txt';
$pagesPattern = '#/catalog/programmirovanie-1361/(?:[a-z]+-\d/)?#su';
$pathsPattern = '#/product/.*?/#su';
$divContentPattern = '#<div class="item-tab">.*<div class="tabs-d__item js-tab-switcher-item">#su';
$imagePattern = '#<div class="item-cover__main">.*?(src="https://.*?)"#su';
$nameBookPattern = '#<h1 class="item-detail__title">(.*)</h1>#su';
$pricePattern = '#<div class="item-actions__price">.*?</div>#su';
$blockPattern ='#<div class="item-tab">.*<div class="tabs-d__item js-tab-switcher-item">#su';
$splitPattern = '#</div>#';
$stringSearchPatterns = ['#Год#', '#Автор#'];
$patterns = [$imagePattern, $nameBookPattern, $pricePattern];

$parser = new ParserHTML();

$html = file_get_contents('https://book24.ru/catalog/programmirovanie-1361/');
$pages = $parser->getPages($html);
$count = 0;

foreach ($pages as $page)
{
    $paths = $parser->getPaths($html, $pathsPattern);

    foreach ($paths as $path)
    {
        $pageContent = file_get_contents('https://book24.ru' . $path);
        $rowData = $parser->getDataString($patterns, $pageContent);
        $blockData = $parser->getDataBlock($pageContent, $blockPattern, $splitPattern, $stringSearchPatterns,);
        $data = array_merge($blockData ?? '', $rowData ?? '', ["\n"]);
        $parser->write($file, $data);
        $count= ++$count;
    }
}

$parser->write($file, 'Найденное количество товаров: ' . $count);
