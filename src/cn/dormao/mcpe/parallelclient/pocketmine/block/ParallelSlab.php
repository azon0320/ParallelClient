<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use pocketmine\block\Block;
use pocketmine\block\Slab;
use pocketmine\item\Item;
use pocketmine\Player;

class ParallelSlab extends Slab implements ParallelPocketmineBlock
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
            if ($player != null && $this->getLevel() != null){
                $block = $this->getLevel()->getBlock($block);
                if ($block->getId() == self::AIR) $block = $this->getLevel()->getBlock($target);
                ParallelBlocks::fastPlayPlaceEvent($block,$target,$item,$player);
            }
        }
        return $f;
    }
}