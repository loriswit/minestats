<?php

function getStatus()
{
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 5, "usec" => 0));
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));
    
    $host = CONFIG["server"]["host"];
    $port = CONFIG["server"]["port"];
    if(@socket_connect($socket, $host, $port) === false)
        return ["status" => "offline"];
    
    else
    {
        $handshake = pack("cccca*nccc", 0, 145, 03, strlen($host), $host, $port, 1, 1, 0);
        $handshake = chr(strlen($handshake) - 2) . $handshake;
        socket_write($socket, $handshake);
        
        // read VarInt
        while(ord(socket_read($socket, 1)) != 0);
        for($i = 0, $size = 0; ($byte = ord(socket_read($socket, 1))) >= 128; $i++)
            $size = ($byte - 128) * pow(128, $i);
        $size += $byte * pow(128, $i);
        
        socket_recv($socket, $json, $size, MSG_WAITALL);
        $info = json_decode($json, true);
        $status = ["status" => "online", "info" => $info];
        return $status;
    }
}
