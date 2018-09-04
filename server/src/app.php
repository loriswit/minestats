<?php

use Slim\Http\Request;
use Slim\Http\Response;

define("CONFIG", json_decode(file_get_contents(__DIR__ . "/../config.json"), true));

$container = new \Slim\Container();

$container["settings"]["displayErrorDetails"] = false;

$container["foundHandler"] = function()
{
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

$container["notFoundHandler"] = function($c)
{
    return function($request, Response $response) use ($c)
    {
        return $response->write("not found")->withStatus(404);
    };
};

$app = new \Slim\App($container);

// redirect paths with a trailing slash
$app->add(function(Request $request, Response $response, callable $next)
{
    $uri = $request->getUri();
    $path = $uri->getPath();
    if($path != "/" && substr($path, -1) == "/")
    {
        $uri = $uri->withPath(substr($path, 0, -1));
        
        if($request->getMethod() == "GET")
            return $response->withRedirect((string)$uri, 301);
        else
            return $next($request->withUri($uri), $response);
    }
    
    return $next($request, $response);
});

require __DIR__ . "/routes.php";

$app->run();
