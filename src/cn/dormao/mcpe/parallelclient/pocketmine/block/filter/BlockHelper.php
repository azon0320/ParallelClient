<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block\filter;


abstract class BlockHelper implements BlockFilter
{
    public static function blockhash($id,$meta){
        return implode(':', [$id, $meta]);
    }

    /**
     * @param $hash
     * @return int[] array
     */
    public static function ablockhash($hash){
        $ints = explode(':',$hash);
        return [intval($ints[0]), intval($ints[1])];
    }
}