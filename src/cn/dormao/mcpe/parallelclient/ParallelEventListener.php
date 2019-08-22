<?php


namespace cn\dormao\mcpe\parallelclient;


use pocketmine\event\Listener;

interface ParallelEventListener extends Listener
{
    function onParallelEvent($eventid, ParallelEvent $event);
}