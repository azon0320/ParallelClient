<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\netbase;


use cn\dormao\mcpe\parallelclient\pocketmine\ParallelPocketmineChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\format\mcregion\Chunk;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\LongTag;

class NetbaseChunk extends Chunk implements ParallelPocketmineChunk
{

    const NBT_KEY_APPLIED = "ParallelChunkApplyed";

    protected $chunkApplied = false;
    /**
     * @param bool $flag
     */
    public function setParallelChunkApplyed($flag)
    {
        //$this->nbt->ParallelChunkApplyed = new ByteTag("ParallelChunkApplyed", (int) ($flag ? 1 : 0));
        $this->chunkApplied = true;
    }

    public function isParallelChunkApplyed()
    {
        //return isset($this->nbt->ParallelChunkApplyed) &&  $this->nbt->ParallelChunkApplyed->getValue() > 1;
        return $this->chunkApplied;
    }

    public function __construct($level, $chunkX, $chunkZ, $nbt = null)
    {
        /*
        $this->setProvider($level);
        $p_x = $chunkX;$p_z = $chunkZ;
        $p_data = str_repeat("\x00", 16384);
        $p_blocks = $p_data . $p_data;
        $p_skyLight = str_repeat("\xff", 16384);
        $p_blockLight = $p_data;
        $p_heightMap = array_fill(0, 256, 0);
        $p_biomeColors = array_fill(0, 256, 0);
        $this->setX($p_x);$this->setZ($p_z);
        $this->blocks = $p_blocks;
        $this->blockLight = $p_blockLight;
        $this->skyLight = $p_skyLight;
        $this->heightMap = $p_heightMap;
        $this->biomeColors = $p_biomeColors;
        */
        parent::__construct($level,$nbt);
        //$this->setProvider($level);
        $this->setX($chunkX);$this->setZ($chunkZ);
        $this->chunkApplied = false;
    }

    public function getBlockId($x, $y, $z)
    {
        return parent::getBlockId($x, $y, $z); //do nothing
    }

    public function setBlockId($x, $y, $z, $id)
    {
        parent::setBlockId($x, $y, $z, $id); //do nothing
    }

    public function getBlockData($x, $y, $z)
    {
        return parent::getBlockData($x, $y, $z); //do nothing
    }

    public function setBlockData($x, $y, $z, $data)
    {
        parent::setBlockData($x, $y, $z, $data); //do nothing
    }

    public function getFullBlock($x, $y, $z)
    {
        return parent::getFullBlock($x, $y, $z); //do nothing
    }

    public function getBlock($x, $y, $z, &$blockId, &$meta = null)
    {
        parent::getBlock($x, $y, $z, $blockId, $meta);//do nothing
    }

    public function setBlock($x, $y, $z, $blockId = null, $meta = null)
    {
        #最底层的BlockSet代码，在这里不再存在事件调用
        return parent::setBlock($x, $y, $z, $blockId, $meta); //do nothing
    }

    public function getBlockSkyLight($x, $y, $z)
    {
        return parent::getBlockSkyLight($x, $y, $z); //do nothing
    }

    public function setBlockSkyLight($x, $y, $z, $level)
    {
        parent::setBlockSkyLight($x, $y, $z, $level); //do nothing
    }

    public function getBlockLight($x, $y, $z)
    {
        return parent::getBlockLight($x, $y, $z); //do nothing
    }

    public function setBlockLight($x, $y, $z, $level)
    {
        parent::setBlockLight($x, $y, $z, $level); //do nothing
    }

    public function getBlockIdColumn($x, $z)
    {
        return parent::getBlockIdColumn($x, $z); //do nothing
    }

    public function getBlockDataColumn($x, $z)
    {
        return parent::getBlockDataColumn($x, $z); //do nothing
    }

    public function getBlockSkyLightColumn($x, $z)
    {
        return parent::getBlockSkyLightColumn($x, $z); //do nothing
    }

    public function getBlockLightColumn($x, $z)
    {
        return parent::getBlockLightColumn($x, $z); //do nothing
    }

    public function isLightPopulated()
    {
        return true;#光照计算
    }

    public function setLightPopulated($value = 1)
    {
        #光照计算
    }

    public function isPopulated()
    {
        return $this->isGenerated();
    }

    public function setPopulated($value = 1)
    {
        $this->setGenerated($value);
    }

    public function isGenerated()
    {
        /*
        if(isset($this->nbt->TerrainGenerated)) {
            return $this->nbt->TerrainGenerated->getValue() > 0;
        }
        return false;
        */
        return true;
    }

    public function setGenerated($value = 1)
    {
        //$this->nbt->TerrainGenerated = new ByteTag("TerrainGenerated", (int) $value);
    }

    public static function fromFastBinary($data, LevelProvider $provider = null)
    {
        $mcrchunk = parent::fromFastBinary($data, $provider);
        return new NetbaseChunk(
            $provider,
            $mcrchunk->getX(), $mcrchunk->getZ(),
            $mcrchunk->nbt
        );
    }

    public function toFastBinary()
    {
        return parent::toFastBinary(); //do nothing
    }

    public static function fromBinary($data, LevelProvider $provider = null)
    {
        $mcrchunk = parent::fromBinary($data, $provider);
        return new NetbaseChunk(
            $provider,
            $mcrchunk->getX(), $mcrchunk->getZ(),
            $mcrchunk->nbt
        );
    }

    public function toBinary()
    {
        return parent::toBinary(); //do nothing
    }

    public function getNBT()
    {
        return parent::getNBT(); //do nothing
    }

    public static function getEmptyChunk($chunkX, $chunkZ, LevelProvider $provider = null){
        try{
            if ($provider != null && $provider instanceof Netbase){
                $chunk = new NetbaseChunk($provider,$chunkX,$chunkZ);
                $chunk->x = $chunkX;
                $chunk->z = $chunkZ;

                $chunk->data = str_repeat("\x00", 16384);
                $chunk->blocks = $chunk->data . $chunk->data;
                $chunk->skyLight = str_repeat("\xff", 16384);
                $chunk->blockLight = $chunk->data;

                $chunk->heightMap = array_fill(0, 256, 0);
                $chunk->biomeColors = array_fill(0, 256, 0);

                $chunk->nbt->V = new ByteTag("V", 1);
                $chunk->nbt->InhabitedTime = new LongTag("InhabitedTime", 0);
                $chunk->nbt->TerrainGenerated = new ByteTag("TerrainGenerated", 0);
                $chunk->nbt->TerrainPopulated = new ByteTag("TerrainPopulated", 0);
                $chunk->nbt->LightPopulated = new ByteTag("LightPopulated", 0);
                return $chunk;
            }
        }catch(\Throwable $e){
            var_dump($e->getMessage());
        }
        return null;
    }
}