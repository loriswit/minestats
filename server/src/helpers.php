<?php

use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

function withFile(Request $request, Response $response, $filename, $root)
{
    $content = @file_get_contents("$root/$filename");
    if($content === false)
        throw new NotFoundException($request, $response);
    
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
