<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


use cn\dormao\mcpe\parallelclient\instance\ByteSequenceReader;
use cn\dormao\mcpe\parallelclient\instance\ByteSequenceWriter;
use cn\dormao\mcpe\parallelclient\ParallelPacket;

abstract class AbstractParallelPacket implements ParallelPacket
{
    public function getChrId()
    {
        return chr($this->getPacketId());
    }

    /** @return string */
    protected abstract function onEncode();

    public function getEncoded()
    {
        return $this->getChrId() . $this->onEncode();
    }

    public function requireSendIndependent()
    {
        return false;
    }

    /**
     * @param string $raw
     * @param int $len
     * @return string
     */
    public static function shiftPacket($raw, $len = 1)
    {
        return substr($raw, $len);
    }

    /**
     * @param string $raw
     * @return ByteSequenceReader
     */
    public static function openReader($raw){
        return new ByteSequenceReader($raw);
    }

    public static function openWriter(){
        return new ByteSequenceWriter();
    }


    public static $packets = [];

    /**
     * @param int $pid
     * @param string $packetclass
     */
    public static function registerPacket($pid, $packetclass){
        self::$packets[$pid] = $packetclass;
    }

    /**
     * @param string $raw
     * @return ParallelPacket|null
     */
    public static function getPacket($raw){
        $id = ord($raw{0});
        $raw = substr($raw, 1);
        $obj = null;
        if (isset(self::$packets[$id])){
            $cls = self::$packets[$id];
            /** @var ParallelPacket $obj */
            $obj = new $cls();
            try{
                $obj->doDecode($raw);
            }catch (\Exception $e){
                $obj = null;
            }
        }
        return $obj;
    }

    public static function registerAll(){
        self::registerPacket(self::PK_CLOSE, ClosePacket::class);
        self::registerPacket(self::PK_BATCHED, BatchedPacket::class);
        self::registerPacket(self::PK_ERROR, ErrorPacket::class);
        self::registerPacket(self::PK_WORLD_SET, WorldSetPacket::class);
        self::registerPacket(self::PK_TEST, TestPacket::class);
        self::registerPacket(self::PK_CHUNK_REQUEST, ChunkRequestPacket::class);
        self::registerPacket(self::PK_GET_SPAWN, GetSpawnPacket::class);
        self::registerPacket(self::PK_SET_SPAWN,SetSpawnPacket::class);
        self::registerPacket(self::PK_CHUNK_RESPONSE_BLOCKS, ChunkResponseBlocksPacket::class);
        self::registerPacket(self::PK_CHUNK_RESPONSE_METAS, ChunkResponseMetasPacket::class);
        self::registerPacket(self::PK_CHUNK_RESPONSE_BIOMES, ChunkResponseBiomesPacket::class);
        self::registerPacket(self::PK_CHUNK_RESPONSE_FASTBIN_BLOCKS, ChunkResponseFastbinBlocks::class);
        self::registerPacket(self::PK_CHUNK_RESPONSE_FASTBIN_METAS, ChunkResponseFastbinMetas::class);
        self::registerPacket(self::PK_WORLD_SET_BLOCK, WorldSetBlockPacket::class);
        self::registerPacket(self::PK_WORLD_TIME, WorldTimePacket::class);
    }
}