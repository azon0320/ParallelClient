<?php


namespace cn\dormao\mcpe\parallelclient\pocketmine\block2;

use cn\dormao\mcpe\parallelclient\ParallelUtil as A;

final class Block2Helper82 implements BlockFullId
{
    public static function register($id, $class = ParallelBlockSolid::class){
        A::forceRegisterCustomBaseList($id,$class);
        A::forceRegisterCustomBlockFullList($id, $class);
    }
    public static function registerAll()
    {
        self::register(1);
        self::register(2);
        self::register(3);
        self::register(4);
        self::register(5);
        #self::register(6, ParallelBlockFlowable::class);
        self::register(7);
        self::register(8);
        self::register(9);
        self::register(10);
        self::register(11);
        self::register(12, ParallelBlockFallable::class);
        self::register(13, ParallelBlockFallable::class);
        self::register(14);
        self::register(15);
        self::register(16);
        self::register(17);
        self::register(18);
        self::register(19);
        self::register(20);
        self::register(21);
        self::register(22);
        self::register(23);
        self::register(24);
        self::register(25);
        self::register(26);
        self::register(27);
        self::register(28);
        self::register(29);
        self::register(30);
        self::register(31);
        self::register(32);
        self::register(33);
        self::register(34);
        self::register(35);
        self::register(37);
        self::register(38);
        self::register(39);
        self::register(40);
        self::register(41);
        self::register(42);
        self::register(45);
        self::register(46);
        self::register(47);
        self::register(48);
        self::register(49);
        self::register(51);
        self::register(52);
        self::register(54);
        self::register(55);
        self::register(56);
        self::register(57);
        self::register(58);
        self::register(60);
        self::register(62);
        self::register(64);
        self::register(66);
        self::register(69);
        self::register(70);
        self::register(71);
        self::register(72);
        self::register(73);
        self::register(74);
        self::register(75);
        self::register(76);
        self::register(77);
        self::register(78);
        self::register(79);
        self::register(80);
        self::register(81);
        self::register(82);
        self::register(83);
        #JukeBox 84
        self::register(85);
        self::register(87);
        self::register(88);
        self::register(89);
        self::register(90);
        self::register(92);
        self::register(93);
        self::register(94);
        self::register(95);
        self::register(96);
        self::register(97);
        self::register(98);
        self::register(99);
        self::register(100);
        self::register(101);
        self::register(102);
        self::register(103);
        self::register(104);
        self::register(105);
        self::register(106);
        self::register(107);
        self::register(108);
        self::register(110);
        self::register(111);
        self::register(112);
        self::register(113);
        self::register(115);
        self::register(116);
        self::register(117);
        self::register(118);
        #Java End Portal 119
        self::register(120);
        self::register(121);
        #Java Dragon Egg 122
        self::register(123);
        self::register(124);
        self::register(125);
        self::register(126);
        self::register(127);
        self::register(129);
        #Java Ender Chest 130
        self::register(131);
        self::register(132);
        self::register(133);
        self::register(139);
        self::register(140);
        self::register(141);
        self::register(142);
        self::register(143);
        self::register(144);
        self::register(146);
        self::register(147);
        self::register(148);
        self::register(149);
        self::register(150);
        self::register(151);
        self::register(152);
        self::register(153);
        self::register(154);
        self::register(159);
        #Java Barrier 160
        self::register(161);
        self::register(162);
        self::register(165);
        self::register(167);
        self::register(171);
        self::register(172);
        self::register(173);
        self::register(174);
        self::register(175);
        #Java FreeStanding Banner 176
        #Java Wall Banner 177
        self::register(178);
        self::register(179);
        self::register(183);
        self::register(184);
        self::register(185);
        self::register(186);
        self::register(187);
        #TODO ???
        self::register(193);
        self::register(194);
        self::register(195);
        self::register(196);
        self::register(197);
        self::register(198);
        self::register(199);
        self::register(243);
        self::register(244);
        self::register(245);
        self::register(246);
        self::register(247);
        self::register(248);
        self::register(249);
        self::register(250);
        self::register(251);
    }
}