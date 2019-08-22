<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine;


abstract class ParallelPocketmineBiome
{

    #PLAINS
    const DEFAULT_BIOME = 1;

    public static function filterBiome0_15_4($biomeId = 1){
        return in_array($biomeId, [0,1,2,3,4,5,6,7,8,12,20,27]) ? $biomeId : self::DEFAULT_BIOME;
    }
}