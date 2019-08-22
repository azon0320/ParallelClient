<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


use cn\dormao\mcpe\parallelclient\ParallelUtil;
use pocketmine\math\Vector3;

class WorldSetBlockPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_WORLD_SET_BLOCK;
    }

    /** @var Vector3 */
    public $pos;
    public $id,$meta;
    public $fallingblock = false;

    protected function onEncode()
    {
        return self::openWriter()
            ->writeUTF8(ParallelUtil::vec3Hash($this->pos))
            ->writeASCII($this->id)->writeASCII($this->meta)
            ->writeASCII($this->fallingblock ? 1 : 0)
            ->getBuffer();
    }

    public function doDecode($raw)
    {
        $in = self::openReader($raw);
        $this->pos = ParallelUtil::avec3Hash($in->readUTF8());
        $this->id = $in->readASCIIi();
        $this->meta = $in->readASCIIi();
        $this->fallingblock = $in->readASCIIi() == 1;
    }
}