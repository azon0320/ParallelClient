<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


use cn\dormao\mcpe\parallelclient\ParallelClient;
use cn\dormao\mcpe\parallelclient\pocketmine\block\filter\BlockFilter;
use cn\dormao\mcpe\parallelclient\pocketmine\block\filter\BlockFilter82;
use cn\dormao\mcpe\parallelclient\pocketmine\block\filter\BlockHelper;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

abstract class ParallelBlocks extends BlockHelper implements ParallelPocketmineBlock, BlockIds
{
    private function __construct(){}

    public static function playPlaceEvent(Player $player, Block $objOrhandOrThis, Block $objOrThisOrBlock, Block $srcOrTarget, Item $item, Level $l = null){
        if ($l != null) {
            $server = $l->getServer();
            if ($l->getProvider() instanceof ParallelClient){
                if ($server == null) {
                    trigger_error("Server is null!");
                } else $server->getPluginManager()->callEvent(new BlockPlaceEvent($player, $objOrhandOrThis, $objOrThisOrBlock, $srcOrTarget, $item));
            }
        }else trigger_error("Level null");
    }

    public static function fastPlayPlaceEvent(Vector3 $blockPos, Block $target, Item $item, Player $player){
        if ($player->getLevel()->getProvider() instanceof ParallelClient) {
            $server = $player->getServer();
            $block = $target->getLevel()->getBlock($blockPos);
            if ($block instanceof ParallelPocketmineBlock) $block->setPlaced();
            $server->getPluginManager()->callEvent(new BlockPlaceEvent($player, $block, $block, $target, $item));
        }
    }

    /** @return BlockFilter */
    private static function getFilter($protocol){
        #TODO Add BlockFilter check
    }

    /** @deprecated  */
    public static function filterBlock0_15_4Out($peId, $pemeta){
        return BlockFilter82::filtOutbound($peId,$pemeta);
    }

    /** @deprecated  */
    public static function filterBlock0_15_4In($javaId, $javaMeta){
        return BlockFilter82::filtInbound($javaId,$javaMeta);
    }

    /**
     * 用玄学的方法强行注册方块
     * 见@ParallelUtil::forceRegisterBlock
     * @deprecated Use protocol-specified BlockFilter::registerSupportedBlocks()
     */
    public static function init(){
        BlockFilter82::registerSupportedBlocks();
    }


}