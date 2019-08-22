<?php


namespace cn\dormao\mcpe\parallelclient;

/** @deprecated */
interface ParallelSocketManager
{

    /**
     * @param int $id
     * @return ParallelSocket
     */
    function getSocket($id);

    /**
     * @param string $address
     * @param int $port
     * @return int
     */
    function startSocket($address, $port);

    /**
     * @param int $id
     */
    function closeSocket($id);
}