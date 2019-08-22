<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\netbase;


use pocketmine\level\generator\Flat;

class NetbaseGenerator extends Flat
{

    public function generateChunk($chunkX, $chunkZ)
    {
        /*
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $succNetwork = false;
        if ($socket) {
            socket_set_block($socket);
            if (socket_connect($socket, "127.0.0.1", 20050)) {
                socket_write($socket, "Hello, I am PocketMine/Genisys/WorldManager");
                $buf = "";
                if ($flag = socket_recv($socket, $buf, 65535, 0)) {
                    if ($buf != "") {
                        var_dump($buf);
                        $succNetwork = true;
                        $this->parsePreset("2;7,4x3,4;1;", $chunkX, $chunkZ);
                    }
                }
                socket_close($socket);
            }
        }
        var_dump($succNetwork ? "Success Network" : "Local generate");
        */
        //parent::generateChunk($chunkX, $chunkZ); //do nothing
    }
}