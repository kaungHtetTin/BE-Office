<?php 
include_once('connect.php');
Class Rank{
    public function get($id){
        $DB=new Database();
        $query = "SELECT * FROM ranks WHERE id=$id";
        $result = $DB->read($query);
        return $result[0];
    }

    public function index(){
        $DB = new Database();
        $query = "SELECT * FROM ranks";
        $result = $DB->read($query);
        return $result;
    }
}


?>