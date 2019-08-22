<?php


namespace cn\dormao\mcpe\parallelclient\instance;


use cn\dormao\mcpe\parallelclient\ParallelPacket;
use cn\dormao\mcpe\parallelclient\ParallelSocket;
use cn\dormao\mcpe\parallelclient\protocol\BatchedPacket;

class TCPSocket extends \Thread implements ParallelSocket
{
    /*
     * Usage
     * $socket = new TCPSocket("127.0.0.1","25565");
     * $socket->start();
     *
     * getInfo
     * $string = $socket->read();
     *
     * send
     * $socket->send("Hello");
     */

    protected $sockHandle;

    /** @var \Threaded */
    protected $store;

    /** @var \Threaded */
    protected $sendQueue;

    /** @var bool */
    protected $running;

    /** @var string */
    protected $address;

    /** @var int */
    protected $port;

    /** @var bool */
    protected $usable;

    /** @var int */
    protected $batchCount;

    protected $cached;

    /**
     * TCPSocket constructor.
     * @param string $address
     * @param int $port
     */
    public function __construct($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
        $this->store = new \Threaded;
        $this->sendQueue = new \Threaded;
        $this->usable = true;
        $this->batchCount = 2;
    }

    public function isRunning()
    {
        return $this->running;
    }

    public function isUsable()
    {
        return $this->usable;
    }

    public function read()
    {
        return $this->store->shift();
    }

    public function close()
    {
        if ($this->running) @socket_shutdown($this->sockHandle);
        $this->running = false;
        $this->usable = false;
    }

    public function run()
    {
        $this->running = true;
        $this->sockHandle = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->sockHandle){
            $this->close();
        }else{
            $back = @socket_connect($this->sockHandle, $this->address, $this->port);
            if (!$back){
                $this->close();
            }else{
                //Done connecting
            }
        }
        while ($this->isRunning()){
            $buf = "";
            $flag = @socket_recv($this->sockHandle, $buf, self::DATA_LENGTH, 0);
            if ($flag == false){
                $this->close();
            }else{
                #var_dump(sprintf("[%d] Recv : %d", microtime(true) * 1000, $flag));
                $this->store[] = $buf;
            }
        }
    }

    /**
     * @param string|ParallelPacket $str
     * @param bool
     */
    public function send($str, $independent = false)
    {
        if ($this->isUsable()) {
            if ($str instanceof BatchedPacket){
                trigger_error("Can not call send(ParallelPacket,bool) with BatchedPacket, it can only processed by socket");
            }else {
                if ($str instanceof ParallelPacket) $independent = $str->requireSendIndependent();
                $str = $str instanceof ParallelPacket ? $str->getEncoded() : $str;
                $this->sendQueue[] = $independent ? [$str] : $str;
            }
        }
    }

    protected function tickSendQueue()
    {
        $pks = [];
        while (count($pks) < $this->batchCount) {
            if (($str = ($this->sendQueue->shift())) != null) {
                if (is_array($str)) {
                    $pks = $str;
                    break;
                } else {
                    $pks[] = $str;
                }
            } else break;
        }
        //if (count($pks) != 0) var_dump($pks);
        if (count($pks) == 1) {
            @socket_send($this->sockHandle, $pks[0], strlen($pks[0]), 0);
        } elseif (count($pks) > 1) {
            $pk = new BatchedPacket();
            $pk->packetPayloads = $pks;
            $dat = $pk->getEncoded();
            @socket_send($this->sockHandle, $dat, strlen($dat), 0);
        }
    }

    public function tick($t = 0)
    {
        if ($this->isUsable() && $this->isRunning()) $this->tickSendQueue();
    }
}