<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine;


interface ParallelPocketmineChunk
{
    /** @param bool */
    function setParallelChunkApplyed($flag);

    /** @return bool */
    function isParallelChunkApplyed();
}