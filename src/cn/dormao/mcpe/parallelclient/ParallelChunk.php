<?php


namespace cn\dormao\mcpe\parallelclient;


use pocketmine\level\format\FullChunk;

interface ParallelChunk
{
    const ORDER_XZY = 0;

    const MAX_Y_127 = 127;
    const MAX_X = 16;
    const MAX_Z = 16;


    /**
     * @param ParallelClient|null $c
     */
    function setParallelClient($c);

    /**
     * @return ParallelClient
     */
    function getParallelClient();

    /**
     * 清除所有方块
     */
    function clearAllData();

    /**
     * @return int
     */
    function getOrder();

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    function getBlockIndex($x,$y,$z);

    /**
     * @param int $x
     * @param int $z
     * @return int
     */
    function getBlockBiomeIndex($x, $z);

    /**
     * @return int
     */
    function getMaxHeight();

    /**
     * @param int $index
     * @return int
     */
    function getBlockId($index);

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    function getBlockIdXYZ($x,$y,$z);

    /**
     * @param int $index
     * @param int $id
     */
    function setBlockId($index,$id);

    /**
     * @param int $index
     * @return int
     */
    function getBlockMeta($index);

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    function getBlockMetaXYZ($x,$y,$z);

    /**
     * @param int $index
     * @param int $meta
     */
    function setBlockMeta($index, $meta);

    /**
     * @param int $index
     * @param $id
     * @param $meta
     */
    function getFullBlock($index, &$id, &$meta);

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $id
     * @param int $meta
     */
    function getFullBlockXYZ($x,$y,$z,&$id,&$meta);

    /**
     * @param int $index
     * @param int $id
     * @param int $meta
     */
    function setFullBlock($index, $id, $meta);

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @param int $id
     * @param int $meta
     */
    function setFullBlockXYZ($x,$y,$z,$id,$meta);

    /**
     * @param int $x
     * @param int $z
     * @param int $biomeId
     */
    function setBlockBiomeAt($x, $z, $biomeId);

    /**
     * @param int $x
     * @param int $z
     * @return int
     */
    function getBlockBiomeAt($x,$z);

    /** @return string */
    function getBlocks();
    /** @param string */
    function setBlocks($blocksdata);

    /** @return string */
    function getMetas();
    /** @param string */
    function setMetas($metasdata);

    /** @return string */
    function getBiomes();
    /** @param string */
    function setBiomes($biomes);

    /**
     * @return int
     */
    function getChunkX();

    /**
     * @return int
     */
    function getChunkZ();

    function setChunkXZ($x, $z);

    /**
     * Returns TRUE when blocks & metas is set from network packet
     * @return bool
     * @deprecated Please use the method isChunkApplyReady()
     */
    function isBlocksAndMetasReady();

    /**
     * @return bool
     */
    function isChunkApplyReady();

    /**
     * @param FullChunk $fchunk
     */
    function pocketmineApply(FullChunk $fchunk);

    function isApplied();

    /**
     * @return int second
     */
    function lastActive();

    function chunkActive();
}