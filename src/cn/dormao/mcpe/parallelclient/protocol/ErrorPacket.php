<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ErrorPacket extends AbstractParallelPacket
{

    public function getPacketId()
    {
        return self::PK_ERROR;
    }

    /** @var int PacketId */
    public $errcode;
    /** @var string */
    public $errstring;

    protected function onEncode()
    {
        $os = self::openWriter();
        $os->writeASCII($this->errcode);
        $os->writeUTF8($this->errstring);
        return $os->getBuffer();
    }

    public function doDecode($raw)
    {
        $is = self::openReader($raw);
        $this->errcode = ord($is->readASCII());
        $this->errstring = $is->readUTF8();
    }
}