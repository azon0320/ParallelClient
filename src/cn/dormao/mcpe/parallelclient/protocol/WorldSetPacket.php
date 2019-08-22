<?php


namespace cn\dormao\mcpe\parallelclient\protocol;



class WorldSetPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_WORLD_SET;
    }

    /** @var string */
    public $worldname;

    protected function onEncode()
    {
        return self::openWriter()->writeUTF8($this->worldname)->getBuffer();
    }

    public function doDecode($raw)
    {
        $this->worldname = self::openReader($raw)->readUTF8();
    }
}