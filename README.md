# PHP Routing Library

**See this guide: https://medium.com/the-andela-way/how-to-build-a-basic-server-side-routing-system-in-php-e52e613cf241**

I extended the codebase above by adding wildcards/path arguments, the ability to parse the body of a POST request (JSON), and .htaccess fallback so the routing library can be included in any public web directory. 

I also included a simple example of how to utilize the authorization header in the HTTP request. In a recent project, I was able to integrate a JWT decoding library to authorize requests.

### Update httpd.conf
Be sure to update the httpd.conf in every environment to allow override so the .htaccess file will work.

`AllowOverride All`

Routes are defined in index.php.

### GET HTML
```php
$router->get('/api', function() {
    return <<<HTML
    <h2>Hello world</h2>
HTML;
});
```

### GET JSON
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

### POST JSON Body
An example of how to access the body of the request using the getBody method. Obviously, you'd want to do something with the data, such as storing it in the DB. This example simply returns it as a JSON object.
```php
$router->post('/api/samplepost/', function($request) {
    return json_encode($request->getBody()) . "\n";
});
```

### Simple Authorization Example
Pass the value of `$headers["Authorization"]` to your decoding library of choice. This example simply returns the value of the Authorization header in the HTTP request.
```php
$router->get('/api/sampleauth', function($request) {
    $data = [];
    $headers = getallheaders();
    $data["auth"] = $headers["Authorization"];
    return json_encode($data);
});
```

### Tests, PHPUnit
Install PHPUnit

Set up config.xml for PHP unit; you may need to change the DOCUMENT_ROOT value.

I used Xdebug for my code coverage reports, but any code coverage report generator that works with PHPUnit and your operating system should work.

To run the tests, navigate to the root directory and run the following command:

`phpunit --whitelist api --stderr --coverage-html report tests`

