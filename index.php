<?php
use CustomCurl\Client;
use Bramus\Router;
header("x-powered-by: Teleimg v0.2beta");

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/class/DB.class.php';

$router = new Router\Router();

$router->get('/', function() {
    header("content-type:text/html; charset:utf-8");
    echo file_get_contents("assets/html/index.html");
});

$router->get('/test', function() {
});

//$router->get('/api/fetch_all', function() {
//    $data = new database('assets/data/teleimg.db');
//    echo json_encode($data->fetchAll());
//});

$router->post('upload', function() {
    header("content-type:application/json; charset:utf-8");
    header("Cache-control: no-cache");
    $curlObj = Client::init('https://telegra.ph/upload', 'post')
        ->setHeader('User-Agent', getallheaders()['User-Agent'])
        ->set('postFields', ['file' => new CURLFile($_FILES["file"]["tmp_name"])])
        ->set('postFieldsBuildQuery', false)
        ->exec();
    if (!$curlObj->getStatus()) {
        exit(json_encode($curlObj->getCurlErrNo()));
    }
    $body = json_decode($curlObj->getBody());
    if ($curlObj->getInfo()['http_code'] !== 200||$body == "" || !is_array($body)) {
        http_response_code($curlObj->getInfo()['http_code']);
        halt("Failed to upload");
    }

    $data = new database('assets/data/teleimg.db');
    $fileName = str_replace("/file/", "", $body[0]->src);
    $data->insert($fileName);
    echo json_encode(['success'=> 1, "src"=>$body[0]->src]);
});

$router->get('/file/(.*)', function($name) {
    $data = new database('assets/data/teleimg.db');
    if(!$data->fetchName($name)){
        http_response_code(403);
        header("content-type: image/jpeg");
//        header("Content-disposition: attachment;filename=AccessDenied.png");
        header('Cache-Control: public,s-maxage=36000,max-age=3600');
        echo file_get_contents(__DIR__ . "/assets/img/akkarin.jpg");
        exit();
    }

    $curlObj = Client::init('https://telegra.ph/file/'.$name)
        ->setHeader('User-Agent', getallheaders()['User-Agent'])
        ->exec();

    if (!$curlObj->getStatus()) {
        http_response_code(501);
        echo file_get_contents(__DIR__ . "/assets/img/akkarin.jpg");
    }
    $header = strtolower($curlObj->getHeader());
    $start = strpos($header, "content-type");
    $end = strpos($header, "content-length");
    if($curlObj->getInfo()['http_code'] === 200) {
        header('Cache-Control: public,s-maxage=360000,max-age=360000');
        header("content-type: ".substr($header, $start+14, $end-$start-16));
        echo $curlObj->getBody();
    }else{
        http_response_code($curlObj->getInfo()['http_code']);
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