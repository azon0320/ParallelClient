<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


use cn\dormao\mcpe\parallelclient\instance\XZYParallelChunk;

/**
 * Class TestPacket
 * @package cn\dormao\mcpe\parallelclient\protocol
 * @deprecated
 */
class TestPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_TEST;
    }

    public $asciiValue;
    public $byteValue;
    public $utf8Value;
    public $intValue;

    public function __construct(XZYParallelChunk $chunk = null)
    {
        $this->asciiValue = chr(253);
        $this->byteValue = $chunk != null ? $chunk->getBlocks() : "";
        $this->utf8Value = "Information";
    }

    public function requireSendIndependent()
    {
        return false;
    }

    protected function onEncode()
    {
        $writer = self::openWriter();
        $writer
            ->writeASCII($this->asciiValue)
            ->writeBytes($this->byteValue)
            ->writeUTF8($this->utf8Value)
            ->writeInt($this->intValue);
        return $writer->getBuffer();
    }

    public function doDecode($raw)
    {
        $reader = self::openReader($raw);
        $this->asciiValue = $reader->readASCII();
        $this->byteValue = $reader->readBytes();
        $this->utf8Value = $reader->readUTF8();
        $this->intValue = $reader->readInt();
        #var_dump("Int Val : " . $this->intValue);
    }
}