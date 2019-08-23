<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;


use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelBlocks;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use pocketmine\block\Block;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\Player;

/**
 * Class ParallelBlockSolid
 * @package cn\dormao\mcpe\parallelclient\pocketmine\block2
 * @deprecated
 */
class ParallelBlockSolid extends Solid implements ParallelPocketmineBlock
{
    public $placed = false;
    public function canProcessEvent(){return $this->placed;}
    public function setPlaced(){$this->placed = true;return $this;}

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $f = parent::place($item, $block, $target, $face, $fx, $fy, $fz, $player);
        if ($player != null){
            $this->setPlaced();
            ParallelBlocks::playPlaceEvent($player,$this,$this,$target,$item,$this->getLevel());
        }
        return $f;
    }
}