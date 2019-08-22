<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\netbase;


use pocketmine\level\format\mcregion\ChunkRequestTask;
use pocketmine\level\Level;
use pocketmine\Server;

/** @deprecated useless */
class NetbaseChunkTask extends ChunkRequestTask
{
    public function onCompletion(Server $server)
    {
        $level = $server->getLevel($this->levelId);
        if($level instanceof Level and $this->hasResult()){
            $p = $level->getProvider();
            if ($p instanceof Netbase){
                $p->sendPlayerChunkPayload($this->chunkX,$this->chunkZ,$this->getResult());
            }
        }
    }
}