<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use pocketmine\block\Block;
use pocketmine\block\Torch;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class ParallelTorch extends Torch implements ParallelPocketmineBlock
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
                #plugin place status
                #Process world set packet
                var_dump(self::WARN_PLUGIN_SETBLOCK);
            }else{
                $this->placed = true;
                $l = $this->getLevel();
                if ($l != null) {
                    //player, hand, block, target
                    //block为新Block位置
                    //target为产生新Block的面的前Block位置
                    //hand为Player手中的Item对应的方块
                    $e = new BlockPlaceEvent($player, $this, $this, $target, $item);
                    $l->getServer()->getPluginManager()->callEvent($e);
                    #var_dump("Event Executed PLACED : " . ($this->placed ? "TRUE" : "FALSE"));
                }else trigger_error(self::WARN_LEVEL_NULL);
            }
        }
        return $f;
    }

    public function onUpdate($type)
    {
        return false;
    }
}