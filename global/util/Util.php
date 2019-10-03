<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 22.06.2017
 * Time: 00:08
 */

class Util {
    public function printr($array) {
        echo("<pre>");
        print_r($array);
        echo("</pre>");
    }

    private function convert($amount) {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($amount / pow(1024, ($i = floor(log($amount, 1024)))), 2) . ' ' . $unit[$i];
    }

    public function getUsedMemory() {
        return $this->convert(memory_get_usage());
    }

    private function parseTrace($trace) {
        $parsed = [
            "file" => $trace['file'],
            "line" => $trace['line'] . " in " . $trace['function'],
            "class" => @$trace['class'] ?: "{NoClass}",
            "args" => ""
        ];
        foreach ($trace['args'] as $k => $arg) {
            if ($k != 0) {
                $parsed['args'] .= ", ";
            }
            $parsed['args'] .= $arg;
        }

        return $parsed;
    }

    public function getTextFromHtml($html){
        $prefix = '<body>';
        $xml = '<?xml encoding="utf-8" ?>'.$prefix.$html.'</body>';
        $document = new DOMDocument();
        $document->registerNodeClass('DOMElement', 'JSLikeHTMLElement');
        $document->formatOutput = false;
        $document->preserveWhiteSpace = false;
        $trap = new DomErrorTrap([$document, 'loadHTML']);

        try {
            $trap->call($xml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        } catch (Exception $e) {
            echo($e->getMessage());
        }
        if(!$trap->ok()){
            Hub::logIf("XML PARSER ERRORS OCCURED: ".count($trap->errors()), LogLevel::ERR, Constants::LOG_FILTERS);
            foreach ($trap->errors() as $error) {
                Hub::logIf($error, LogLevel::ERR, Constants::LOG_FILTERS);
            }
        }

        return $document->textContent;
    }

    public function getLogConsole($logs) {
        $types = ['info', 'warn', 'err'];
        $console = "<div id='consoleContainer' class='resizable draggable'>";
        foreach ($logs as $log) {
            $trace = $this->parseTrace($log['T']);
            $typeClass = $types[$log['L']];
            $title = $typeClass . " from " . $trace['class'] . " ⇒ " . $log['T']['function'];
            $console .= "<div class='consoleEntry'>";
            $console .= "<div class='entryTitle " . $typeClass . "'>" . $title . "</div>";
            $console .= "<div class='consoleMessageBox " . $typeClass . "'>" . $log['M'] . "</div>";
            $console .= "<div class='consoleTrace hidden'><pre>" . print_r($trace, true) . "</pre></div>";
            $console .= "</div>";
        }

        $console .= "</div>";
        return $console;
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
} 