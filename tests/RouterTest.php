<?php
use PHPUnit\Framework\TestCase;
include_once './api/Request.php';
include_once './api/Router.php';

/*
	protected static function getMethod($name) {
		$class = new ReflectionClass('Router');
		$method = $class->getMethod($name);
		return method;
	}
*/

class RequestMock implements IRequest {
    function __construct($method, $uri) {
        $this->bootstrapSelf($method, $uri);
    }

    private function bootstrapSelf($method, $uri) {
        $this->requestMethod = $method;
        $this->requestUri = $uri;
        $this->serverProtocol = "HTTP/1.1";
    }

    private function toCamelCase($string) {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $matches);
        foreach($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }

    public function getBody() {
        if($this->requestMethod === "GET") {
            return;
        }
        if($this->requestMethod === "POST") {
            $result = array();
            foreach($_POST as $key => $value) {
                $result[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);   
            }
            return $result;
        }
        return $body;
    }
}

class RouterTest extends TestCase
{
	public function testGet200() {
		
		$router = new Router(new RequestMock("GET", "/some/path/here"));
		//$response = "Hello";
		$router->get("/some/path/here", function($request) {
			 return "Success!";
		});
		$this->expectOutputString("Success!");
	}
	public function testGetEmptyRoute() {
		
		$router = new Router(new RequestMock("GET", "/"));
		//$response = "Hello";
		$router->get("/", function($request) {
			 return "Root/empty route";
		});
		$this->expectOutputString("Root/empty route");
	}		
	public function testGet404() {
		
		$router = new Router(new RequestMock("GET", "/not/valid/path"));
		//$response = "Hello";
		$router->get("/some/path/here", function($request) {
			 return "Success!";
		});
		$this->expectOutputString("");
	}
	public function testGet405() {
		
		$router = new Router(new RequestMock("GET", "/some/path/here"));
		//$response = "Hello";
		$router->delete("/some/path/here", function($request) {
			 return "Success!";
		});
		$router->get("/some/other/path", function($request) {
			 return "Success!";
		});
		$this->expectOutputString("");
	}
	public function testParameter() {
		$router = new Router(new RequestMock("GET", "/param/path/12345"));
		$router->get("/param/path/{num}", function($request, $parameters) {
			return $parameters["num"];
		});
		$this->expectOutputString("12345");
	}
	public function testMultipleRoutes() {
		$router = new Router(new RequestMock("GET", "/param/path/12345"));
		$router->get("/param/path/{num}", function($request, $parameters) {
			return $parameters["num"];
		});
		$router->get("/param/path/another", function($request, $parameters) {
			return "Non-parameter route";
		});
		$this->expectOutputString("12345");
	}
}
