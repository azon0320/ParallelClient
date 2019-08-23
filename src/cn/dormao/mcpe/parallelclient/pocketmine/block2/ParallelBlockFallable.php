<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;


use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use pocketmine\block\Fallable;

/**
 * Class ParallelBlockFallable
 * @package cn\dormao\mcpe\parallelclient\pocketmine\block2
 * @deprecated
 */
class ParallelBlockFallable extends Fallable implements ParallelPocketmineBlock
{
    public $placed = false;
    public function canProcessEvent(){return $this->placed;}
    public function setPlaced(){$this->placed = true;return $this;}
}