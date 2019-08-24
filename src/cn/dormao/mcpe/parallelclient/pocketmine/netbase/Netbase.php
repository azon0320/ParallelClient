<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\netbase;


use cn\dormao\mcpe\parallelclient\instance\TCPSocket;
use cn\dormao\mcpe\parallelclient\instance\XZYFastbinParallelChunk;
use cn\dormao\mcpe\parallelclient\instance\XZYParallelChunk;
use cn\dormao\mcpe\parallelclient\ParallelChunk;
use cn\dormao\mcpe\parallelclient\ParallelClient;
use cn\dormao\mcpe\parallelclient\ParallelEvent;
use cn\dormao\mcpe\parallelclient\ParallelEventListener;
use cn\dormao\mcpe\parallelclient\ParallelPacket;
use cn\dormao\mcpe\parallelclient\ParallelTimings;
use cn\dormao\mcpe\parallelclient\ParallelUtil;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelBlocks;
use cn\dormao\mcpe\parallelclient\pocketmine\block\ParallelPocketmineBlock;
use cn\dormao\mcpe\parallelclient\pocketmine\ParallelPocketmineChunk;
use cn\dormao\mcpe\parallelclient\protocol\AbstractParallelPacket;
use cn\dormao\mcpe\parallelclient\protocol\action\entity\EntityActionPacket;
use cn\dormao\mcpe\parallelclient\protocol\action\WorldEventPacket;
use cn\dormao\mcpe\parallelclient\protocol\BatchedPacket;
use cn\dormao\mcpe\parallelclient\protocol\ChunkRequestPacket;
use cn\dormao\mcpe\parallelclient\protocol\ChunkResponseBiomesPacket;
use cn\dormao\mcpe\parallelclient\protocol\ChunkResponseBlocksPacket;
use cn\dormao\mcpe\parallelclient\protocol\ChunkResponseFastbinBlocks;
use cn\dormao\mcpe\parallelclient\protocol\ChunkResponseFastbinMetas;
use cn\dormao\mcpe\parallelclient\protocol\ChunkResponseMetasPacket;
use cn\dormao\mcpe\parallelclient\protocol\EntityEventPacket;
use cn\dormao\mcpe\parallelclient\protocol\ErrorPacket;
use cn\dormao\mcpe\parallelclient\protocol\GetSpawnPacket;
use cn\dormao\mcpe\parallelclient\protocol\WorldSetBlockPacket;
use cn\dormao\mcpe\parallelclient\protocol\SetSpawnPacket;
use cn\dormao\mcpe\parallelclient\protocol\WorldSetPacket;
use cn\dormao\mcpe\parallelclient\protocol\WorldTimePacket;
use pocketmine\block\Block;
use pocketmine\block\WallSign;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\mcregion\Chunk;
use pocketmine\level\format\mcregion\McRegion;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\utils\ChunkException;
use pocketmine\utils\Config;

class Netbase extends McRegion implements ParallelClient
{

    const CONFIGURATION_FILE_NAME = "address.yml";
    const DEFAULT_REMOTE_ADDRESS = "127.0.0.1";
    const DEFAULT_REMOTE_PORT = 20050;
    const FORMAT_GENERATOR_TYPE = "netbase";
    const DEFAULT_REMOTE_WORLD = "world";

    const RECONNECT_TICKS = 20 * 10;

    /** @var Vector3 */
    protected $spawn;
    /** @var string */
    protected $address;

    /** @var string */
    protected $port;

    /** @var ParallelChunk[] */
    protected $parallelChunks;

    /** @var TCPSocket */
    protected $parallelSocket;

    /** @var string */
    protected $remote_world;

    /** @var string[] */
    protected $asyncParallelQueue = [];

    //TODO 重新连接
    /** @var int */
    protected $reconnectTick;

    /*
    protected $chunkRequestQueue = [];
    */

    public function getParallelChunk($x, $z)
    {
        $hash = ParallelUtil::chunkHash($x,$z);
        return isset($this->parallelChunks[$hash]) ? $this->parallelChunks[$hash] : null;
    }

    public function getParallelChunkAt($x, $y, $z)
    {
        $chunkX = (int) ($x / 16);$chunkZ = (int) ($z / 16);
        return $this->getParallelChunk($chunkX, $chunkZ);
    }

    public function loadParallelChunk($x, $z)
    {
        if ($this->getParallelChunk($x,$z) == null){
            $this->setParallelChunk($x, $z, new XZYParallelChunk($this, $x, $z));
            //$this->setParallelChunk($x, $z, new XZYFastbinParallelChunk($this, $x, $z));
        }
    }

    public function asyncLoadParallelChunk($x, $z)
    {
        $hash = ParallelUtil::chunkHash($x,$z);
        $this->loadParallelChunk($x, $z);
        if (!isset($this->asyncParallelQueue[$hash])){
            $pk = new ChunkRequestPacket();
            $pk->chunkx = $x;$pk->chunkz = $z;
            $this->sendParallelPacket($pk);
            $this->asyncParallelQueue[$hash] = intval(microtime(true));
        }else{
            $lastLoad = $this->asyncParallelQueue[$hash];
            if (intval(microtime(true)) - $lastLoad > 4){
                $pk = new ChunkRequestPacket();
                $pk->chunkx = $x;$pk->chunkz = $z;
                $this->sendParallelPacket($pk);
                $this->asyncParallelQueue[$hash] = intval(microtime(true));
            }
        }
    }

    public function sendParallelPacket(ParallelPacket $packet)
    {
        if ($this->parallelSocket != null) $this->getParallelSocket()->send($packet);
    }

    protected function startSocket(){
        $this->parallelSocket = new TCPSocket($this->getParallelAddress(), $this->getParallelPort());
        $this->parallelSocket->start();
    }

    protected function processRaw($str){
        /** @var ParallelPacket $pk */
        $pk = AbstractParallelPacket::getPacket($str);
        if ($pk != null){
            switch ($pk->getPacketId()) {
                case ParallelPacket::PK_CLOSE:
                    $this->getServer()->getLogger()->info("Close Packet Received, turn to local");
                    break;
                case ParallelPacket::PK_ERROR:
                    $this->getLevel()->getServer()->getLogger()->warning(str_replace("%E", $pk->errcode, str_replace("%S", $pk->errstring, "ErrorPacket{%E , %S}")));
                    break;
                case ParallelPacket::PK_BATCHED:
                    /** @var BatchedPacket $pk */
                    foreach ($pk->packetPayloads as $dat){
                        $this->processRaw($dat);
                    }
                    break;
                case ParallelPacket::PK_SET_SPAWN:
                    $pk = new SetSpawnPacket();
                    $pk->doDecode($str);
                    $this->setSpawn($pk->spawn);
                    $this->getLevel()->setSpawnLocation($pk->spawn);
                    $this->getServer()->getLogger()->info("[" . $this->remote_world . "] Spawn Location " . ParallelUtil::vec3Hash($this->getSpawn()));
                    break;
                case ParallelPacket::PK_CHUNK_RESPONSE_BLOCKS:
                    /** @var ChunkResponseBlocksPacket $pk */
                    $chunkx = $pk->chunkx;$chunkz = $pk->chunkz;
                    $chunk = $this->getParallelChunk($pk->chunkx, $pk->chunkz);
                    if ($chunk != null && !$chunk->isApplied()) {
                        $chunk->setBlocks($pk->payload);
                    }
                    break;
                case ParallelPacket::PK_CHUNK_RESPONSE_METAS:
                    /** @var ChunkResponseMetasPacket $pk */
                    $chunk = $this->getParallelChunk($pk->chunkx, $pk->chunkz);
                    if ($chunk != null && !$chunk->isApplied()) $chunk->setMetas($pk->payload);
                    break;
                case ParallelPacket::PK_CHUNK_RESPONSE_BIOMES:
                    /** @var ChunkResponseBiomesPacket $pk */
                    $chunk = $this->getParallelChunk($pk->chunkx,$pk->chunkz);
                    if ($chunk != null && !$chunk->isApplied()) $chunk->setBiomes($pk->payload);
                    break;
                case ParallelPacket::PK_CHUNK_RESPONSE_FASTBIN_BLOCKS:
                    /** @var ChunkResponseFastbinBlocks $pk */
                    $chunk = $this->getParallelChunk($pk->chunkx,$pk->chunkz);
                    if ($chunk != null && !$chunk->isApplied()) $chunk->setBlocks($pk->payload);
                    break;
                case ParallelPacket::PK_CHUNK_RESPONSE_FASTBIN_METAS:
                    /** @var ChunkResponseFastbinMetas $pk */
                    $chunk = $this->getParallelChunk($pk->chunkx,$pk->chunkz);
                    if ($chunk != null && !$chunk->isApplied()) $chunk->setMetas($pk->payload);
                    break;
                case ParallelPacket::PK_WORLD_SET_BLOCK:
                    /** @var WorldSetBlockPacket $pk */
                    $id = $pk->id;
                    $blockdat = ParallelBlocks::filterBlock0_15_4In($id, $pk->meta);
                    if (isset(Block::$list[$id])){
                        $b = Block::get($blockdat[0]);$b->setDamage($blockdat[1]);
                        #TODO Serialize this statement
                        /** @deprecated  */
                        $this->netbaseInboundSetBlock($pk->pos, $b);
                        #$this->getLevel()->setBlock($pk->pos, $b, false, false);
                        #ParallelUtil::updateAroundNonEvent($this->getServer()->getPluginManager(),$b);
                        #$this->getLevel()->addParticle(new DestroyBlockParticle($pk->pos, $b));
                    }else{
                        $b = $pk->fallingblock ? Block::get(Block::SAND) : Block::get(Block::STONE);
                        #$this->getLevel()->setBlock($pk->pos,$b,false,false);
                        #ParallelUtil::updateAroundNonEvent($this->getServer()->getPluginManager(),$b);
                        $this->netbaseInboundSetBlock($pk->pos, $b);
                        $this->getServer()->getLogger()->warning(sprintf("[%s] Found unexpected block id : %d", $this->getRemoteWorld(), $pk->id));
                    }
                    break;
                case ParallelPacket::PK_WORLD_TIME:
                    /** @var WorldTimePacket $pk */
                    $this->getLevel()->setTime($pk->timetick);
                    if ($pk->timerun) $this->getLevel()->startTime(); else $this->getLevel()->stopTime();
                    $this->getServer()->getLogger()->info(sprintf("[%s] WorldTime %d (%s)", $this->getRemoteWorld(), $pk->timetick, $pk->timerun ? "Running" : "Stoppped"));
                    break;
                //TODO More Packets, process here
            }
        }
    }

    public function netbaseInboundSetBlock(Vector3 $fullv, Block $b){
        $b->position(Position::fromObject($fullv, $this->getLevel()));
        $this->getLevel()->setBlockIdAt($fullv->getX(),$fullv->getY(), $fullv->getZ(), $b->getId());
        $this->getLevel()->setBlockDataAt($fullv->getX(),$fullv->getY(), $fullv->getZ(), $b->getDamage());
        $this->getLevel()->sendBlocks($this->getLevel()->getChunkPlayers($fullv->getX() >> 4, $fullv->getZ() >> 4),[$b],UpdateBlockPacket::FLAG_ALL_PRIORITY);
    }

    //This method will be called in PluginTask via Scheduler(period=1)
    public function processParallelData()
    {
        if ($this->parallelSocket != null) {
            if ($this->parallelSocket->isUsable()) {
                if ($this->parallelSocket->isRunning()) {
                    $str = $this->parallelSocket->read();
                    if ($str != null) {
                        $this->processRaw($str);
                    }
                    $this->parallelSocket->tick();
                    /** @var ParallelPocketmineChunk $chunk */
                    foreach ($this->chunks as $chunk) {
                        if (!$chunk->isParallelChunkApplyed()) {
                            $cx = $chunk->getX();
                            $cz = $chunk->getZ();
                            $vchunk = $this->getParallelChunk($cx, $cz);
                            if ($vchunk != null && $vchunk->isChunkApplyReady()) {
                                if (isset($this->asyncParallelQueue[ParallelUtil::chunkHash($cx, $cz)])) unset($this->asyncParallelQueue[ParallelUtil::chunkHash($cx, $cz)]);
                                $vchunk->pocketmineApply($chunk);
                                $this->sendChunk($chunk);
                            } else $this->asyncLoadParallelChunk($cx, $cz);
                        }
                    }
                } else {
                    //do nothing
                }
            } else {
                //Stop Run
                $this->parallelSocket = null;
                $this->getServer()->getLogger()->warning(sprintf(
                    "[%s] Parallel closed, the world does not work now.", $this->getRemoteWorld()
                ));
            }
        }
    }

    public function doParallelTick($t=0){
        $this->processParallelData();
        if ($t % (20 * 60 * 3) == 0) $this->gc();
    }

    public function unloadParallelChunk($x, $z){
        $oldchunk = $this->getParallelChunk($x, $z);
        if ($oldchunk != null){
            $oldchunk->setParallelClient(null);
        }
        unset($this->parallelChunks[ParallelUtil::chunkHash($x,$z)]);
    }

    public function closeParallel()
    {
        $this->clearParallelChunks();
        $this->parallelSocket->close();
    }

    public function clearParallelChunks()
    {
        foreach ($this->parallelChunks as $c){
            $c->setParallelClient(null);
        }
        $this->parallelChunks = [];
    }


    public function pocketmineProvider(){ return $this; }

    public function setParallelChunk($x, $z, ParallelChunk $chunk){
        $this->unloadParallelChunk($x,$z);
        $chunk->setParallelClient($this);
        $this->parallelChunks[ParallelUtil::chunkHash($x, $z)] = $chunk;
    }

    public function gc(){$this->doGarbageCollection();}



    #--------------- Parallel界 -----------------#
    /*
     * Non-Override Methods & Reasons
     * 从McRegion来的并且不重写的方法及表明不重写的原因
     * getProviderOrder() 在Level中暂时只有get,set方法,暂时无用
     * usesChunkSection() McRegion的ChunkSection为false,与默认值相同
     * unloadChunks() 卸载所有Chunks,原理是清空Chunk数组
     * unloadChunk(int,int) 卸载一个Chunk,原理是从数组移除一个Chunk
     * getLoadedChunks() 获得已加载的Chunks,原理是返回Chunk数组
     * isChunkLoaded(int,int) 是否已经加载指定位置的Chunk,原理是判定数组是否存在ChunkHash
     * createChunkSection(int) 获得ChunkSection,McRegion不支持ChunkSection,Netbase直接继承即可
     */

    public static function getProviderName(){
        return "netbase";
    }

    public static function isValid($path){
        /*
        $isValid = (file_exists($path . "/" . self::CONFIGURATION_FILE_NAME) and is_dir($path . "/region/"));

        if($isValid){
            $files = glob($path . "/region/*.mc*");
            foreach($files as $f){
                if(strpos($f, ".mcr") !== false){ //McRegion
                    $isValid = false;
                    break;
                }
            }
        }
        return $isValid;*/
        return file_exists($path . "/" . self::CONFIGURATION_FILE_NAME);
    }

    public static function generate($path, $name, $seed, $generator, array $options = []){
        //不生成region文件夹,Anvil和McRegion将不会识别
        if(!file_exists($path)){
            mkdir($path, 0777, true);
        }
        $levelData = new CompoundTag("Data", [
            "hardcore" => new ByteTag("hardcore", 0),
            "initialized" => new ByteTag("initialized", 1),
            "GameType" => new IntTag("GameType", 0),
            "generatorVersion" => new IntTag("generatorVersion", 1), //2 in MCPE
            "SpawnX" => new IntTag("SpawnX", 128),
            "SpawnY" => new IntTag("SpawnY", 70),
            "SpawnZ" => new IntTag("SpawnZ", 128),
            "version" => new IntTag("version", 19133),
            "DayTime" => new IntTag("DayTime", 0),
            "LastPlayed" => new LongTag("LastPlayed", microtime(true) * 1000),
            "RandomSeed" => new LongTag("RandomSeed", $seed),
            "SizeOnDisk" => new LongTag("SizeOnDisk", 0),
            "Time" => new LongTag("Time", 0),
            "generatorName" => new StringTag("generatorName", Generator::getGeneratorName($generator)),
            "generatorOptions" => new StringTag("generatorOptions", isset($options["preset"]) ? $options["preset"] : ""),
            "LevelName" => new StringTag("LevelName", $name),
            "GameRules" => new CompoundTag("GameRules", [])
        ]);
        $nbt = new NBT(NBT::BIG_ENDIAN);
        $nbt->setData(new CompoundTag("", [
            "Data" => $levelData
        ]));
        $buffer = $nbt->writeCompressed();
        file_put_contents($path . "level.dat", $buffer);

        $conf_file = $path . self::CONFIGURATION_FILE_NAME;
        $conf = new Config($conf_file,Config::PROPERTIES,[]);
        $conf->set("address", self::DEFAULT_REMOTE_ADDRESS);
        $conf->set("port", self::DEFAULT_REMOTE_PORT);
        $conf->set("remote_world", self::DEFAULT_REMOTE_WORLD);
        $conf->save();
        unset($conf);
    }

    public function __construct(Level $level, $path)
    {
        parent::__construct($level, $path);
        $conf_file = $path . self::CONFIGURATION_FILE_NAME;
        $conf = new Config($conf_file, Config::PROPERTIES,[]);
        $this->address = $conf->get("address", self::DEFAULT_REMOTE_ADDRESS);
        $this->port = $conf->get("port", self::DEFAULT_REMOTE_PORT);
        $this->remote_world = $conf->get("remote_world", self::DEFAULT_REMOTE_WORLD);
        unset($conf);
        $this->parallelChunks = [];
        $this->startSocket();
        $worldsetpk = new WorldSetPacket();
        $worldsetpk->worldname = $this->getRemoteWorld();
        $this->parallelSocket->send($worldsetpk);
        $spawnpk = new GetSpawnPacket();
        $this->parallelSocket->send($spawnpk);
        $this->getLevel()->getServer()->getLogger()->info(
            "[".$this->getRemoteWorld()."] Opened Parallel Socket " . $this->getParallelAddress() . ":" . $this->getParallelPort() . "/" . $this->getRemoteWorld()
        );
    }

    public function getGenerator(){
        return self::FORMAT_GENERATOR_TYPE;
    }

    public function getGeneratorOptions(){
        return [];
    }

    public function saveChunks()
    {
        //do nothing
        //TODO 重写保存的Chunks,原理是:将所有改动的Chunks用setBlock的方式发送到Java服务器
    }

    public function doGarbageCollection()
    {
        #McRegion的方法原理是关闭并保存长时间无用的Region，Netbase继承的代码完全废弃了region，所以不需要操作region
        #清理无用的虚拟Chunk
        $sec = intval(microtime(true));
        $vcount = 0;
        foreach ($this->parallelChunks as $vchunk) {
            #当附近无人时，Level会自动unload无用的FullChunk
            if ($sec - $vchunk->lastActive() > 60 * 2 && $this->getChunk($vchunk->getChunkX(), $vchunk->getChunkZ()) == null) {
                $this->unloadParallelChunk($vchunk->getChunkX(), $vchunk->getChunkZ());
                $vcount++;
            }
        }
        if ($vcount != 0){
            $this->getServer()->getLogger()->info(
                "[".$this->getRemoteWorld()."] Unload Parallel " . $vcount
            );
        }
    }

    /*
     * requestChunkTask(int,int) 这是一个Chunk对象转二进制数据并传给玩家的过程,不涉及Chunk格式
     * Level在玩家发出Chunk请求后，先检查缓存的数据，如果没有或者还没生成，就会调用requestChunkTask
     * 调用requestChunkTask后，Level会保存一个队列ID，一直等到Task回调Level的ChunkRequestCallback
     * 如果回调时Level并不存在这个队列ID，Level将直接将这个Chunk
     */
    public function requestChunkTask($x, $z)
    {
        /** @var NetbaseChunk $chunk */
        $chunk = $this->getChunk($x, $z, true);
        if ($chunk->isParallelChunkApplyed()){
            $vchunk = $this->getParallelChunk($x, $z);
            if ($vchunk != null) {
                $vchunk->chunkActive();
                return parent::requestChunkTask($x, $z);
            }else{
                trigger_error("can not find parallel from a loaded FullChunk, is it collected as garbage?");
            }
        }
        return null;
    }
    public function sendChunk(FullChunk $chunk){
        $this->requestChunkTask($chunk->getX(), $chunk->getZ());
    }

    public function loadChunk($chunkX, $chunkZ, $create = false)
    {
        $index = Level::chunkHash($chunkX, $chunkZ);
        if(isset($this->chunks[$index])){
            return true;
        }
        $chunk = $this->getEmptyChunk($chunkX,$chunkZ);
        if ($chunk != null) {
            $this->chunks[$index] = $chunk;
        }
        return $chunk != null;
    }

    public function getChunk($chunkX, $chunkZ, $create = false)
    {
        $index = Level::chunkHash($chunkX, $chunkZ);
        if(isset($this->chunks[$index])){
            $chunk = $this->chunks[$index];
            return $chunk;
        }else if ($create){
            $this->loadChunk($chunkX, $chunkZ, $create);
        }
        return isset($this->chunks[$index]) ? $this->chunks[$index] : null;
    }

    public function getEmptyChunk($chunkX, $chunkZ)
    {
        $c = NetbaseChunk::getEmptyChunk($chunkX,$chunkZ,$this);
        return $c;
    }

    public function saveChunk($x, $z)
    {
        //do nothing
        return true;
    }

    public function setChunk($chunkX, $chunkZ, FullChunk $chunk)
    {
        if(!($chunk instanceof Chunk)){
            throw new ChunkException("Invalid Chunk class");
        }

        if (!($chunk instanceof ParallelPocketmineChunk)) {
            throw new ChunkException("Invalid ParallelPocketmineChunk class");
        }

        /*
        $chunk->setProvider($this);

        $chunk->setX($chunkX);
        $chunk->setZ($chunkZ);
        */

        if(isset($this->chunks[$index = Level::chunkHash($chunkX, $chunkZ)]) and $this->chunks[$index] !== $chunk){
            //$this->unloadChunk($chunkX, $chunkZ, false);
            return;
        }

        $this->chunks[$index] = $chunk;
    }

    public function isChunkGenerated($chunkX, $chunkZ)
    {
        $chunk = $this->getChunk($chunkX,$chunkZ, false);
        return $chunk != null && $chunk->isGenerated();
    }

    public function isChunkPopulated($chunkX, $chunkZ)
    {
        $chunk = $this->getChunk($chunkX,$chunkZ, false);
        return $chunk != null && $chunk->isPopulated();
    }

    public function getSpawn()
    {
        return $this->spawn == null ?  parent::getSpawn() : $this->spawn;
    }

    public function close()
    {
        $this->unloadChunks();
        $this->level = null;
        $this->closeParallel();
    }



    public function getParallelAddress()
    {
        return $this->address;
    }

    public function getParallelPort()
    {
        return $this->port;
    }

    public function getRemoteWorld()
    {
        return $this->remote_world;
    }

    public function getParallelSocket()
    {
        return $this->parallelSocket;
    }

    public function isParallelSocketRunning()
    {
        return $this->parallelSocket->isRunning();
    }

    /** @deprecated useless */
    public function sendPlayerChunkPayload($chunkx, $chunkz,$ordered)
    {
        foreach ($this->getLevel()->getPlayers() as $p) {
            $p->sendChunk($chunkx, $chunkz, $ordered);
        }
    }
}