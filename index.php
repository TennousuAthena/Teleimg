<?php
use CustomCurl\Client;
use Bramus\Router;
header("x-powered-by: Teleimg v0.1beta");

require __DIR__ . '/class/sqlite.class.php';
require __DIR__ . '/vendor/autoload.php';

$router = new Router\Router();

$router->get('/', function() {
    header("content-type:text/html; charset:utf-8");
    echo file_get_contents("assets/html/index.html");
});


$router->post('upload', function() {
    header("content-type:application/json; charset:utf-8");
    header("Cache-control: no-cache");
    $curlObj = Client::init('https://telegra.ph/upload', 'post')
        ->set('postFields', ['file' => new CURLFile($_FILES["file"]["tmp_name"])])
        ->set('postFieldsBuildQuery', false)
        ->exec();

    if (!$curlObj->getStatus()) {
        exit(json_encode($curlObj->getCurlErrNo()));
    }
    $body = json_decode($curlObj->getBody());
    if ($curlObj->getInfo()['http_code'] !== 200||$body == "" || !is_array($body)) {
        halt("Failed to upload");
    }
    echo json_encode(['success'=> 1, "src"=>$body[0]->src]);
});

$router->get('/file/(.*)', function($name) {

    $curlObj = Client::init('https://telegra.ph/file/'.$name)->exec();

    if (!$curlObj->getStatus()) {
        throw new Exception('Curl Error', $curlObj->getCurlErrNo());
    }
    $header = strtolower($curlObj->getHeader());
    $start = strpos($header, "content-type");
    $end = strpos($header, "content-length");
    if($curlObj->getInfo()['http_code'] === 200) {
        header('Cache-Control: public,s-maxage=360000,max-age=360000');
        header("content-type: ".substr($header, $start+14, $end-$start-16));
        echo $curlObj->getBody();
    }else{
        header("content-type: image/jpeg");
        header('Cache-Control: public,s-maxage=36000,max-age=3600');
        echo file_get_contents(__DIR__ . "/assets/img/akkarin.jpg");
    }
});

// Run it!
$router->run();

function halt($msg){
    exit(json_encode(["success"=> 0, "msg"=>$msg]));
}