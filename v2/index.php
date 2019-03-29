<?php
include_once 'Request.php';
include_once 'Router.php';
$router = new Router(new Request($_SERVER));

$router->get('/v2', function() {
    return <<<HTML
    <h2>Hello world</h2>
HTML;
});

$router->get('/v2/profile/', function($request) {
    return <<<HTML
    <h1>Profile</h1>
HTML;
});

$router->post('/v2/samplepost/', function($request) {
    return json_encode($request->getBody());
});

$router->get('/v2/sampleget/', function(){
    $someData = [];
    for($j = 0; $j < 4; $j++) {
        $someData[] = [];
        for($i = 0; $i < 15; $i++) {
            $someData[$j][] = $i;
        }
    }

    return json_encode($someData) . "\n";
});

$router->get('/v2/sampleget/{foo}/{bar}', function($request, $parameters) {
    $someData = new stdClass();
    $someData->auth = $parameters["foo"];
    $someData->beach = $parameters["bar"];

    return json_encode($someData) . "\n";
});

$router->get('/v2/sampleauth', function($request) {
    $data = [];
    $headers = getallheaders();
    $data["auth"] = $headers["Authorization"];
    return json_encode($data);
});

