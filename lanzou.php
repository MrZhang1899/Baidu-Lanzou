<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json; charset=utf-8');
//$ip = "221.4.172.162";
//$ip_port = "3128";
function CurlGet($url, $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36')
{
    global $ip;
    global $ip_port;
	$cookie = 'UM_distinctid=16aceda3c0f29c-0b1e4102d538ca-b781e3e-144000-16aceda3c11330; noads=1';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);     
    curl_setopt($curl, CURLOPT_PROXY, $ip);                            //代理服务器地址
    curl_setopt($curl, CURLOPT_PROXYPORT, $ip_port); 
	curl_setopt($curl, CURLOPT_REFERER, 'https://www.lanzous.com');
	curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    if ($UserAgent != "") {
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    }
    #关闭SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    #返回数据不直接显示
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function CurlPost($post_data, $url, $ifurl, $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.10 Safari/537.36')
{
    global $ip;
    global $ip_port;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);     
    curl_setopt($curl, CURLOPT_PROXY, $ip);                            //代理服务器地址
    curl_setopt($curl, CURLOPT_PROXYPORT, $ip_port);  
    curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent); 
    if ($ifurl != '') {
        curl_setopt($curl, CURLOPT_REFERER, $ifurl);
    }
    #关闭SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    #返回数据不直接显示
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function CurlGetRealUrl($url)
{
    global $ip;
    global $ip_port;
	$curl = curl_init();
	$headers = array(
	'Host:vip.d0.baidupan.com',
		'Connection:keep-alive',
		'Upgrade-Insecure-Requests:1',
		'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.10 Safari/537.36',
		'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
		'Accept-Encoding:gzip, deflate, br',
		'Accept-Language:zh-CN,zh;q=0.9'
	);
	curl_setopt($curl, CURLOPT_URL, $url);     
    curl_setopt($curl, CURLOPT_PROXY, $ip);                            //代理服务器地址
    curl_setopt($curl, CURLOPT_PROXYPORT, $ip_port); 
	curl_setopt($curl, CURLOPT_REFERER, 'https://www.lanzous.com');
	//设置头文件的信息作为数据流输出
	curl_setopt($curl, CURLOPT_HEADER, true);
	//设置获取的信息以文件流的形式返回，而不是直接输出。
	curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);  
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
	#关闭SSL
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	#返回数据不直接显示
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($curl);  
	curl_close($curl);
	return $response."end";
}

$url = isset($_GET['url']) ? $_GET['url'] : "";
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : "";
$type = isset($_GET['type']) ? $_GET['type'] : "";
if (empty($url)) {
    die(
    json_encode(
        array(
            'code' => 400,
            'msg' => '请输入URL'
        )
        , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
}
$softInfo = CurlGet($url);
if (strstr($softInfo, "文件取消分享了") != false) {
    die(
    json_encode(
        array(
            'code' => 400,
            'msg' => '文件取消分享了'
        )
        , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
}
preg_match('~class="b">(.*?)<\/div>~', $softInfo, $softName);

if (strstr($softInfo, "手机Safari可在线安装") != false) {
  	if(strstr($softInfo, "n_file_infos") != false){
      	$ipaInfo = CurlGet($url, 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
    	preg_match('~href="(.*?)" target="_blank" class="appa"~', $ipaInfo, $ipaDownUrl);
    }else{
    	preg_match('~com/(\w+)~', $url, $lanzouId);
        if (!isset($lanzouId[1])) {
            die(
            json_encode(
                array(
                    'code' => 400,
                    'msg' => '解析失败，获取不到文件ID'
                )
                , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }
        $lanzouId = $lanzouId[1];
        $ipaInfo = CurlGet("https://www.lanzous.com/tp/" . $lanzouId, 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1');
        preg_match('~href="(.*?)" id="plist"~', $ipaInfo, $ipaDownUrl);
    }
    
    $ipaDownUrl = isset($ipaDownUrl[1]) ? $ipaDownUrl[1] : "";
    if ($type != "down") {
        die(
        json_encode(
            array(
                'code' => 200,
                'msg' => '',
                'name' => isset($softName[1]) ? $softName[1] : "",
                'downUrl' => $ipaDownUrl
            )
            , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    } else {
        header("Location:$ipaDownUrl");
        die;
    }
}
if($pwd){
	$ifurl = "https://www.lanzous.com/";
}else{
	preg_match('~<iframe.*?name="[\s\S]*?" src="(.*?)" frameborder="[\s\S]*?" scrolling="~', $softInfo, $link);
	$ifurl = "https://www.lanzous.com/" . $link[1];
  	$softInfo = CurlGet($ifurl);
}
if (empty($pwd) && strstr($softInfo, "输入密码") != false) {
    die(
    json_encode(
        array(
            'code' => 400,
            'msg' => '请输入分享密码'
        )
        , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
}
if($pwd){
	preg_match("~'action=(.*?)&sign=(.*?)&p=~", $softInfo, $segment);
}else{
	preg_match("~[\s\S]*?:'(.*?)',[\s\S]*?:'(.*?)'~", $softInfo, $segment);
}
$post_data = array(
    "action" => $segment[1],
    "sign" => $segment[2],
    "p" => $pwd,
);
$softInfo = CurlPost($post_data, "https://www.lanzous.com/ajaxm.php", $ifurl);
$softInfo = json_decode($softInfo, true);
if ($softInfo['zt'] != 1) {
    die(
    json_encode(
        array(
            'code' => 400,
            'msg' => $softInfo['inf']
        )
        , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
}
$downUrl = $softInfo['dom'] . '/file/' . str_replace('\/','/',$softInfo['url']);
preg_match('~Location: (.*?)[\s\S]*?end~',CurlGetRealUrl($downUrl),$real_url);
$real_url = trim(str_replace(array('Location:','/r/n','/r','/n','end'),'',$real_url[0]));
if ($type != "down") {  
    die(
    json_encode(
        array(
            'code' => 200,
            'msg' => '如无法下载请返回文章查看是否有其他下载方式',
            'name' => isset($softName[1]) ? $softName[1] : $softInfo['inf'],
            'downUrl' => $real_url
        )
        , JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
} else {
    header("Location:$real_url");
    die;
}