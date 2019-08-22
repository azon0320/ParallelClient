<?php


namespace cn\dormao\mcpe\parallelclient\instance;



use cn\dormao\mcpe\parallelclient\ParallelChunk;
use cn\dormao\mcpe\parallelclient\ParallelClient;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelBlocks;
use cn\dormao\mcpe\parallelclient\pocketmine\ParallelPocketmineBiome;
use cn\dormao\mcpe\parallelclient\pocketmine\ParallelPocketmineChunk;
use pocketmine\block\Block;
use pocketmine\level\format\FullChunk;
use pocketmine\level\generator\biome\Biome;

class XZYParallelChunk implements ParallelChunk
{

    /** @var string */
    protected $blocks;

    /** @var string */
    protected $metas;

    /** @var string */
    protected $biomes;

    /** @var ParallelClient */
    protected $client;

    /** @var int */
    protected $chunkX;

    /** @var int */
    protected $chunkZ;

    /** @var bool */
    protected $bool_blocks;
    /** @var bool */
    protected $bool_metas;
    /** @var bool */
    protected $bool_biomes;

    /** @var int */
    protected $lastActive;

    protected $creationTime;

    protected $isApplied;

    public function __construct(ParallelClient $client, $x, $z)
    {
        $this->clearAllData();
        $this->client = $client;
        $this->chunkX = $x;$this->chunkZ = $z;
        $this->bool_blocks = false;
        $this->bool_metas = false;
        $this->bool_biomes = false;
        $this->isApplied = false;
        $this->creationTime = microtime(true) * 1000;
        $this->chunkActive();
    }

    /** @return int */
    public function getOrder(){
        return self::ORDER_XZY;
    }

    public function clearAllData()
    {
        $this->blocks = str_repeat(chr(0), self::MAX_X * self::MAX_Z * $this->getMaxHeight());
        $this->metas = str_repeat(chr(0), self::MAX_X * self::MAX_Z * $this->getMaxHeight());
        $this->biomes = str_repeat(chr(Biome::PLAINS), self::MAX_X * self::MAX_Z);
    }

    public function getMaxHeight(){
        return self::MAX_Y_127;
    }

    public function getBlockIndex($x, $y, $z)
    {
        $indexY = self::MAX_X * self::MAX_Z * ($y % $this->getMaxHeight());
        $indexZ = self::MAX_Z * ($z % self::MAX_Z);
        return $indexY + $indexZ + $x;
    }

    public function getBlockBiomeIndex($x, $z)
    {
        $indexZ = self::MAX_Z * ($z % self::MAX_Z);
        return $indexZ + $x;
    }

    public function getBlockId($index)
    {
        $id = $this->blocks{$index};
        return ord($id);
    }
    public function getBlockIdXYZ($x, $y, $z)
    {
        return $this->getBlockId($this->getBlockIndex($x,$y,$z));
    }

    public function setBlockId($index, $id)
    {
        $this->chunkActive();
        $this->blocks{$index} = chr($id);
    }

    public function getBlockMeta($index)
    {
        $meta = $this->metas{$index};
        return ord($meta);
    }
    public function getBlockMetaXYZ($x, $y, $z)
    {
        return $this->getBlockMeta($this->getBlockIndex($x,$y,$z));
    }

    public function setBlockMeta($index, $meta)
    {
        $this->metas{$index} = chr($meta);
    }

    public function getFullBlock($index, &$id, &$meta)
    {
        $id = $this->getBlockId($index);
        $meta = $this->getBlockMeta($index);
    }

    public function getFullBlockXYZ($x,$y,$z,&$id,&$meta){
        $id = $this->getBlockIdXYZ($x,$y,$z);
        $meta = $this->getBlockMetaXYZ($x,$y,$z);
    }

    public function setFullBlock($index, $id, $meta)
    {
        $this->setBlockId($index,$id);
        $this->setBlockMeta($index, $meta);
    }

    public function setFullBlockXYZ($x, $y, $z, $id, $meta)
    {
        $this->setFullBlock($this->getBlockIndex($x,$y,$z),$id,$meta);
    }

    public function getBlockBiomeAt($x, $z)
    {
        $index = $this->getBlockBiomeIndex($x, $z);
        return ord($this->biomes{$index});
    }

    public function setBlockBiomeAt($x, $z, $biomeId)
    {
        $index = $this->getBlockBiomeIndex($x, $z);
        $this->biomes{$index} = chr($biomeId);
    }


    public function getBlocks(){$this->chunkActive();return $this->blocks;}

    public function setBlocks($blocksdata){$this->chunkActive();$this->blocks = $blocksdata;$this->bool_blocks = true;}

    public function getMetas(){$this->chunkActive();return $this->metas;}

    public function setMetas($metasdata){$this->chunkActive();$this->metas = $metasdata;$this->bool_metas = true;}

    public function getBiomes(){$this->chunkActive();return $this->biomes;}

    public function setBiomes($biomes){$this->chunkActive();$this->biomes = $biomes;$this->bool_biomes = true;}



    public function getChunkX(){return $this->chunkX;}

    public function getChunkZ(){return $this->chunkZ;}

    public function setChunkXZ($x, $z){$this->chunkActive();$this->chunkX = $x;$this->chunkZ = $z;}

    public function getParallelClient(){return $this->client;}

    public function setParallelClient($c){$this->chunkActive();$this->client = $c;}

    public function isBlocksAndMetasReady()
    {
        return $this->isChunkApplyReady();
    }

    public function isChunkApplyReady(){
        return $this->bool_blocks && $this->bool_metas;//&& $this->bool_biomes;
    }

    public function pocketmineApply(FullChunk $c){
        $period = microtime(true);
        for ($x = 0;$x < self::MAX_X;$x++){
            for ($y =0;$y < $this->getMaxHeight();$y++){
                for ($z=0;$z < self::MAX_Z; $z++){
                    $id = $this->getBlockIdXYZ($x,$y,$z);
                    if ($id != 0) {
                        $meta = $this->getBlockMetaXYZ($x, $y, $z);
                        if (isset(Block::$list[$id])) {
                            //$c->setBlock($x,$y,$z, $id == 0 ? null : $id, $meta == 0 ? null : $meta);
                            $ids = ParallelBlocks::filterBlock0_15_4In($id, $meta);
                            $c->setBlock($x, $y, $z, $ids[0],$ids[1]);
                        }else{
                            $c->setBlock($x,$y,$z,1, 0);
                        }
                        $biome = $this->getBlockBiomeAt($x, $z);
                        $color = Biome::getBiome(ParallelPocketmineBiome::filterBiome0_15_4($biome))->getColor();
                        $R = $color >> 16;
                        $G = ($color >> 8) & 0xff;
                        $B = $color & 0xff;
                        $c->setBiomeId($x, $z, $biome);
                        $c->setBiomeColor($x, $z, $R, $G, $B);
                    }
                }
            }
        }
        if ($c instanceof ParallelPocketmineChunk){
            $c->setParallelChunkApplyed(true);
            $this->isApplied = true;
        }
        $this->chunkActive();
        #var_dump(sprintf("Raw processed : %.4f", microtime(true) - $period));
        #Average apply time (blocks & metas apply) : 170ms - 610ms
        #Average apply time (blocks & metas filtered apply) : 98ms - 232ms
        #Average apply time (blocks & metas filtered & filter(non-memory)) 1.9s
    }

    public function isApplied(){return $this->isApplied;}


    public function lastActive(){
        return $this->lastActive;
    }

    public function chunkActive(){
        $this->lastActive = intval(microtime(true));
    }
}