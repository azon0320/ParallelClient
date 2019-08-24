<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine;


use cn\dormao\mcpe\parallelclient\ParallelChunk;
use cn\dormao\mcpe\parallelclient\ParallelClient;
use cn\dormao\mcpe\parallelclient\ParallelPocketmine;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelBlocks;
use cn\dormao\mcpe\parallelclient\pocketmine\netbase\Netbase;
use cn\dormao\mcpe\parallelclient\pocketmine\netbase\NetbaseGenerator;
use cn\dormao\mcpe\parallelclient\protocol\AbstractParallelPacket;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\format\LevelProviderManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements ParallelPocketmine
{

    const VERSION = "0.3.9";
    const BRANCH_VERSION = "0.4.0";

    public function onLoad()
    {
        LevelProviderManager::addProvider($this->getServer(), Netbase::class);
        Generator::addGenerator(NetbaseGenerator::class, Netbase::FORMAT_GENERATOR_TYPE);
        AbstractParallelPacket::registerAll();
        ParallelBlocks::init();
    }

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new TickTask($this), 1);
        $pm = $this->getServer()->getPluginManager();
        #(new ParallelPocketmineListener($this))->registerEvents($pm);
        (new ParallelPocketmineListener2($this))->registerEvents($pm);
    }

    public function tick($tick){
        if ($tick % 12 == 0) {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $cx = $player->getFloorX() >> 4;
                $cz = $player->getFloorZ() >> 4;
                $block = $player->getLevel()->getBlock($player->add(0));
                $fullchunkout = "In Chunk {" . $cx . "," . $cz . "} Pos{".$block->__toString()."}";
                /** @var ParallelChunk $vchunk */
                $vchunk = $player->getLevel()->getProvider() instanceof ParallelClient ? ($player->getLevel()->getProvider()->getParallelChunk($cx, $cz)) : null;
                $parallelout = '';
                if ($vchunk != null) $parallelout = "VChunk (".$cx.','.$cz.") Id{".$vchunk->getBlockIdXYZ($player->getFloorX() % 16, $player->getFloorY(), $player->getFloorZ() % 16)."}";
                #$player->sendPopup($fullchunkout . "\n" . $parallelout);
            }
        }
        foreach ($this->getServer()->getLevels() as $level){
            $provider = $level->getProvider();
            if ($provider instanceof ParallelClient){
                $provider->doParallelTick($tick);
            }
        }
    }

    /**
     * @param Level|LevelProvider $levelOprovider
     * @return ParallelClient|null
     */
    public function vclient($levelOprovider){
        $p = null;
        if ($levelOprovider instanceof Level){
            $p = $levelOprovider->getProvider();
        }else $p = $levelOprovider;
        $p = $p instanceof ParallelClient ? $p : null;
        return $p;
    }
    public function getParallelClientInstance($levelOrProvider)
    {
        return $this->vclient($levelOrProvider);
    }

    public function pocketmine()
    {
        return $this->getServer();
    }

    /** @return ParallelClient[] */
    public function getParallelChannels(){
        $channels = [];
        foreach ($this->getServer()->getLevels() as $l){
            if ($l->getProvider() instanceof ParallelClient){
                $channels[] = $l->getProvider();
            }
        }
        return $channels;
    }

    public function statusJson(){
        $var = [];
        $var["timestamp"] = intval(microtime(true));
        $parallels = [];
        foreach ($this->getServer()->getLevels() as $level){
            $provider = $level->getProvider();
            if ($provider instanceof ParallelClient){
                $parallels[] = [
                    'connection' => $provider->isParallelSocketRunning() ? 1 : 0,
                    'address' => sprintf("%s:%s", $provider->getParallelAddress(),$provider->getParallelPort()),
                    'world' => $provider->getRemoteWorld(),
                    'protocol' => AbstractParallelPacket::PK_VERSION,
                ];
            }
        }
        $var["parallels"] = $parallels;
        return json_encode($var);
    }
}