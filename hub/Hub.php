<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 00:11
 */

use hub\hubUtil\Deployment;

require_once("Constants.php");
require_once("Messages.php");
require_once("hubUtil/Svg.php");
require_once("hubUtil/Vec.php");
require_once("hubUtil/Node.php");
require_once("hubUtil/ClassLoader.php");

abstract class LogLevel {
    const INFO = 0;
    const WARN = 1;
    const ERR = 2;
}

/**
 * @property Auth Auth
 * @property User User
 * @property Db Db
 * @property PageDb PageDb
 * @property TemplateDb TemplateDb
 * @property UserDb UserDb
 * @property Page Page
 * @property Template Template
 * @property Rest Rest
 * @property Settings Settings
 * @property Request Request
 * @property Url Url
 * @property Util Util
 * @property Action Action
 * @property DomBuilder DomBuilder
 * @property PagePartDb PagePartDb
 * @property PagePart PagePart
 * @property SettingsDb SettingsDb
 * @property Variables Variables
 * @property VariableLoader VariableLoader
 * @property Resources Resources
 * @property Ajax Ajax
 * @property ImageManipulator ImageManipulator
 * @property Menu Menu
 * @property TemplateUtils TemplateUtils
 * @property StructureDb StructureDb
 * @property File File
 * @property Assets Assets
 * @property Html Html
 * @property RoutingTable RoutingTable
 * @property PermLevelsDb PermLevelsDb
 * @property PermLevels PermLevels
 * @property AbstractObject AbstractObject
 * @property Sql Sql
 * @property LanguageController LanguageController
 * @property CascadeFilterController CascadeFilterController
 */
class Hub {


    /* @var Hub $instance */
    private static $instance;
    private static $loaded = false;
    private static $logs = [];
    public static $loadedList;
    private static $initTime;

    private static $muteWarnings = false;


    public function muteWarnings(){
        self::$muteWarnings = true;
    }

    public static function isErrMuted(){
        return self::$muteWarnings;
    }

    private function getClassFiles($directory) {
        $dirList = array();
        $dir = new RecursiveDirectoryIterator(
            $directory,
            RecursiveDirectoryIterator::SKIP_DOTS
        );
        foreach (new RecursiveIteratorIterator($dir) as $file) {
            if ($file->isDir()) {
                continue;
            }
            $type = explode(".", $file->getFilename())[1];
            if ($type == "php") { //is .php file
                $name = $file->getBasename(".php");
                if (in_array($name, Constants::PLUGIN_DISABLED)) {
                    continue;
                }
                $dirList[] = $file;
            }
        }
        return $dirList;
    }

    /* GENERATE GRAPH */
    private function createNodes($relations) {
        /*0 - parent 1 - child*/
        $set = new NodeList();
        foreach ($relations as $relation) {
            $set->addRelation($relation[0], $relation[1]);
        }
        return $set;
    }

    private function generateTreeGraph($relations) {
        $nodes = $this->createNodes($relations);
        $graph = new Svg(200, 500);
        $y = 10;
        $x = 10;
        foreach ($nodes->list as $k => $v) {
            $graph->addText($x, $y, $k, '#ffffff');
            $v->x = $x;
            $v->y = $y;
            $y += 20;
            if ($y > $graph->height) {
                $y = 10;
                $x += 200;
            }
        }
        return $graph->get();
    }

    /* Sortowanie topologiczne - rozwiązywanie zależności pomiędzy klasami
       Topological sort - solving dependencies between classes
        $nodeids - array containing nodenames
        $edges - array containing connections between nodes
    */
    private function topological_sort($nodeids, $edges) {
        $L = $S = $nodes = array();
        foreach ($nodeids as $id) {
            $nodes[$id] = array('in' => array(), 'out' => array());
            foreach ($edges as $e) {
                if ($id == $e[0]) {
                    $nodes[$id]['out'][] = $e[1];
                }
                if ($id == $e[1]) {
                    $nodes[$id]['in'][] = $e[0];
                }
            }
        }
        foreach ($nodes as $id => $n) {
            if (empty($n['in'])) $S[] = $id;
        }
        while (!empty($S)) {
            $L[] = $id = array_shift($S);
            foreach ($nodes[$id]['out'] as $m) {
                $nodes[$m]['in'] = array_diff($nodes[$m]['in'], array($id));
                if (empty($nodes[$m]['in'])) {
                    $S[] = $m;
                }
            }
            $nodes[$id]['out'] = array();
        }
        foreach ($nodes as $n) {
            if (!empty($n['in']) or !empty($n['out'])) {
                $graph = new Svg();
                $graph->create();
                Constants::logCrit("Cyclic error!", "ERROR while sorting class dependencies!",
                    $this->generateTreeGraph($edges));

                return null;
            }
        }
        return $L;
    }

    private function createDependencyQueue($locations) {
        $DELIMITER = '#\/\*[\s]*(D|d)epends:\s*#';// /* Depends:
        $REPLACE = '#\/\*[\s]*(D|d)epends:|[\s\*\/]#';
        $nodes = [];
        $edges = [];
        // Create DAG
        /* @var SplFileObject $value */
        foreach ($locations as $key => $value) { //loop on class files
            $tokens = token_get_all(file_get_contents($value));
            foreach ($tokens as $token) { //loop on tokens inside class file
                if ($token[0] == T_COMMENT or $token[0] == T_DOC_COMMENT) { //comment token found
                    $comment = $token[1];
                    $node = trim($value->getBasename('.php'));
                    if (!preg_match($DELIMITER, $comment)) { //does not have dependency
                        $nodes[] = $node;
                    } else { //has dependencies
                        $dependencies = explode(',', trim(preg_replace($REPLACE, "", $comment)));
                        foreach ($dependencies as $dependency) { //create $edges
                            $edges[] = [$dependency, $node];
                        }
                    }
                    $nodes[] = $node;
                    break;
                }
            }
        }
        // Sort topologically
        $sortedNames = $this->topological_sort($nodes, $edges);
        $sorted = [];
        foreach ($sortedNames as $sortedName) { //get full paths from filenames
            /* @var SplFileObject $location */
            foreach ($locations as $location) {
                if ($location->getBasename('.php') == $sortedName) {
                    $sorted[] = $location;
                    break;
                }
            }
        }
        return $sorted;

    }

    private function classListToArray($list) {
        $array = [];//needs basename and pathname
        /* @var SplFileObject $class */
        foreach ($list as $class) {
            array_push($array, [
                    'basename' => $class->getBasename('.php'),
                    'path' => $class->getPathname()]
            );
        }

        return $array;
    }

    private function getClassList($pluginDir) {
        $list = $this->getClassFiles($pluginDir);
        $list = $this->createDependencyQueue($list);
        $list = $this->classListToArray($list);
        return $list;
    }

    private function genClassListFile($confFile, $pluginDir) {
        $list = $this->getClassList($pluginDir);

        file_put_contents($confFile, serialize($list));
        return $list;
    }

    private function loadClassList($file, $pluginDir) {
        if (!file_exists($file)) {
            return $this->genClassListFile($file, $pluginDir);
        } else {
            $list = unserialize(file_get_contents($file));
            return $list;
        }
    }

    private function getSortedDirs($directory) {
        if (Constants::DEV_MODE) {//reload class list, toposort
            $list = $this->getClassList($directory);
        } else {//load list from file
            $list = $this->loadClassList(Constants::CLASS_DEPENDENCY_ORDER, $directory);
        }
        return $list;
    }

    private function load($directory) {
        $dirs = $this->getSortedDirs($directory);

        $this->logIf(function () use ($dirs) {
            return $this->logPluginOrder($dirs);
        }, LogLevel::INFO, Constants::PLUGIN_ORDER);

        /* @var SplFileObject $classFile */
        foreach ($dirs as $classFile) {
            $name = $classFile['basename'];
            $varName = strtolower($name);
            require_once($classFile['path']);
            if (class_exists($name)) {
                $this->$varName = new $name($this);
            } else {
                $this->log(Messages::format(Messages::$CLASS_NON_EXISTING, $name), LogLevel::ERR);
            }

            self::$loadedList[] = $name;
        }
    }

    private function logPluginOrder($order, $delimiter = '<br/>') {
        $ret = "";
        /* @var SplFileObject $classFile */
        foreach ($order as $k => $classFile) {
            $ret .= ($k + 1) . "-" . $classFile['basename'] . $delimiter;
        }
        return $ret;
    }

    public static function jsFileExists($name) {
        $path = Constants::JS_DIR;
        return file_exists($path . "/" . $name . ".js");
    }

    public function printLoaded($echo = true, $separator = '<br/>') {
        $return = '';
        foreach (self::$loadedList as $class) {
            if($echo){
                echo(" " . $class . $separator);
            }else{
                $return .= $class.$separator;
            }
        }
        return $return;
    }

    function __construct() {
        self::$initTime = microtime();
        new Messages();
        new ClassLoader();
        $this->loadExternalLib(__DIR__.'/../'.Constants::EXTERNAL_LIB_DIR);
        //$this->load(Constants::PLUGIN_DIR);
    }

    public static function initObjects(){
        foreach (self::$loadedList as $className){
            $objectName = strtolower($className);
            $object = Hub::get()->$objectName;
            if(method_exists($object, 'init')){
                $object->init();
            }
        }
    }

    public function logRunnigTime() {
        logJS('TOTAL LOAD TIME: ' .
            (microtime() - self::$initTime) . ' (Dev mode: ' .
            (Constants::DEV_MODE ? 'true' : 'false') . ')');
    }

    public function logMemUsage(){
        logJS("Memory usage: ".(memory_get_usage()/1000)." Kb");
    }

    static private function getCallingMethodData($subtractLevels = 0) {
        $e = new Exception();
        $trace = $e->getTrace();
        $trace = $trace[2+$subtractLevels];
        if (gettype($trace['args']) == "array") {
            foreach ($trace['args'] as $key => $value) {
                $type = gettype($value);
                if ($type == "object") {
                    $trace['args'][$key] = "Obj: " . get_class($value);
                } else {
                    $trace['args'][$key] = $type;
                }
            }
        }
        return $trace;
    }


    /**
     * @param string|array $message
     * @param int $level
     */
    static public function log($message, $level = 0, $subtractLevels = 0) {
        if (is_array($message)) {
            $message = "<pre>" . print_r($message, true) . "</pre>";
        }
        if (is_integer($message)) {
            $message = Messages::get($message);
        }
        self::$logs[] = ['M' => $message, "T" => Hub::getCallingMethodData($subtractLevels), 'L' => $level];
    }

    /*
     * Logs value returned by $function if $logActionName in Constants is true
     * */
    public static function logIf($function, $level, $logActionName) {
        if ($logActionName) {
            if (is_numeric($function)) {
                Hub::log(Messages::get($function), $level, 1);
            } elseif (is_callable($function)) {
                Hub::log($function(), $level, 1);
            } else {
                Hub::log($function, $level, 1);
            }
        }
    }

    /**
     * @return array
     */
    public function getLogs() {
        return self::$logs;
    }

    /**
     * @return Hub constructed instance
     */
    public static function get() {
        if (!self::$loaded) {
            self::$loaded = true;
            self::$instance = new Hub();
            self::checkDeployment();
            //self::initObjects();
        }
        if (!self::$instance) {
            log(Messages::$ACCESSING_NON_LOADED_HUB, LogLevel::ERR);

        }
        return self::$instance;
    }

    function __get($name) {
        $this->$name = new $name($this);
        if(method_exists($this->$name, 'init')){
            $this->$name->init();
        }
        return $this->$name;
    }

    private function loadExternalLib($dir) {
        foreach (Constants::EXTERNAL_LIB as $libPath) {
            $path = Constants::ROOT_DIR.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$libPath;
            if(file_exists($path)){
                require_once($path);
                Hub::log("External lib loaded: ".$libPath, LogLevel::INFO);
            }else{
                Hub::log("External lib >".$libPath."< cannot be loaded", LogLevel::ERR);
            }
        }
    }

    private static function checkDeployment(){
        $path = __DIR__.DIRECTORY_SEPARATOR.Constants::DEPLOYMENT_FILE;
        if(file_exists($path)){return;}
        require_once('hubUtil/Deployment.php');
        $result = Deployment::deploy();
        if($result){
            file_put_contents($path, 'Deployed at '.date("Y-m-d H:i:s"));
        }
        exit();
    }

    public static function deployment(){
        $path = __DIR__.DIRECTORY_SEPARATOR.Constants::DEPLOYMENT_FILE;
        require_once('hubUtil/Deployment.php');
        $result = Deployment::deploy();
        if($result){
            file_put_contents($path, 'Deployed at '.date("Y-m-d H:i:s"));
        }
    }
} 