<?php


namespace cn\dormao\mcpe\parallelclient;


use pocketmine\level\format\LevelProvider;

interface ParallelClient
{
    /**
     * @return string
     */
    function getParallelAddress();

    /**
     * @return int
     */
    function getParallelPort();

    function closeParallel();

    function clearParallelChunks();

    function loadParallelChunk($x,$z);

    /**
     * @param int $x
     * @param int $z
     * @return ParallelChunk|null
     */
    function getParallelChunk($x,$z);

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return ParallelChunk|null
     */
    function getParallelChunkAt($x,$y,$z);

    /**
     * @param int $x
     * @param int $z
     * @param ParallelChunk $chunk
     */
    function setParallelChunk($x,$z, ParallelChunk $chunk);

    function unloadParallelChunk($x,$z);

    /**
     * @return ParallelSocket
     */
    function getParallelSocket();

    /**
     * @return bool
     */
    function isParallelSocketRunning();

    /**
     * @param ParallelPacket $packet
     */
    function sendParallelPacket(ParallelPacket $packet);

    function asyncLoadParallelChunk($x,$z);

    function processParallelData();

    /**
     * @return LevelProvider
     */
    function pocketmineProvider();

    /** @return string */
    function getRemoteWorld();

    /**
     * called by PluginTask(period=1)
     * @param int $tick
     */
    function doParallelTick($tick=0);

    function gc();
}