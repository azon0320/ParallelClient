<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block\filter;


use cn\dormao\mcpe\parallelclient\ParallelUtil;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelAnvil;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelFurnace;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelHayBale;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelJackOLantern;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelLadder;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPumpkin;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelQuartz;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelSign;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelSlab;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelSlabDouble;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelSlabWood;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelSlabWoodDouble;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairAcacia;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairBirch;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairBrick;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairCobble;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairDarkOak;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairJungle;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairNetherBrick;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairOak;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairQuartz;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairRedSandStone;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairSandStone;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairSpruce;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelStairStoneBrick;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelTorch;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelWallSign;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelWood;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelWood2;
use cn\dormao\mcpe\parallelclient\pocketmine\block2\Block2Helper82;
use pocketmine\block\Block;
use pocketmine\block\Door;
use pocketmine\block\Flowable;
use pocketmine\block\Slab;

class BlockFilter82 extends BlockHelper
{
    public static function getSupportedVersions()
    {
        return ["0.15.4"];
    }

    public static $inbound = [];
    public static $outbound = [];

    public static function getOutboundFilter(){
        return self::$outbound;
    }

    public static function getInboundFilter()
    {
        return self::$inbound;
    }

    public static function canPlace(Block $b){
        #TODO Door Process
        $f = !($b instanceof Flowable) && !($b instanceof Door);
        switch ($b->getId()){
            case 106: #VINE
            case 46: #TNT
                $f = false;
        }
        return $f;
    }

    public static function registerSupportedBlocks()
    {
        ParallelUtil::forceRegisterBlock(self::TORCH, ParallelTorch::class);
        ParallelUtil::forceRegisterBlock(self::SIGN_POST, ParallelSign::class);
        ParallelUtil::forceRegisterBlock(self::WALL_SIGN, ParallelWallSign::class);
        ParallelUtil::forceRegisterBlock(self::SLAB,ParallelSlab::class);
        ParallelUtil::forceRegisterBlock(self::WOOD_SLAB, ParallelSlabWood::class);
        ParallelUtil::forceRegisterBlock(self::DOUBLE_SLAB, ParallelSlabDouble::class);
        ParallelUtil::forceRegisterBlock(self::DOUBLE_WOOD_SLAB, ParallelSlabWoodDouble::class);
        ParallelUtil::forceRegisterBlock(self::WOOD_STAIRS, ParallelStairOak::class);
        ParallelUtil::forceRegisterBlock(self::JUNGLE_WOODEN_STAIRS, ParallelStairJungle::class);
        ParallelUtil::forceRegisterBlock(self::ACACIA_WOODEN_STAIRS, ParallelStairAcacia::class);
        ParallelUtil::forceRegisterBlock(self::BIRCH_WOODEN_STAIRS, ParallelStairBirch::class);
        ParallelUtil::forceRegisterBlock(self::SPRUCE_WOODEN_STAIRS, ParallelStairSpruce::class);
        ParallelUtil::forceRegisterBlock(self::DARK_OAK_WOODEN_STAIRS, ParallelStairDarkOak::class);
        ParallelUtil::forceRegisterBlock(self::BRICK_STAIRS, ParallelStairBrick::class);
        ParallelUtil::forceRegisterBlock(self::SANDSTONE_STAIRS, ParallelStairSandStone::class);
        ParallelUtil::forceRegisterBlock(self::STONE_BRICK_STAIRS, ParallelStairStoneBrick::class);
        ParallelUtil::forceRegisterBlock(self::COBBLESTONE_STAIRS, ParallelStairCobble::class);
        ParallelUtil::forceRegisterBlock(self::NETHER_BRICK_STAIRS, ParallelStairNetherBrick::class);
        ParallelUtil::forceRegisterBlock(self::QUARTZ_STAIRS, ParallelStairQuartz::class);
        ParallelUtil::forceRegisterBlock(self::RED_SANDSTONE_STAIRS, ParallelStairRedSandStone::class);
        ParallelUtil::forceRegisterBlock(self::QUARTZ_BLOCK, ParallelQuartz::class);
        ParallelUtil::forceRegisterBlock(self::LADDER, ParallelLadder::class);
        ParallelUtil::forceRegisterBlock(self::ANVIL, ParallelAnvil::class);
        ParallelUtil::forceRegisterBlock(self::PUMPKIN, ParallelPumpkin::class);
        ParallelUtil::forceRegisterBlock(self::JACK_O_LANTERN, ParallelJackOLantern::class);
        ParallelUtil::forceRegisterBlock(self::HAY_BALE, ParallelHayBale::class);
        ParallelUtil::forceRegisterBlock(self::WOOD, ParallelWood::class);
        ParallelUtil::forceRegisterBlock(self::WOOD2, ParallelWood2::class);
        ParallelUtil::forceRegisterBlock(self::FURNACE, ParallelFurnace::class);
        #Vine is error placing on the solid block
        #ParallelUtil::forceRegisterBlock(self::VINE,ParallelVine::class);
        #Skull Block needs NBT
        #ParallelUtil::forceRegisterBlock(self::SKULL_BLOCK, ParallelSkull::class);
        self::registerInbound();self::registerOutbound();
    }

    private static function registerInbound(){
        self::$inbound = [];
        $general = [
            144 => self::STONE, #Skull不支持
            54 => self::STONE, #TODO 添加箱子支持
            146 => self::STONE, #陷阱箱不支持
            125 => self::DOUBLE_WOOD_SLAB, #转码
            126 => self::WOOD_SLAB, #转码
            158 => self::DROPPER #转码
        ];
        foreach ($general as $id => $vid){
            for ($i=0;$i<16;$i++){
                self::$inbound[self::blockhash($id,$i)] = self::blockhash($vid, $i);
            }
        }
        self::$inbound[self::blockhash(self::SLAB,Slab::NETHER_BRICK)] = self::blockhash(self::SLAB, Slab::QUARTZ);
        self::$inbound[self::blockhash(self::SLAB,Slab::NETHER_BRICK + 8)] = self::blockhash(self::SLAB, Slab::QUARTZ+8);
        self::$inbound[self::blockhash(self::SLAB, Slab::QUARTZ)] = self::blockhash(self::SLAB, Slab::NETHER_BRICK);
        self::$inbound[self::blockhash(self::SLAB, Slab::QUARTZ + 8)] = self::blockhash(self::SLAB, Slab::NETHER_BRICK+8);
        self::$inbound[self::blockhash(self::DOUBLE_SLAB, Slab::QUARTZ)] = self::blockhash(self::DOUBLE_SLAB, Slab::NETHER_BRICK);
        self::$inbound[self::blockhash(self::DOUBLE_SLAB, Slab::NETHER_BRICK)] = self::blockhash(self::DOUBLE_SLAB, Slab::QUARTZ);
        self::$inbound[self::blockhash(self::QUARTZ_BLOCK, 4)] = self::blockhash(self::QUARTZ_BLOCK, 10);
        self::$inbound[self::blockhash(self::QUARTZ_BLOCK, 3)] = self::blockhash(self::QUARTZ_BLOCK, 6);
        self::$inbound[self::blockhash(self::GRASS_PATH,0)] = self::blockhash(208, 0);
    }

    private static function registerOutbound(){
        self::$outbound = [];
        $general = [
            self::SKULL_BLOCK => 1, #Skull不支持，需要NBT
            self::CHEST => 1, #TODO 增加箱子支持
            self::TRAPPED_CHEST => 1, #陷阱箱不支持
            self::DOUBLE_WOOD_SLAB => 125, #转码
            self::WOOD_SLAB => 126, #转码
            self::STONECUTTER => 1, #Java不支持切石机,
            self::NETHER_REACTOR => 1, #Java不支持反应核
            self::VINE => 1, #Vine update() error
        ];
        foreach ($general as $k => $v){
            for($i=0;$i<16;$i++){
                self::$outbound[self::blockhash($k,$i)] = self::blockhash($v, $i);
            }
        }
        self::$outbound[self::blockhash(self::SLAB,Slab::NETHER_BRICK)] = self::blockhash(self::SLAB, Slab::QUARTZ);
        self::$outbound[self::blockhash(self::SLAB,Slab::NETHER_BRICK+8)] = self::blockhash(self::SLAB, Slab::QUARTZ+8);
        self::$outbound[self::blockhash(self::SLAB, Slab::QUARTZ)] = self::blockhash(self::SLAB, Slab::NETHER_BRICK);
        self::$outbound[self::blockhash(self::SLAB, Slab::QUARTZ+8)] = self::blockhash(self::SLAB, Slab::NETHER_BRICK+8);
        self::$outbound[self::blockhash(self::DOUBLE_SLAB, Slab::QUARTZ)] = self::blockhash(self::DOUBLE_SLAB, Slab::NETHER_BRICK);
        self::$outbound[self::blockhash(self::DOUBLE_SLAB, Slab::NETHER_BRICK)] = self::blockhash(self::DOUBLE_SLAB, Slab::QUARTZ);
        self::$outbound[self::blockhash(self::QUARTZ_BLOCK,10)] = self::blockhash(self::QUARTZ_BLOCK, 4);
        self::$outbound[self::blockhash(self::QUARTZ_BLOCK,6)] = self::blockhash(self::QUARTZ_BLOCK, 3);
        self::$outbound[self::blockhash(self::QUARTZ_BLOCK,9)] = self::blockhash(self::QUARTZ_BLOCK, 1);
        self::$outbound[self::blockhash(self::QUARTZ_BLOCK,5)] = self::blockhash(self::QUARTZ_BLOCK, 1);
        self::$outbound[self::blockhash(self::GRASS_PATH, 0)] = self::blockhash(208, 0);
    }

    /**
     * @param $peId
     * @param $pemeta
     * @return int[]
     */
    public static function filtOutbound($peId, $pemeta)
    {
        $out = [$peId, $pemeta];
        $hash = self::blockhash($peId,$pemeta);
        $v = self::getOutboundFilter();
        if (isset($v[$hash])){
            $out = self::ablockhash($v[$hash]);
        }
        return $out;
    }

    /**
     * @param $javaId
     * @param $javaMeta
     * @return int[]
     */
    public static function filtInbound($javaId, $javaMeta)
    {
        $out = [$javaId, $javaMeta];
        $hash = self::blockhash($javaId,$javaMeta);
        $v = self::getInboundFilter();
        if (isset($v[$hash])){
            $out = self::ablockhash($v[$hash]);
        }
        return $out;
    }
}