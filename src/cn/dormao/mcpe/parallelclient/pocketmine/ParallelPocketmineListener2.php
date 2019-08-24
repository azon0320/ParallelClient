<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine;


use cn\dormao\mcpe\parallelclient\ParallelUtil;
use cn\dormao\mcpe\parallelclient\pocketmine\block\filter\BlockFilter82;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelBlocks;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use cn\dormao\mcpe\parallelclient\pocketmine\block2\Block2Helper82;
use cn\dormao\mcpe\parallelclient\protocol\WorldSetBlockPacket;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Fallable;
use pocketmine\block\Flowable;
use pocketmine\block\Gravel;
use pocketmine\block\Liquid;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\PluginManager;

class ParallelPocketmineListener2 implements Listener
{
    protected $main;

    public function blockbreak(BlockBreakEvent $e){
        $client = $this->parallel()->getParallelClientInstance($e->getBlock()->getLevel());
        if ($client != null) {
            $e->getBlock()->getLevel()->setBlock($e->getBlock(), new Air());
            ParallelUtil::updateAroundNonEvent($this->parallel()->getServer()->getPluginManager(),$e->getBlock());
        }
    }

    public function blockplace(BlockPlaceEvent $e){
        $client = $this->parallel()->getParallelClientInstance($e->getBlock()->getLevel());
        if ($client != null) {
            $block = $e->getBlock();
            if (!($block instanceof ParallelPocketmineBlock) && !BlockFilter82::canPlace($block)) $e->setCancelled();
        }
    }

    public function playerclick(PlayerInteractEvent $e){
        $client = $this->parallel()->getParallelClientInstance($e->getBlock()->getLevel());
        if ($client != null) {
            $item = $e->getItem();
            $block = $item->getBlock();
            if ($block != null) {
                if (!($block instanceof ParallelPocketmineBlock) && !BlockFilter82::canPlace($block)) $e->setCancelled();
            }
        }
    }

    public function blockupdate(BlockUpdateEvent $e){
        if ($this->parallel()->getParallelClientInstance($e->getBlock()->getLevel()) != null){
            $block = $e->getBlock();
            if (!($block instanceof ParallelPocketmineBlock)){
                if ($block instanceof Liquid) $e->setCancelled();
            }
        }
    }

    public function registerEvents(PluginManager $pm){
        //Blocks
        $pm->registerEvent(BlockBreakEvent::class,$this,EventPriority::HIGHEST, new MethodEventExecutor("blockbreak"),$this->parallel(),true);
        $pm->registerEvent(BlockPlaceEvent::class,$this,EventPriority::HIGHEST, new MethodEventExecutor("blockplace"),$this->parallel(),true);
        $pm->registerEvent(BlockUpdateEvent::class,$this,EventPriority::HIGHEST,new MethodEventExecutor("blockupdate"),$this->parallel(),true);
        //Players
        $pm->registerEvent(PlayerJoinEvent::class,$this,EventPriority::HIGHEST, new MethodEventExecutor("playerjoin"),$this->parallel(),true);
        $pm->registerEvent(PlayerInteractEvent::class,$this, EventPriority::HIGHEST, new MethodEventExecutor("playerclick"), $this->parallel(), true);
        //$pm->registerEvent(PlayerChatEvent::class, $this, EventPriority::HIGHEST, new MethodEventExecutor("playerchat"), $this->parallel(), true);
    }

    /** @return Main */
    public function parallel(){return $this->main;}

    public function __construct(Main $main){
        $this->main = $main;
    }

    #test
    public function playerjoin(PlayerJoinEvent $e){
        $ver = Main::VERSION;$branch_ver = Main::BRANCH_VERSION;
        $msg = "";
        $msg .= "这个服务器用了 Parallel 世界通信插件(协议号:$ver 分支版本:$branch_ver)\n";
        $msg .= "指定世界的区块会从特定的远程服务器加载，方块更新则跟从宿主世界方块规则\n";
        $msg .= "为了宿主服务器安全，以后可能会移除该信息\n";
        $msg .= "== 以下为启用了 Parallel 的世界 ==\n";
        $worlds = "";
        $pworld = $e->getPlayer()->getLevel()->getFolderName();
        foreach ($this->parallel()->getParallelChannels() as $pro){
            $peworld = $pro->pocketmineProvider()->getLevel()->getFolderName();
            $remoteworld = $pro->getRemoteWorld();
            $address = $pro->getParallelAddress();
            $port = $pro->getParallelPort();
            $worlds .= ("\n" . sprintf("本地[$peworld] => ($address:$port/$remoteworld) %s", $pworld == $peworld ? "(您的位置)" : ""));
        }
        if (strlen($worlds) > 0) substr($worlds, 1);
        $msg .= $worlds;
        $e->getPlayer()->sendMessage($msg);
    }
}