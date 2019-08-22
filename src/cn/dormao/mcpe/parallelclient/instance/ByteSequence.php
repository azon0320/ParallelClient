<?php


namespace cn\dormao\mcpe\parallelclient\instance;


use cn\dormao\mcpe\parallelclient\ParallelSequence;
use cn\dormao\mcpe\parallelclient\ParallelUtil;

abstract class ByteSequence implements ParallelSequence
{

    protected $buffer;

    protected function writeASCII0($dat)
    {
        $dat = is_string($dat) ? $dat : chr(is_int($dat) ? $dat : 0);
        $this->buffer .= $dat;
    }

    protected function writeByteLength0($len)
    {
        $this->buffer .= ParallelUtil::shortToByteLen($len);
    }

    protected function writeBytes0($dat)
    {
        $this->writeByteLength0(strlen($dat));
        $this->buffer .= $dat;
    }

    protected function writeUTF80($dat)
    {
        $this->writeByteLength0(strlen($dat));
        $this->buffer .= $dat;
    }

    protected function writeInt0($num){
        $this->buffer .= ParallelUtil::intToBytes($num);
    }

    protected function readASCII0()
    {
        $d = $this->buffer{0};
        $this->sub0();
        return $d;
    }

    protected function readByteLength0()
    {
        $b1 = $this->buffer{0};
        $b2 = $this->buffer{1};
        $this->sub0(2);
        return ParallelUtil::byteLenToShort($b1, $b2);
    }

    protected function readUTF80()
    {
        $len = $this->readByteLength0();
        $str = substr($this->buffer, 0, $len);
        $this->sub0($len);
        return $str;
    }

    protected function readInt0(){
        $i = ParallelUtil::byteLenToInt($this->buffer{0},$this->buffer{1},$this->buffer{2},$this->buffer{3});
        $this->sub0(4);
        return $i;
    }

    protected function readBytes0()
    {
        return $this->readUTF80();
    }

    protected function sub0($len = 1)
    {
        $this->buffer = substr($this->buffer,$len);
    }

    /**
     * @return mixed
     */
    public function getBuffer()
    {
        return $this->buffer;
    }


    public function writeASCII($dat)
    {

    }

    public function writeShort($num)
    {

    }

    public function writeBytes($dat)
    {

    }

    public function writeUTF8($dat)
    {

    }

    public function readASCII()
    {
        return '';
    }

    public function readShort()
    {

    }

    public function readASCIIi(){
        return $this->readASCII();
    }

    public function readBytes()
    {
        return '';
    }

    public function readUTF8()
    {
        return '';
    }

    public function sub($len = 1)
    {

    }

    public function readInt()
    {
        return 0;
    }

    public function writeInt($num)
    {

    }
}