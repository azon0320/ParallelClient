<?php


namespace cn\dormao\mcpe\parallelclient\protocol;



class ChunkRequestPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_CHUNK_REQUEST;
    }

    /** @var int */
    public $chunkx;
    /** @var int */
    public $chunkz;

    protected function onEncode()
    {
        return self::openWriter()->writeUTF8($this->chunkx . ',' . $this->chunkz)->getBuffer();
    }

    public function doDecode($raw)
    {
        $is = self::openReader($raw);
        $r = explode(",", $is->readUTF8());
        $this->chunkx = $r[0];$this->chunkz = $r[1];
    }
}