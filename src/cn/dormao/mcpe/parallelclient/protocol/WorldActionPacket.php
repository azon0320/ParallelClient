<?php


namespace cn\dormao\mcpe\parallelclient\protocol\action\world;


use cn\dormao\mcpe\parallelclient\protocol\AbstractParallelPacket;

abstract class WorldActionPacket extends AbstractParallelPacket
{
    public function getPacketProcessType()
    {
        return self::PK_TYPE_WORLD;
    }
}