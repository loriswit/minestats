<?php

use Slim\Http\Request;
use Slim\Http\Response;

require "status.php";
require "helpers.php";

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
    return withJsonFile($request, $response, CONFIG["server"]["world"] . "/stats/$uuid.json");
});

$app->get("/advancements/{uuid}", function(Request $request, Response $response, $uuid)
{
    return withJsonFile($request, $response, CONFIG["server"]["world"] . "/advancements/$uuid.json");
});

$app->get("/archived", function(Request $request, Response $response)
{
    return $response->withJson(array_keys(CONFIG["archived"]));
});

$app->group("/archived/{name}", function()
{
    $this->get("/users", function(Request $request, Response $response, $name)
    {
        $server = CONFIG["archived"][$name];
        return withJsonFile($request, $response, "usercache.json", $server["root"]);
    });
    
    $this->get("/stats/{uuid}", function(Request $request, Response $response, $name, $uuid)
    {
        $server = CONFIG["archived"][$name];
        return withJsonFile($request, $response, $server["world"] . "/stats/$uuid.json", $server["root"]);
    });
    
    $this->get("/advancements/{uuid}", function(Request $request, Response $response, $name, $uuid)
    {
        $server = CONFIG["archived"][$name];
        return withJsonFile($request, $response, $server["world"] . "/advancements/$uuid.json", $server["root"]);
    });
});
