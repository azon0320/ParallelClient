<?php


namespace cn\dormao\mcpe\parallelclient;


use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\Server;

interface ParallelPocketmine
{
    /** @return Server */
    function pocketmine();

    /** @return ParallelClient[] */
    function getParallelChannels();

    /**
     * @param Level|LevelProvider $levelOrProvider
     * @return ParallelClient|null
     */
    function getParallelClientInstance($levelOrProvider);


}