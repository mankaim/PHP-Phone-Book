<?php
final class Bootstrap{
    use errors;
    public function __construct(){
        $this->errorReporting();       
        $this->init();
        $this->routing();
    }

    public function errorReporting(){
        // error_reporting(-1);
        if(DEBUG)
            ini_set("display_errors",1); //develop mode
        else{
            //pro mode
            ini_set("display_errors",0);
            $logFileName = date("Y-m-d").".log";
            ini_set("error_log",ROOT_PATH."logs/$logFileName");
        }
    }
    private function init(){
        date_default_timezone_set(DEFAULT_TIMEZONE);
        ob_start();
    }
    private function initSession(){
       
        if(isset($_COOKIE[SESSION_NAME])){
            if(!preg_match('/^[a-zA-Z0-9]{1,40}$/',$_COOKIE[SESSION_NAME]))
                die('Security error!');
        }
        if(!isset($_SESSION)){
            ini_set('session.cookie_samesite','Strict');
            ini_set('session.cookie_httponly',1);
            ini_set('session.hash_function','sha1');
            if(SSL)
                ini_set('session.cookie_secure','sha1');
            ini_set('session.name',SESSION_NAME);
            session_start();
        }
    }
    public function routing(){
     try {
        $requestPath = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO']) : '';
        $requestPathArray = explode('/',$requestPath);
        array_shift($requestPathArray);
        $requestPathArray = array_map('trim',$requestPathArray);
        $requestPathArray = array_filter($requestPathArray, fn($value) => trim($value) !== '');
        $type='';
        if(count($requestPathArray) > 0 && $requestPathArray[0] == ADMIN_DIR_NAME){
            //Backend
            $this->initSession();
            $type='backend';
            $routeName = $requestPathArray;
        }
        else{
            //Frontend
            $type='frontend';
            $routeName = $requestPathArray??$requestPathArray;
        }
        $this->dispatcher($type,$routeName);
     } catch (\Throwable $th) {
        return $this->error($th);
     }
    }
    public function dispatcher($type,$route){
        // print $type."<br>";
        // print_r($route);
        try {
            if($type==='backend'){
                $route[0] = $route[0]==='admin'?'dashboard':$route[0];
                $rr = isset($route[1])?$route[1]:'home';
                $modelFile = ROOT_PATH."model/backend/".$route[0].".php";
                $routeFile = ROOT_PATH."route/backend/".$route[0].".php";
                if(!file_exists($modelFile) || !file_exists($routeFile) ){
                    return $this->error();
                }else
                {
                    require_once($modelFile);
                    require_once($routeFile);
                    $className = ucwords($route[0]);
                    $instanceController = new $className;
                    $isCallableMethod = array($instanceController,$rr);

                    if(!is_callable($isCallableMethod))
                        return $this->error();
                    else  call_user_func($isCallableMethod);
                }
         
            }
        } catch (\Throwable $th) {
            return $this->error($th);
        }
        // try {
        //     $route = $route?$route:'home';
        //     if($type ==='backend')
        //     if(!file_exists(ROOT_PATH."model/".$type."/".$route.".php") || !file_exists(ROOT_PATH."route/".$type."/".$route.".php"))
        //         return $this->error();
        //     else{
        //         require_once(ROOT_PATH."model/".$type."/".$route.".php");
        //         require_once(ROOT_PATH."route/".$type."/".$route.".php");
        //         $c = ucwords($route);
        //         $d = new $c;
        //         $d->home();

        //     }
      
        // } catch (\Throwable $th) {
        //     return $this->error($th);
        // }
      
      
    }
}
    // $db = Database::getInstance();
    // $db->search();
?>