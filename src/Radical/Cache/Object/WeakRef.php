<?php
namespace Radical\Cache\Object;

if(class_exists("WeakReference")){
    class WeakRef {
        private $data = array();

        function get($key){
            if(is_object($key)){
                $key = (string)$key;
            }
            if(!isset($this->data[$key])) return null;
            $ret = $this->data[$key];
            $ret = $ret->get();
            if($ret === null){
                unset($this->data[$key]);
            }
            return $ret;
        }
        function set($key,$value,$ttl = null){
            $value = \WeakReference::create($value);
            if(is_object($key)){
                $key = (string)$key;
            }
            $this->data[$key] = $value;
        }
        function count(){
            return count($this->data);
        }
        function gc($force = false){
            foreach($this->data as $k=>$v){
                if($v->get () === null){
                    unset($this->data[$k]);
                }
            }
        }
        function delete($key){
            if(isset($this->data[$key])){
                unset($this->data[$key]);
            }
        }
    }
}else{
    class WeakRef {
        private $data = array();
        private $weakrefSupport;

        function __construct($support = null){
            if($support === null){
                if(class_exists('Weakref')) {
                    $this->weakrefSupport = true;
            }
            }else{
                $this->weakrefSupport = $support;
            }
        }
        function get($key){
            if(is_object($key)){
                $key = (string)$key;
            }
            if(!isset($this->data[$key])) return null;
            $ret = $this->data[$key];
            if($this->weakrefSupport){
                if($ret->valid()){
                    $ret = $ret->get();
                }else{
                    unset($this->data[$key]);
                    $ret = null;
                }
            }
            return $ret;
        }
        function set($key,$value,$ttl = null){
            if($this->weakrefSupport){
                $value = new \WeakRef($value);
            }
            if(is_object($key)){
                $key = (string)$key;
            }
            $this->data[$key] = $value;
        }
        function count(){
            return count($this->data);
        }
        function gc($force = false){
            if($this->weakrefSupport){
                foreach($this->data as $k=>$v){
                    if(!$v->valid ()){
                        unset($this->data[$k]);
                    }
                }
            }elseif($force){
                foreach($this->data as $k=>$v){
                    unset($this->data[$k]);
                }
            }
        }
        function delete($key){
            if(isset($this->data[$key])){
                unset($this->data[$key]);
            }
        }
    }
}