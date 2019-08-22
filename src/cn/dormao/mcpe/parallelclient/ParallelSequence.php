<?php


namespace cn\dormao\mcpe\parallelclient;


interface ParallelSequence
{
    /**
     * @return string
     */
    function readASCII();

    /** @return int */
    function readShort();

    /**
     * @return string
     */
    function readUTF8();

    /**
     * @return string
     */
    function readBytes();

    /**
     * @return int
     */
    function readInt();

    /**
     * @param int|string
     */
    function writeASCII($dat);

    /** @param int $num*/
    function writeShort($num);

    /**
     * @param string $dat
     */
    function writeUTF8($dat);

    /**
     * @param string $dat
     */
    function writeBytes($dat);

    /**
     * @param int $num
     */
    function writeInt($num);

    /*
     * @param int $len
     *
    function writeByteLength($len);*/

    /**
     * @param int $len
     */
    function sub($len = 1);

    /**
     * @return string
     */
    function getBuffer();
}