<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ChunkResponseFastbinMetas extends ChunkResponseFastbinBlocks
{
    public function getPacketId()
    {
        return self::PK_CHUNK_RESPONSE_FASTBIN_METAS;
    }
}