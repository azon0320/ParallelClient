<?php


namespace cn\dormao\mcpe\parallelclient;


interface ParallelEvent
{
    /**
     * @return bool
     */
    function isNetworkCancelled();

    /**
     * @return int
     */
    function getEventId();
}