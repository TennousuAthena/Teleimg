<?php
use CustomCurl\Client;
use Bramus\Router;
header("content-type:application/json; charset:utf-8");
header("x-powered-by: Teleimg v0.1beta");

require __DIR__ . '/vendor/autoload.php';

$router = new Router\Router();

$router->get('/', function() {
    $curlObj = Client::init('https://telegra.ph/upload', 'post')
        ->set('postFields', ['file' => new CURLFile('./test.jpg')])
        ->set('postFieldsBuildQuery', false)
        ->exec();


    if (!$curlObj->getStatus()) {
        exit(json_encode($curlObj->getCurlErrNo()));
    }
    $body = json_decode($curlObj->getBody());
    if ($body == "") {
        halt("Failed to upload");
    }
    echo json_encode(['success'=> 1, "src"=>$body[0]->src]);
});
// Run it!
$router->run();

function halt($msg){
    exit(json_encode(["success"=> 0, "msg"=>$msg]));
}