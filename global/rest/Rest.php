<?php
/**
 * Created by PhpStorm.
 * User: SiarkoWodór
 * Date: 27.06.2017
 * Time: 10:56
 */

/*Wzór każdego zapytania AJAX:
{
    status: 0,1... - status przetwarzania zapytania, czy ogólnie się udało
    content: {} - zawartość zwrotna; opcjonalne, jeżeli jest co zwrócić
}
*/

class Rest {

    private $prefixes = null;
    private $prefix = null;
    private $url;
    private $method;

    private $wasParsed = false;
    private $htmlInvoked = false;

    private $forHtml = null;

    private $pattern = array();

    function __construct() {
        $this->prefixes = new Stack();
    }

    public function getInstance(){
        return $this;
    }

    private function getPattern($pattern){
        if($this->prefix != null){
            if(substr($pattern, 0, 1) == '#'){
                $pattern = '#'.$this->prefix.substr($pattern, 1);
            }else{
                $pattern = '#'.$this->prefix.$pattern.'#';
            }
        }
        return $pattern;
    }

    public function get($link, $handle){
        $this->pattern['GET'][] = ['pattern'=> $this->getPattern($link), 'handle' => $handle];
    }

    public function post($link, $handle){
        $this->pattern['POST'][] = ['pattern' => $this->getPattern($link), 'handle' => $handle];
    }

    /**
     * @return null
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * @param null $prefix
     */
    public function setPrefix($prefix) {
        if($prefix != null){
            $this->prefixes->push($this->prefix);
        }
        $this->prefix = $prefix;
    }

    public function popPrefix(){
        $p = $this->prefixes->pop();
        $this->prefix = $p;
    }

    public function html($event){
        if($this->wasParsed){
            if($this->htmlInvoked){
                Hub::get()->log("HTML Already invoked!", LogLevel::WARN);
            }
            $this->htmlInvoked = true;
            $this->forHtml = $event;
            return;
        }
        Hub::get()->log("HTML invoked outside routing function!", LogLevel::ERR);
    }

    public function parse(){
        $handles = @$this->pattern[$this->method] ?: [];
        foreach($handles as $value){
            $pattern = $value['pattern'];
            if(preg_match($pattern, $this->url, $extracted)){
                $this->wasParsed = true;
                $value['handle']($extracted);

                if($this->forHtml !== null ){
                    Hub::get()->DomBuilder->jsonMode(false);
                    $func = [$this->forHtml];
                    $func[0]();
                }
                $this->wasParsed = false;
                return;
            }
        }
        Hub::get()->log("No rest route found!<br>LINK: ".$this->url."<br>METHOD: ".$this->method, LogLevel::WARN);
    }

    public function setUrl($url){
        $this->url = $url;
    }
    public function setMethod($method){
        $this->method = strtoupper($method);
    }

    public function printRoutes() {
        printr($this->pattern);
    }

} 