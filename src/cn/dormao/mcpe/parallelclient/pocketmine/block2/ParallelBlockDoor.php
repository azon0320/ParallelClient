<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;


use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use pocketmine\block\Door;

/**
 * Class ParallelBlockDoor
 * @package cn\dormao\mcpe\parallelclient\pocketmine\block2
 * @deprecated
 */
class ParallelBlockDoor extends Door implements ParallelPocketmineBlock
{
    public $placed = false;
    public function canProcessEvent(){return $this->placed;}
    public function setPlaced(){$this->placed = true;return $this;}
}