
<?php
/*
 * @Author: mrzhang1899 
 * @Date: 2019-07-29 11:35:33 
 * @Last Modified by: mrzhang1899
 * @Last Modified time: 2019-07-29 14:34:35
 * 
 * 
 * @package Lanzou
 * @author Filmy
 * @version 1.2.1
 * @link https://mlooc.cn
 */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');

$url = isset($_GET['url']) ? $_GET['url'] : "";
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : "";
$type = isset($_GET['type']) ? $_GET['type'] : "";

if (strpos($url, 'baidu')) {
    header('Location: bdpan.php?url=' . $url . '&pwd=' . $pwd);
} elseif (strpos($url, 'lanzou')) {
    header('Location: lanzou.php?type=down&url=' . $url . '&pwd=' . $pwd);
} else {
    die(json_encode(
        array(
            'msg' => '非云盘链接'
        ),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    ));
}
