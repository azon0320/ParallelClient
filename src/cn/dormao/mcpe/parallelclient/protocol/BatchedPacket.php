<?php


namespace cn\dormao\mcpe\parallelclient\protocol;



class BatchedPacket extends AbstractParallelPacket
{

    const BATCH_MAX_PACKETS = 99;

    public function getPacketId()
    {
        return self::PK_BATCHED;
    }

    /** @var string[] */
    public $packetPayloads = [];

    //[char1 : id][char1 : count][5 : length][payload][5 : length][payload]
    protected function onEncode()
    {
        $count = count($this->packetPayloads);
        $writer = self::openWriter();
        $writer->writeASCII($count);
        foreach ($this->packetPayloads as $pkDat) {
            $writer->writeBytes($pkDat);
        }
        return $writer->getBuffer();
    }

    public function requireSendIndependent()
    {
        return true;
    }

    public function doDecode($raw)
    {
        #$len = strlen($raw) + 1;
        $in = self::openReader($raw);
        $count = ord($in->readASCII());
        #var_dump(sprintf("batched count : %d  Total length : %d", $count,$len));
        $pks = [];

        for ($i=0;$i<$count;$i++){
            $pks[] = $in->readBytes();
        }
        $this->packetPayloads = $pks;
    }
}