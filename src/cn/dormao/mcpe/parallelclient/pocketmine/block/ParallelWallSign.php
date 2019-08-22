<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use pocketmine\block\Block;
use pocketmine\block\WallSign;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class ParallelWallSign extends WallSign implements ParallelPocketmineBlock
{
    protected $placed = false;

    public function canProcessEvent()
    {
        return $this->placed;
    }

    public function setPlaced()
    {
        $this->placed = true; return $this;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        $f = parent::place($item, $block, $target, $face, $fx, $fy, $fz, $player);
        if ($f){
            if ($player == null){
                #plugin set
            }else{
                $this->placed = true;
                $faces = [
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ];
                $newblock = isset($faces[$face]) ? (new ParallelWallSign($this->meta))->setPlaced() : (new ParallelSign($this->meta))->setPlaced();
                $newblock->setComponents($this->x,$this->y,$this->z);
                $newblock->setLevel($this->getLevel());
                if ($this->getLevel() != null) {
                    ParallelBlocks::playPlaceEvent($player, $newblock, $newblock, $target, $item, $this->getLevel());
                }
            }
        }
        return $f;
    }

    public function onUpdate($type){
        $faces = [
            2 => 3,
            3 => 2,
            4 => 5,
            5 => 4,
        ];
        if($type === Level::BLOCK_UPDATE_NORMAL){
            if(isset($faces[$this->meta])) {
                $id = $this->getSide($faces[$this->meta])->getId();
                if ($id === self::AIR) {
                    $this->getLevel()->useBreakOn($this);
                }
                return Level::BLOCK_UPDATE_NORMAL;
            }
        }
        return false;
    }
}