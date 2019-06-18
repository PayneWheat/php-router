#PHP Routing Library

**See this guide: https://medium.com/the-andela-way/how-to-build-a-basic-server-side-routing-system-in-php-e52e613cf241**

I extended the codebase above by adding wildcards/path arguments, the ability to parse the body of a POST request (JSON), and .htaccess fallback so the routing library can be included in any public web directory. 

I also included a simple example of how to utilize the authorization header in the HTTP request. In a recent project, I was able to integrate a JWT decoding library to authorize requests.

Routes are defined in index.php.
###GET HTML
```php
$router->get('/api', function() {
    return <<<HTML
    <h2>Hello world</h2>
HTML;
});
```

###GET JSON
This is just an example of how to return a JSON object
```php
$router->get('/api/sampleget/', function(){
    $someData = [];
    for($j = 0; $j < 4; $j++) {
        $someData[] = [];
        for($i = 0; $i < 15; $i++) {
            $someData[$j][] = $i;
        }
    }

    return json_encode($someData) . "\n";
});
```

###POST JSON Body
An example of how to access the body of the request using the getBody method. Obviously, you'd want to do something with the data, such as storing it in the DB. This example simply returns it as a JSON object.
```php
$router->post('/api/samplepost/', function($request) {
    return json_encode($request->getBody()) . "\n";
});
```

###Simple Authorization Example
```php
$router->get('/api/sampleauth', function($request) {
    $data = [];
    $headers = getallheaders();
    $data["auth"] = $headers["Authorization"];
    return json_encode($data);
});
```