<?php
include('connect.php');
class Login{

    public function authRefresh($user_id,$auth_token){

        $DB=new Database();
        $query="select 
        *
        from users where user_id='$user_id' limit 1";
        $result=$DB->read($query);

        if($result){
            $row=$result[0];
        
            if($row['auth_token']==$auth_token){
                $res['auth']="success";
                $res['data']=$row;
                $res['version']="1.0";
                return $res;
            }else{
                $res['auth']="failure";
                return $res;
            }
        }else{
            $res['auth']="failure";
            return $res;
        }
    }

    public function loginUser($email,$password){

        $email=addslashes($email);
		$password=addslashes($password);
		
        $key = "koko&yoe"; 
        $encrypt_password = md5(md5($email . $password) . $key);


        $DB=new Database();
        $query="select * from users where email='$email' limit 1";
        $result=$DB->read($query);
        if($result){
            $row=$result[0];
            $user_id=$row['user_id'];
            if($row['password']==$encrypt_password){
                //$auth_token= uniqid();
                $auth_token=$encrypt_password;
                $queryAuth="update users set auth_token='$auth_token' where user_id=$user_id";
                $DB->save($queryAuth);

                $res['auth']="success";
                $res['auth_token']=$auth_token;
                $res['user_id']=$user_id;
                $res['profile_image']=$row['profile_image'];
                $res['rank_id']=$row['rank_id'];
                $res['valid_date']=$row['valid_date'];
                $res['verified']=$row['verified'];
                $res['version']="1.3";

                return $res;
            }else{
                $res['auth']="fail";
                $res['error']="Wrong password!";
                return $res;
            }
        }else{
            $res['auth']="fail";
            $res['error']="This email have not registered yet. Create new account.";
            return $res;
        }

     }
}

?>
