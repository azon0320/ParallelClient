<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


use cn\dormao\mcpe\parallelclient\ParallelUtil;

class ChunkResponseBlocksPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_CHUNK_RESPONSE_BLOCKS;
    }

    /** @var int */
    public $chunkx;
    /** @var int */
    public $chunkz;
    /** @var string */
    public $payload;

    protected function onEncode()
    {
        return self::openWriter()
            ->writeUTF8(ParallelUtil::chunkHash($this->chunkx, $this->chunkz))
            ->writeBytes($this->payload)
            ->getBuffer();
    }

    public function doDecode($raw)
    {
        $is = self::openReader($raw);
        $utf = $is->readUTF8();
        $node = ParallelUtil::achunkHash($utf);
        $this->chunkx = intval($node[0]);
        $this->chunkz = intval($node[1]);
        $this->payload = $is->readBytes();
    }
}