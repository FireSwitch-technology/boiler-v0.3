<?php

namespace models;
trait Traits {

    public function  __construct() {

    }

    public  function getMemoryUsage() {
        $mem_usage = memory_get_usage( true );
        if ( $mem_usage < 1024 )
        return $mem_usage.' bytes';
        elseif ( $mem_usage < 1048576 )
        return round( $mem_usage/1024, 2 ).' KB';
        else
        return round( $mem_usage/1048576, 2 ).' MB';
    }

    public  function checkSize() {
        $memory_usage = $this->getMemoryUsage();
        echo 'Memory usage: ' . $memory_usage;
    }

}