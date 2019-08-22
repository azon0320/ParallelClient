<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class GetSpawnPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_GET_SPAWN;
    }

    protected function onEncode()
    {
        return "";
    }

    public function doDecode($raw)
    {
        //do nothing
    }
}