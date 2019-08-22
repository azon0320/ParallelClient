<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ChunkResponseFastbinBlocks extends ChunkResponseBlocksPacket
{
    public function getPacketId()
    {
        return self::PK_CHUNK_RESPONSE_FASTBIN_BLOCKS;
    }
}