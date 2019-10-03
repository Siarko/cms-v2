<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 02.02.2018
 * Time: 22:24
 */

class Node {
    public $name;
    public $childs = [];
    public $x, $y;

    public function __construct($name) {
        $this->name = $name;
    }

    public function addChild($child){
        array_push($this->childs, $child);
    }

    public function __toString() {
        return "N: ".$this->name;
    }
}

class NodeList{
    public $list = [];

    public function __toString() {
        $ret = '<pre>NODESET ['.count($this->list).'] {\n';
        foreach ($this->list as $node){
            $c = "{";
            foreach ($node->childs as $child) {
                $c .= "$child,";
            }
            $c .= "}";
            $ret .= "Name: ".$node->name."; Childs: ".$c."\n";
        }
        return $ret."}</pre>";
    }

    /* @var Node $node*/
    public function add($node){
        $this->list[$node->name] = $node;
    }

    public function addRelation($parent, $child){
        if($this->contains($parent)){
            $this->list[$parent]->addChild($child);
        }else{
            $node = new Node($parent);
            $node->addChild($child);
            $this->add($node);
        }
    }

    /* @var Node $node*/
    public function contains($node){
        foreach ($this->list as $k => $v){
            if ($k == $node){
                return true;
            }
        }
        return false;
    }
}