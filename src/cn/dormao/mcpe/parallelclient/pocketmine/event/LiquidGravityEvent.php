<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\event;


use pocketmine\block\Block;
use pocketmine\event\block\BlockEvent;

class LiquidGravityEvent extends BlockEvent
{
    public static $handlerList = null;

    public $affectedBlock;

    public function __construct(Block $block, Block $neighbour)
    {
        parent::__construct($block);
        $this->affectedBlock = $neighbour;
    }

    /**
     * @return Block
     */
    public function getAffectedBlock()
    {
        return $this->affectedBlock;
    }
}