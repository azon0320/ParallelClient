<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ChunkResponseMetasPacket extends ChunkResponseBlocksPacket
{
    public function getPacketId()
    {
        return self::PK_CHUNK_RESPONSE_METAS;
    }
}