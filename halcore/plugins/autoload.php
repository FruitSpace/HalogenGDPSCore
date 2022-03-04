<?php

class PluginCore{
    public $HAL_PLUGINS;
    function __construct(){
        //Scan Plugins
        $_files=scandir(__DIR__);
        $HAL_PLUGINS=array();
        foreach ($_files as $fle){
            if(substr($fle,0,3)=="mod"){
                require_once __DIR__."/".$fle;
                $plug=substr(explode(".",$fle)[0],3);
                $HAL_PLUGINS[$plug]=new $plug;
            }
        }
        $this->HAL_PLUGINS=$HAL_PLUGINS;
    }

    function callPlugin($endpoint, ...$data){
        $_endpoint=explode("::",$endpoint);
        if(key_exists($_endpoint[0],$this->HAL_PLUGINS)){
            $plug=$this->HAL_PLUGINS[$_endpoint[0]];
            if(method_exists($plug,$_endpoint[1])) return $plug->{$_endpoint[1]}(...$data);
        }
        return null;
    }

    //===ESSENTIAL===

    function preInit(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::preInit",$this,...$data);
        }
    }

    function unload(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::unload",$this,...$data);
        }
    }

    //===PLAYER===

    function onPlayerNew(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onPlayerNew",$this,...$data);
        }
    }

    function onPlayerActivate(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onPlayerActivate",$this,...$data);
        }
    }

    function onPlayerLogin(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onPlayerLogin",$this,...$data);
        }
    }

    function onPlayerBackup(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onPlayerBackup",$this,...$data);
        }
    }

    function onPlayerScoreUpdate(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onPlayerScoreUpdate",$this,...$data);
        }
    }

    //===LEVEL===

    function onLevelUpload(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onLevelUpload",$this,...$data);
        }
    }

    function onLevelUpdate(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onLevelUpdate",$this,...$data);
        }
    }

    function onLevelDelete(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onLevelDelete",$this,...$data);
        }
    }

    function onLevelRate(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onLevelRate",$this,...$data);
        }
    }

    function onLevelReport(...$data){
        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
            $this->callPlugin($plugin."::onLevelReport",$this,...$data);
        }
    }

//    function unload(...$data){
//        foreach (array_keys($this->HAL_PLUGINS) as $plugin){
//            $this->callPlugin($plugin."::unload",$this,...$data);
//        }
//    }

}

//$plug=new PluginCore();
//var_dump($plug->HAL_PLUGINS);
//
//$plug->preInit();
//$plug->onLevelUpload(4,"hi halogen","DaniilKreyk","-");
//$plug->unload();