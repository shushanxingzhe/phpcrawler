<?php
/**
 * User: liujiafu
 * Date: 2017/2/9
 * Time: 11:29
 */
error_reporting(0);

require_once (__DIR__.'/game_detail.php');

$url = 'http://www.xxxxxx.com/m/';


$doc = new DOMDocument;

$doc->loadHTMLFile($url);
$xPath = new DOMXPath( $doc );

$results = $xPath->query('//div[@id="games"]/div[@class="newgame"]');
foreach ($results as $items){
    echo $items->getElementsByTagName('span')[0]->nodeValue;
    $list = $items->getElementsByTagName('li');
    $result_array = array();
    foreach ($list as $game){
        $node_a = $game->getElementsByTagName('a')[0];
        $node_em = $game->getElementsByTagName('em')[0];
        $item = array(
            'name' => $node_a->nodeValue,
            'url'  => $node_a->getAttribute('href'),
            'date' => $node_em->nodeValue
        );
        fetch_game($item['url']);

        $result_array [] = $item;
    }
    echo json_encode($result_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
