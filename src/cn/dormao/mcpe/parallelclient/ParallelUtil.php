<?php


namespace cn\dormao\mcpe\parallelclient;


use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginManager;

abstract class ParallelUtil
{

    /**
     * @param int $x
     * @param int $z
     * @return string
     */
    public static function chunkHash($x, $z){
        return $x . ',' . $z;
    }

    /**
     * @param string $hash
     * @return int[]
     */
    public static function achunkHash($hash){
        return explode(",", $hash);
    }

    /**
     * @param Vector3 $v
     * @return string
     */
    public static function vec3Hash(Vector3 $v){
        return $v->getX() . ',' . $v->getY() . ',' . $v->getZ();
    }

    /**
     * @param string $str
     * @return Vector3
     */
    public static function avec3Hash($str){
        $nodes = explode(",", $str);
        $len = count($nodes);
        return new Vector3(
            $len > 0 ? doubleval($nodes[0]) : 0.0,
            $len > 1 ? doubleval($nodes[1]) : 0.0,
            $len > 2 ? doubleval($nodes[2]) : 0.0
        );
    }

    /**
     * @param $num
     * @return string
     * @deprecated Please use shortToByteLen(int)
     */
    public static function countForByteLen($num){
        return self::shortToByteLen($num);
    }

    /**
     * 2 bytes
     * 256 * 256 = 65535
     * 一个用来表示短整型的整数
     * 网络最大接收65535
     * @param int $num
     * @return string
     */
    public static function shortToByteLen($num){
        $byte1 = intval($num / 256);
        $byte2 = intval($num % 256);
        return chr($byte1) . chr($byte2);
    }

    /**
     * @param string $b1
     * @param string $b2
     * @return int
     */
    public static function byteLenToShort($b1, $b2){
        return ord($b1) * 256 + ord($b2);
    }

    public static function intToBytes($num){
        $byte1 = intval(($num / pow(256,3)) % 256);
        $byte2 = intval(($num / pow(256,2)) % 256);
        $byte3 = intval(($num / pow(256,1)) % 256);
        $byte4 = intval($num % 256);
        return chr($byte1) . chr($byte2) . chr($byte3) . chr($byte4);
    }

    public static function byteLenToInt($b1,$b2,$b3,$b4){
        return ord($b1) * pow(256,3) + ord($b2) * pow(256,2) + ord($b3) * pow(256,1) + ord($b4);
    }

    public static function payloadAllEmpty($str){
        for ($i=0;$i<strlen($str);$i++){
            if (ord($str{$i}) != 0) return false;
        }
        return true;
    }

    /**
     * Block::$list有一个基于Id的简单Block Class表
     * Block::$fullList是精确版的Block Class表
     * Item::$list的Block部分(<256)有一个Block Class表
     * Generator中的Block::init()仅在线程生命周期有效，并不影响到主线程的Block Class表
     * @param int 方块Id
     * @param string 修改的方块Class
     */
    public static function forceRegisterBlock($id, $blockclass){
        Block::$list[$id] = $blockclass;
        for($data = 0; $data < 16; ++$data){
            Block::$fullList[($id << 4) | $data] = new $blockclass($data);
        }
        Item::$list[$id] = $blockclass;
    }

    public static function updateAroundNonEvent(PluginManager $pm,Block $block){
        if ($block instanceof ParallelPocketmineBlock) $block->onUpdate(Level::BLOCK_UPDATE_NORMAL);
        $sides = [Vector3::SIDE_DOWN,Vector3::SIDE_UP,Vector3::SIDE_EAST,Vector3::SIDE_WEST,Vector3::SIDE_SOUTH,Vector3::SIDE_NORTH];
        foreach ($sides as $side){
            $blk = $block->getSide($side);
            $ev = new BlockUpdateEvent($blk);
            $pm->callEvent($ev);
            if (!$ev->isCancelled()) $blk->onUpdate(Level::BLOCK_UPDATE_NORMAL);
        }
    }
}