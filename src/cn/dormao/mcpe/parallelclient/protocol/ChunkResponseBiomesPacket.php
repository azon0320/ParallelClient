<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class ChunkResponseBiomesPacket extends ChunkResponseBlocksPacket
{
    public function getPacketId()
    {
        return self::PK_CHUNK_RESPONSE_BIOMES;
    }
}