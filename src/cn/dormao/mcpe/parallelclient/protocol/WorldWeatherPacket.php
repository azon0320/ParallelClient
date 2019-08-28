<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class WorldWeatherPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_WORLD_WEATHER;
    }

    public $weatherId;

    protected function onEncode()
    {
        // TODO: Implement onEncode() method.
    }

    public function doDecode($raw)
    {
        // TODO: Implement doDecode() method.
    }
}