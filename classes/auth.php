<?php
Class Auth{
    public function checkAuthAndGetData($user_id,$auth_token){
        
        $DB=new Database();
        $query="select * from users where user_id='$user_id' limit 1";
        $result=$DB->read($query);

        if($result){
            $row=$result[0];
            if($row['auth_token']==$auth_token){
                return $row;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
}
?>