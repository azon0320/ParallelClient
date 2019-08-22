<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


use cn\dormao\mcpe\parallelclient\ParallelUtil;
use pocketmine\math\Vector3;

class SetSpawnPacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_SET_SPAWN;
    }

    /** @var Vector3 */
    public $spawn;

    protected function onEncode()
    {
        return self::openWriter()->writeUTF8(ParallelUtil::vec3Hash($this->spawn))->getBuffer();
    }

    public function doDecode($raw)
    {
        $this->spawn = ParallelUtil::avec3Hash(self::openReader($raw)->readUTF8());
    }
}