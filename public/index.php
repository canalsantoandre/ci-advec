<?php
        
        if(!isset($_SERVER['SERVER_NAME'])){
                $server = 'localhost';
        }else{
                $server = $_SERVER['SERVER_NAME'];
        }
        require("index-".str_replace('www.','', $server).".php");