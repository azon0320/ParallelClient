<?php


namespace cn\dormao\mcpe\parallelclient\instance;


class ByteSequenceWriter extends ByteSequence
{
    public function __construct()
    {
        $this->buffer = "";
    }

    public function writeASCII($dat)
    {
        parent::writeASCII0($dat);return $this;
    }

    public function writeShort($num)
    {
        $this->writeByteLength0($num);
    }

    public function writeBytes($dat)
    {
        parent::writeBytes0($dat);return $this;
    }

    public function writeUTF8($dat)
    {
        parent::writeUTF80($dat);return $this;
    }

    public function writeInt($num)
    {
        $this->writeInt0($num);return $this;
    }

    public function sub($len = 1)
    {
        parent::sub0($len);return $this;
    }
}