<?php


namespace cn\dormao\mcpe\parallelclient;


interface ParallelSocket
{

    const DATA_LENGTH = 65535;

    /**
     * @param string|ParallelPacket $str
     */
    function send($str);

    /**
     * Read a data string(ASCII[]) from queue
     * @return string|null
     */
    function read();

    /**
     * @return bool
     */
    function isRunning();

    function isUsable();

    function close();

    function tick($t=0);
}