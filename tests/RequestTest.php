<?php
use PHPUnit\Framework\TestCase;
//include_once '../api/Request.php';
include_once './api/Request.php';
include_once './api/Router.php';

function MockServerVariable($m="GET") {
	$s = [];
	$s["HTTP_HOST"] = "mysite.local";
	$s["HTTP_CONNECTION"] = "keep-alive";
	$s["HTTP_CACHE_CONTROL"] = "max-age=0";
	$s["REQUEST_METHOD"] = $m;
	return $s;
}

class RequestTest extends TestCase
{
	public function testRequestConst() {
		$serv = MockServerVariable();
		$request = new Request($serv);
		$this->assertEquals($serv["HTTP_HOST"], $request->httpHost);
	}
	public function testRequestGetBody() {
		$servGet = MockServerVariable();
		$request = new Request($servGet);
		$this->assertEquals(null, $request->getBody());
		$servPost = MockServerVariable("POST");
		$request = new Request($servPost);
		$this->assertEquals(null, $request->getBody());
		$servDel = MockServerVariable("DELETE");
		$request = new Request($servDel);
		$this->assertEquals(null, $request->getBody());
	}
}
