<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;


use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use pocketmine\block\Crops;

/**
 * Class ParallelBlockCrops
 * @package cn\dormao\mcpe\parallelclient\pocketmine\block2
 * @deprecated
 */
class ParallelBlockCrops extends Crops implements ParallelPocketmineBlock
{
    public $placed = false;
    public function canProcessEvent(){return $this->placed;}
    public function setPlaced(){$this->placed = true;return $this;}
}