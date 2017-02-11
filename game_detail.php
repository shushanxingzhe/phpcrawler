<?php
/**
 * User: liujiafu
 * Date: 2017/2/9
 * Time: 13:10
 */


require_once (__DIR__.'/config.php');

function fetch_game($url)
{
    global $conn;

    $IMG_PATH = ROOT_PATH . '/resource/image';
    $DESC_PATH = ROOT_PATH . '/resource/screenshot';

    $doc = new DOMDocument;

    $doc->loadHTMLFile($url);
    $xPath = new DOMXPath($doc);

    $info = $xPath->query('//div[@class="gtext"]/ul/li');
    $title = $xPath->query('//div[@class="g_tit"]/span');
    $version = $title[0]->getElementsByTagName('font')->item(0);
    $title[0]->removeChild($version);

    $description = $xPath->query('//div[@class="gameinfo"]');
    $brief = $description[0]->getElementsByTagName('p')->item(0);
    $description[0]->removeChild($brief);
    $gtag = $xPath->query('//div[@class="gtag"] //a');
    $tag_array = array();
    foreach ($gtag as $tag_item) {
        $tag_array[] = $tag_item->nodeValue;
    }
    $tags = json_encode($tag_array, JSON_UNESCAPED_UNICODE);

    $img = $xPath->query('//em[@class="gimg"]/img');
    $img_url = $img[0]->getAttribute('src');
    $image_path = parse_url($img_url, PHP_URL_PATH);
    $file_path = dirname($image_path);
    if (!file_exists($IMG_PATH . $file_path)) {
        mkdir($IMG_PATH . $file_path, 0777, true);
    }
    file_put_contents($IMG_PATH . $image_path, fopen($img_url, 'r'));


    $imgview = $xPath->query('//div[@id="imgview"] //img');
    $shot_array = array();
    foreach ($imgview as $img_item) {
        $shot_img = $img_item->getAttribute('src');
        $shot_path = parse_url($shot_img, PHP_URL_PATH);
        $file_path = dirname($shot_path);
        if (!file_exists($DESC_PATH . $file_path)) {
            mkdir($DESC_PATH . $file_path, 0777, true);
        }
        file_put_contents($DESC_PATH . $shot_path, fopen($shot_img, 'r'));
        $shot_array[] = $shot_path;
    }
    $screenshot = json_encode($shot_array);


    $game = array(
        'name' => $title[0]->nodeValue,
        'version' => $version->nodeValue,
        'type' => $info[0]->getElementsByTagName('a')[0]->nodeValue,
        'language' => $info[1]->getElementsByTagName('span')[0]->nodeValue,
        'size' => $info[2]->getElementsByTagName('span')[0]->nodeValue,
        'update_date' => $info[3]->getElementsByTagName('span')[0]->nodeValue,
        'tags' => $tags,
        'brief' => addslashes($brief->nodeValue),
        'description' => addslashes($description[0]->nodeValue),
        'image' => $image_path,
        'screenshot' => $screenshot
    );

    if ($res = mysqli_query($conn, "select count(0) from game where name = '{$game['name']}' and version = '{$game['version']}' and language = '{$game['language']}'")) {
        $exist = mysqli_fetch_row($res);
        if($exist[0]) {
            return false;
        }
    }

    $columns = implode('\',\'', $game);
    $sql = "insert into game (`name`, `version`, `type`, `language`, `size`, `update_date`, `tags`, `brief`, `description`, `image`, `screenshot`) values('{$columns}')";

    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        echo "Error: " . mysqli_error($conn);
        die();
    }

}
