<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ClosePacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_CLOSE;
    }

    protected function onEncode()
    {
        return '';
    }

    public function doDecode($raw)
    {
        //do nothing
    }
}