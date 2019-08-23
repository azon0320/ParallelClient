<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block\filter;


use pocketmine\block\Block;
use pocketmine\block\BlockIds;

interface BlockFilter extends BlockIds
{
    #TODO All Platform Block Ids put here


    /** @return array */
    static function getSupportedVersions();
    /** @return array */
    static function getOutboundFilter();
    /** @return array */
    static function getInboundFilter();

    static function filtInbound($javaId, $javaMeta);
    static function filtOutbound($peId, $peMeta);

    static function canPlace(Block $b);

    static function registerSupportedBlocks();
}