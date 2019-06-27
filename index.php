
<?php
/*
 * Author: MrZhang1899
 * Date: 2019/06/27 15:22
 * 借鉴了 https://github.com/MHanL/LanzouAPI 的部分代码, 
 * 转载或使用请保留版权！！   
 * 以下是作者信息:
 * 
 * @package Lanzou
 * @author Filmy
 * @version 1.2.1
 * @link https://mlooc.cn
 * 
 */
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
$url = isset($_GET['url']) ? $_GET['url'] : "";
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : "";
//判断链接是否为空
if (empty($url)) {
    die(json_encode(
        array(
            'msg' => '请输入URL'
        ),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    ));
} elseif (strpos($url, 'pan.baidu.com/s/') !== false) { //判断是否百度网盘链接
    $url = preg_replace("/[\\x80-\\xff]/", "", $url); //过滤链接中的中文
    $insert_string = "wp";
    $start = 17;
    $newurl = substr_replace($url, $insert_string, $start, 0);
    $detail = MrZhang1899($url); //查看原网页
    // echo $detail;die();
    if (empty($pwd) && strpos($detail, '请输入提取码') !== false) {
        die(json_encode(
            array(
                'msg' => '请输入提取码'
            ),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));
    } elseif (strpos($detail, '分享的文件已经被删除了') !== false) {
        die(json_encode(
            array(
                'msg' => '分享的文件已经被删除了'
            ),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));
    } elseif (strpos($detail, '链接分享内容可能因为涉及侵') !== false) {
        die(json_encode(
            array(
                'msg' => '此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！'
            ),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));
    } elseif (empty($pwd)) {
        header("Location:$newurl");
    } else {
        $newurl = $newurl . "?path=%2F&page=1&pwd=" . $pwd;
        header("Location:$newurl");
    }
} else {
    die(json_encode(
        array(
            'msg' => '非百度网盘链接'
        ),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    ));
}

function MrZhang1899($url, $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36')
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    if ($UserAgent != "") {
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
