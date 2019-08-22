<?php


namespace cn\dormao\mcpe\parallelclient\instance;


class ByteSequenceReader extends ByteSequence
{
    /**
     * ByteSequenceReader constructor.
     * @param string $buffer
     */
    public function __construct($buffer)
    {
        $this->buffer = $buffer;
    }

    public function readASCII()
    {
        return parent::readASCII0();
    }

    public function readASCIIi(){
        return ord($this->readASCII());
    }

    public function readShort()
    {
        return $this->readByteLength0();
    }

    public function readBytes()
    {
        return parent::readBytes0();
    }

    public function readUTF8()
    {
        return parent::readUTF80();
    }

    public function readInt()
    {
        return $this->readInt0();
    }
}