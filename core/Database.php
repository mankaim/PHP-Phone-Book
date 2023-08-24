<?php
final class Database{
    use errors;
    private static $instance = null;
    private $host=DB_HOST;
    private $user=DB_USER;
    private $password=DB_PASWORD;
    private $dbname=DB_NAME;
    private $pdo = null;

    private function __construct(){
        try {
            //set DSN
            $dsn = "mysql:host=".$this->host.";dbname=".$this->dbname;
            //create PDO instance
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            if($this->pdo){
                $this->pdo->exec("set names utf8mb4");
                //set default for Fetch
                // $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                //diabled PDO emulate:
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }
           
        } catch (Exception $e) {
            $this->error($e);
        }
       
    }
    public function create($object){ //Insert Data
       try {
            $columns = array_map(function ($column){return ":".$column; }, array_keys($object));
            $columnsStr = implode(", ",$columns);
            $object['phone_numbers'] =  implode("+",$object['phone_numbers']);;
            $rows = implode(",",array_keys($object));
            $sql = "INSERT INTO phone_numbers($rows) VALUES($columnsStr)";
            $stmt = $this->pdo->prepare($sql);
            $stmt = $stmt->execute($object);
            if($stmt)
                return true;
            return false;
       } catch (\Throwable $th) {
            return false;
       }
    }
    public function read($object,$checkIsset=false){
        try {
            $columns = array_map(function ($column){return $column."=?"; }, array_keys($object['where']));
            $columnsStr = implode(" AND ",$columns);
            $where = array_values($object['where']);
            $sql = "SELECT * FROM ".$object['tableName']." WHERE $columnsStr";
            $stmt = $this->pdo->prepare($sql);
            if($stmt){
                $stmt->execute($where);
                $res = $stmt->fetchAll();
                if(count($res)>0){
                    if($checkIsset)
                        return true;
                    else
                        return $res;
                }
                else
                 return false;
            }
            else
                return false;
        } catch (\Throwable $th) {
            return false;
        }
       
    }
    public function getRow($tableName,$where){
        try {
            $sql = "SELECT id FROM $tableName WHERE username = ? AND password = ?";
            $stmt = $this->pdo->prepare($sql);
            if($stmt){
                $stmt->execute($where);
                $res = $stmt->fetchAll();
                if(count($res)>0){
                    $res = json_encode($res,true);
                    return $res;
                }
                else
                 return false;
              
            }
            else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public function search($tableName,$where,$checkIsset=false){
        try {
            $columns = array_map(function ($column){return $column." LIKE ?"; }, array_keys($where));
            $columnsStr = implode(" AND ",$columns);
            $where = array_values($where);

             $sql = "SELECT * FROM $tableName WHERE $columnsStr";
          
             $stmt = $this->pdo->prepare($sql);
             if($stmt){
                 $stmt->execute($where);
                 $res = $stmt->fetchAll();
                 if(count($res)>0){
                    if($checkIsset)
                        return true;
                    else
                        return $res;
                 }
             }
         } catch (Exception $e) {
             $this->error($e);
         }
     }
    // public function search(){
    //    try {
    //         $sql = 'SELECT * FROM posts WHERE author = ? && is_published = ? LIMIT ?';
    //         $stmt = $this->pdo->prepare($sql);
    //         if($stmt){
    //             $stmt->execute(['ramin','1',10]);
    //             $res = $stmt->fetchAll();
    //             $res = json_encode($res,true);
    //             return $res;
    //         }
    //     } catch (Exception $e) {
    //         $this->error($e);
    //     }
    // }
    public function update($object){
        
    }
    public function delete($object){
        //DELETE DATA
        $id = 5;
        $sql = "DELETE FROM posts WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        if($stmt)
            print "POST DELETED";
    }
    static function getInstance()
    {
        if(self::$instance==null)
            self::$instance = new Database();
        return self::$instance;
    }
    public function __destruct(){
        $this->pdo = null;
    }
}
?>