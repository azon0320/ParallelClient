<?php


namespace cn\dormao\mcpe\parallelclient\protocol;


class WorldTimePacket extends AbstractParallelPacket
{
    public function getPacketId()
    {
        return self::PK_WORLD_TIME;
    }

    /** @var int */
    public $timetick;
    /** @var bool */
    public $timerun;

    protected function onEncode()
    {
        return self::openWriter()
            ->writeInt($this->timetick)
            ->writeASCII($this->timerun ? 1 : 0)->getBuffer();
    }

    public function doDecode($raw)
    {
        $in = self::openReader($raw);
        $this->timetick = $in->readInt();
        $this->timerun = $in->readASCIIi() != 0;
    }
}