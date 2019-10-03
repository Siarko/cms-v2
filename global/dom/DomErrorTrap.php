<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 08.05.2019
 * Time: 04:06
 */

class DomErrorTrap {
    protected $callback;
    protected $errors = array();
    function __construct($callback) {
        $this->callback = $callback;
    }
    function call() {
        $result = null;
        set_error_handler(array($this, 'onError'));
        try {
            $result = call_user_func_array($this->callback, func_get_args());
        } catch (Exception $ex) {
            restore_error_handler();
            throw $ex;
        }
        restore_error_handler();
        return $result;
    }
    function onError($errno, $errstr, $errfile, $errline) {
        $this->errors[] = array($errno, $errstr, $errfile, $errline);
    }
    function ok() {
        return count($this->errors) === 0;
    }
    function errors() {
        return $this->errors;
    }
}