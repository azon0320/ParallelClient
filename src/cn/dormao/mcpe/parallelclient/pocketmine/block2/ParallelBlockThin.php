<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;


/**
 * Class ParallelBlockThin
 * @package cn\dormao\mcpe\parallelclient\pocketmine\block2
 * @deprecated
 */
class ParallelBlockThin
{
    public $placed = false;
    public function canProcessEvent(){return $this->placed;}
    public function setPlaced(){$this->placed = true;return $this;}
}