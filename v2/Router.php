<?php
class Router {
    private $request;
    private $supportedHttpMethods = array("GET", "POST");

    function __construct(IRequest $request) {
        $this->request = $request;
    }

    function __call($name, $args) {
        list($route, $method) = $args;
        $pathValues = [];
        if(preg_match_all("/{[A-Za-z0-9]+}/", $route, $results) > 0) {
            
            foreach($results[0] as $result) {
                $pathValues[] = trim($result, "{}");
            }
        }

        if(!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        $formattedRoute = $this->formatRoute($route);
        $this->{strtolower($name)}[$formattedRoute] = ["method"=>$method, "pathValues"=>$pathValues, "path"=>$formattedRoute];
    }

    private function formatRoute($route) {
        $result = rtrim($route, '/');
        if($result === '') {
            return '/';
        }
        return $result;
    }

    private function sortHelper($val1, $val2) {
        return count($val1["pathValues"]) > count($val2["pathValues"]);
    } 
    
    private function sortRoutesByPathValueCount($methodDictionary) {
        $sortedMethods = $methodDictionary;
        usort($sortedMethods, array($this, "sortHelper"));
        return $sortedMethods;
    }

    private function invalidMethodHandler() {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler() {
        header("{$this->request->serverProtocol} 404 Not Found");
    }
    /*
    private function unauthorizedHandler() {
        header("{$this->request->serverProtocol} 401 Unauthorized");
    }
    */
    private function findMatchingMethod($sortedMethods, $formattedRoute) {
        $pathParts = explode("/", $formattedRoute);
        $pathPartsCount = count($pathParts);
        foreach($sortedMethods as $m) {
            $methodParts = explode("/", $m["path"]);
            $methodPartsCount = count($methodParts);
            $match = false;
            if($pathPartsCount == $methodPartsCount) {    
                $arguments = [];
                $i = $methodPartsCount - 1 - count($m["pathValues"]);
                for($i; $i >= 0; $i--) {
                    if($methodParts[$i] != $pathParts[$i]) {
                        break;
                    }
                    if($i == 0) {
                        $j = $pathPartsCount - count($m["pathValues"]);
                        $k = 0;
                        for($j; $j < $pathPartsCount; $j++) {
                            $arguments[$m["pathValues"][$k]] = $pathParts[$j];
                            $k++;
                        }
                        return array($m, $arguments);
                    }
                }
            }
        }
        return array(null, null);
    }

    function resolve() {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formattedRoute = $this->formatRoute($this->request->requestUri);

        $sortedMethods = $this->sortRoutesByPathValueCount($methodDictionary);

        list($method, $arguments) = $this->findMatchingMethod($sortedMethods, $formattedRoute);

        if(is_null($method)) {
            $this->defaultRequestHandler();
            return;
        }

        if(count($arguments) == 0) {
            echo call_user_func_array($method["method"], array($this->request));
        } else {
            echo call_user_func_array($method["method"], array($this->request, $arguments));
        }

    }

    function __destruct() {
        $this->resolve();
    }
}