<?php
final class Utils{
    private static $instance = null;
    private function __construct() {}
    static function getInstance()
    {
        if(self::$instance==null)
            self::$instance = new Utils();
        return self::$instance;
    }
    public function redirect($url){
        header("Location:$url");
        die;
    }
    public function getLang(){
        if(isset($_COOKIE["phone_book_lang"])){
            $cookie = $this->encode($_COOKIE["phone_book_lang"]);
            if(is_numeric($cookie) || strlen($cookie)>2)
                return 'en';
            else
                return strtolower($cookie);
        }
        else
            return null;
    }
    public function post($key){
        if(isset($_POST[$key]) && !is_array($_POST[$key]))
            return trim($_POST[$key]);
        else
        return null;
    }
    public function get($key){
        if(isset($_GET[$key]) && !is_array($_GET[$key]))
            return trim($_GET[$key]);
        else
        return null;
    }
    public function isEmailValid($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public function makeHash($str):string
    {
        return $str ? sha1(SECRET_KEY.$str."dv%".SECRET_KEY."a%w") : '';
    }
    public function safeString($str):string
    {
        return $str ? htmlentities(addslashes($str), ENT_QUOTES, 'UTF-8') : '';
    }
    
    public function safeInt($int):int
    {
        return (int)$int<=0?0:(int)$int;
    }
    public function safeFloat($float):float
    {
        return $float ? (float)$float : 0;
    }
    function encode($data) {
        if (is_array($data)) {
            return array_map(array($this,'encode'), $data);
        }
        if (is_object($data)) {
            $tmp = clone $data;
            foreach ( $data as $k => $var )
                $tmp->{$k} = $this->encode($var);
            return $tmp;
        }
        return $data ? htmlentities(addslashes(trim($data)), ENT_QUOTES, 'UTF-8') : '';
    }
    public function decode($data) {
        if (is_array($data)) {
            return array_map(array($this,'decode'), $data);
        }
        if (is_object($data)) {
            $tmp = clone $data; 
            foreach ( $data as $k => $var )
                $tmp->{$k} = $this->decode($var);
            return $tmp;
        }
        return $data ? html_entity_decode(stripslashes($data),ENT_QUOTES,'UTF-8') : '';
    }
}

?>