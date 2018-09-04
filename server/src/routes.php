<?php

use Slim\Http\Request;
use Slim\Http\Response;

require "status.php";

function withFile(Request $request, Response $response, $filename, $root)
{
    $content = @file_get_contents("$root/$filename");
    if($content === false)
        throw new \Slim\Exception\NotFoundException($request, $response);
    
    return $response->write($content);
}

function withHtmlFile(Request $request, Response $response, $filename, $root = CONFIG["server"]["root"])
{
    return withFile($request, $response, $filename, $root)->withHeader("Content-type", "text/html");
}

function withTextFile(Request $request, Response $response, $filename, $root = CONFIG["server"]["root"])
{
    return withFile($request, $response, $filename, $root)->withHeader("Content-type", "text/plain");
}

function withJsonFile(Request $request, Response $response, $filename, $root = CONFIG["server"]["root"])
{
    return withFile($request, $response, $filename, $root)->withHeader("Content-type", "application/json");
}

$app->get("/", function(Request $request, Response $response)
{
    return withHtmlFile($request, $response, "/assets/about.html", __DIR__);
});

$app->get("/status", function(Request $request, Response $response)
{
    return $response->withJson(getStatus());
});

$app->get("/logs", function(Request $request, Response $response)
{
    return withTextFile($request, $response, "logs/latest.log");
});

$app->get("/users", function(Request $request, Response $response)
{
    return withJsonFile($request, $response, "usercache.json");
});

$app->get("/stats/{uuid}", function(Request $request, Response $response, $uuid)
{
    return withJsonFile($request, $response, "world/stats/$uuid.json");
});

$app->get("/advancements/{uuid}", function(Request $request, Response $response, $uuid)
{
    return withJsonFile($request, $response, "world/advancements/$uuid.json");
});
