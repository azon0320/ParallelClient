<?php


namespace cn\dormao\mcpe\parallelclient;


interface ParallelPacket
{

    const PK_VERSION = 23;

    #General
    const PK_CLOSE = 0;
    const PK_BATCHED = 1;
    const PK_ERROR = 2;
    const PK_WORLD_SET = 3;
    const PK_TEST = 4;

    #WorldEvent
    const PK_CHUNK_REQUEST = 5;
    const PK_GET_SPAWN = 6;
    const PK_SET_SPAWN = 7;
    const PK_CHUNK_RESPONSE_BLOCKS = 8;
    const PK_CHUNK_RESPONSE_METAS = 9;
    const PK_CHUNK_RESPONSE_BIOMES = 10;
    const PK_CHUNK_RESPONSE_FASTBIN_BLOCKS = 11;
    const PK_CHUNK_RESPONSE_FASTBIN_METAS = 12;
    const PK_WORLD_SET_BLOCK = 15;
    const PK_WORLD_TIME = 16;
    const PK_WORLD_WEATHER = 17;

    #EntityEvent TODO AllPackets
    const PK_ENTITY_SPAWN = 16;
    const PK_ENTITY_DESPAWN = 17;
    const PK_PLAYER_SPAWN = 18;
    const PK_PLAYER_DESPAWN = 19;
    const PK_ENTITY_ID = 20;
    const PK_ENTITY_MOVE = 21;
    const PK_ENTITY_HIT = 22;


    /**
     * @return int 0-255
     */
    function getPacketId();

    function getEncoded();

    function requireSendIndependent();

    /**
     * Must be shifted first from caller!
     * @param string
     */
    function doDecode($raw);
}