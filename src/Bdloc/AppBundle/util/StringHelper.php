<?php

namespace Bdloc\AppBundle\Util;


class StringHelper{

    //retourne une chaîne aléatoire, d'une longueur $length
    function randomString($length = 50){
        $chars = "ABCDEFGHIJKLMNOPQRSTRUVWYXZabcdefghijklmnopqrstruvwyxz0123456789";
        $string = "";
        for($i=0;$i<$length;$i++){
            $num = mt_rand(0, strlen($chars)-1);
            $string .= $chars[$num];
        }
        return $string;
    }
}