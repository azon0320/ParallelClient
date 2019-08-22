<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use pocketmine\block\Block;
use pocketmine\block\WoodStairs;
use pocketmine\item\Item;
use pocketmine\Player;

class ParallelStairOak extends WoodStairs implements ParallelPocketmineBlock
{
    protected $placed = false;

    public function canProcessEvent()
    {
        return $this->placed;
    }

    public function setPlaced()
    {
        $this->placed = true;return $this;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $f = parent::place($item, $block, $target, $face, $fx, $fy, $fz, $player);
        if ($f){
            if ($player == null){
                #plugin block set
            }else{
                $this->setPlaced();
                ParallelBlocks::playPlaceEvent($player,$this,$this,$target,$item,$this->getLevel());
            }
        }
        return $f;
    }
}