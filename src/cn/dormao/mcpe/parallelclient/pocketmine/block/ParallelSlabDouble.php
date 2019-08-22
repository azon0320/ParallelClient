<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use pocketmine\block\DoubleSlab;

class ParallelSlabDouble extends DoubleSlab implements ParallelPocketmineBlock
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
}