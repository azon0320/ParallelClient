<?php


namespace cn\dormao\mcpe\parallelclient\instance;


use cn\dormao\mcpe\parallelclient\ParallelClient;
use cn\dormao\mcpe\parallelclient\pocketmine\ParallelPocketmineChunk;
use pocketmine\block\Block;
use pocketmine\level\format\FullChunk;

class XZYFastbinParallelChunk extends XZYParallelChunk
{
    public function __construct(ParallelClient $client, $x, $z)
    {
        parent::__construct($client, $x, $z);
    }

    public function pocketmineApply(FullChunk $c)
    {
        $blockIn = new ByteSequenceReader($this->blocks);
        $blockmod = $blockIn->readShort();
        $metaIn = new ByteSequenceReader($this->metas);
        for ($i=0;$i<$blockmod;$i++){
            $index = $blockIn->readInt();
            $id = $blockIn->readASCIIi();
            $meta = $blockIn->readASCIIi();
            if (isset(Block::$list[$id])){

            }
        }
        if ($c instanceof ParallelPocketmineChunk){
            $c->setParallelChunkApplyed(true);
            $this->isApplied = true;
        }
    }

    /*
     * public void fromBukkitChunk(Chunk c){
     *     ByteSequenceWriter writer = new ByteSequenceWriter();
     *     int modTimes = 0;
     *     for (int x = 0;x < getMaxX();x++){
            for (int z=0;z < getMaxZ();z++){
                for (int y=0;y<getMaxHeight();y++){
                    Block block = c.getBlock(x,y,z);
                    int[] dat = bukkitGetBlockValue(block);
                    if (dat[0] != 0 || dat[1] != 0){
                        modTimes++;
                        writer.writeInt(getBlockIndex())
                        this.setBlockBiomeAt(x,z,block.getBiome().ordinal());
                    }
                }
            }
     * }
     */
}