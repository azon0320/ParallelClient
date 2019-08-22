<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine;

use pocketmine\scheduler\PluginTask;

class TickTask extends PluginTask
{
    public function __construct(Main $owner)
    {
        parent::__construct($owner);
    }

    public function onRun($currentTick)
    {
        $this->getOwner()->tick($currentTick);
    }
}