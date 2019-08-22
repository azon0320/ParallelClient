<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block;


/*
 *
 * 方块在放置时是处于直立状态，触发BlockPlaceEvent
 * 但是有些方块放置后是不完整的，需要更新，触发BlockUpdateEvent
 * 这时候需要让Listener识别到这个Block，无视BlockPlaceEvent，接收BlockUpdateEvent
 *
 * 例子
 * 火把插在墙上，刚插上的时候，BlockPlaceEvent得到的火把是直立的
 * 如果这时候把直立的火把发送到PC端，PC端会将这个火把当作直立浮空（火把下面没有方块），计算物理之后让它掉下来
 * 火把触发BlockPlaceEvent后，Level调用Block::place()事件
 * 这时火把将直立状态更新成附在墙上的状态，PC端就可以认为到这个火把是插在墙上的，不会因为重力掉下来
 *
 * 实现方法
 * 需要修改的方块实现ParallelPocketmineBlock接口
 * 未触发Block::place()时，canProcessEvent()返回FALSE，ParallelListener不会处理这个事件
 * 事件触发并且没有取消时，Block::place()调用，此时canProcessEvent()为TRUE，继承后的方块重新调用BlockPlaceEvent
 * 这时候的BlockPlaceEvent就可以为ParallelListener调用
 * 修改后的Block如果不能注册到Block::$list就无法启用它的功能,用玄学的方法让它加进PocketMine的生存周期(见@ParallelBlocks)
 *
 *
 * 目前可能存在的问题：
 *  其他插件会监听到2次BlockPlaceEvent,是否考虑另外增加专用的事件处理?
 *
 * 附
 * Level::setBlock(Vector3 pos,Block block,bool direct,bool update)
 * 参数3 direct表示不管是否事件被取消都装上这个方块
 * 参数4 update表示是否在调用时触发一个BlockUpdateEvent
 *
 * Block::place() 如果放置成功返回TRUE，否则就是FALSE
 *
 * 当然如果能改掉服务器代码的原本逻辑是很方便的,为了安全不能这么做
 * 测试代码: Genisys 0.15.4
 */
interface ParallelPocketmineBlock
{

    const WARN_PLUGIN_SETBLOCK = "Plugin place status";

    const WARN_LEVEL_NULL = "Attempt to place a block with no level value";

    /**
     * 表示这个方块在触发BlockPlaceEvent时,Listener是否应该处理这个事件
     * 仅用于Parallel功能
     * @return bool
     */
    function canProcessEvent();

    /**
     * @return ParallelPocketmineBlock
     */
    function setPlaced();
}