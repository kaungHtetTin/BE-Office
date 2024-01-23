<?php
include('connect.php');

class User{

    public function getUserProfile($user_id){
        $DB=new Database();
        $query="SELECT 
        name,profile_image,email,phone,address,official_agent_id,valid_date
        FROM users WHERE user_id=$user_id";

        $result=$DB->read($query);

        return $result;
    }


    public function updatProfileImage($data,$FILE){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];

        $userData=$this->checkAuthAndGetData($user_id,$auth_token);

        $currentProfile=$userData['profile_image'];
        if($userData!=null){
            $file=$FILE['myfile']['name'];
            $file_loc=$FILE['myfile']['tmp_name'];
            $folder="../uploads/profiles/";
            if(move_uploaded_file($file_loc,$folder.$file)){

                $query="update users set profile_image='$file' where user_id=$user_id";
                $DB=new Database();
                $DB->save($query);
                if($currentProfile!="")unlink($folder.$currentProfile);

                $response['status']="success";
                return $response;
            }else{
                $response['status']="fail";
                return $response;
            }
           
        }else{
            $response['status']="fail";
            return $response;
        }
        
    }

    public function updatProfileData($data){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];
        $key=$data['key'];
        $value=$data['value'];

        $userData=$this->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $query="update users set $key='$value' where user_id=$user_id";
            $DB=new Database();
            $result=$DB->save($query);
            if($result){
                $response['status']="success";
                return $response;
            }else{
                $response['status']="fail";
                return $response;
            }
           
        }else{
            $response['status']="fail";
            return $response;
        }
    }

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

    public function searchByPhone($phone){
        $query ="select
            user_id,
            name,
            profile_image
            from users where  phone='$phone'
        ";

        $DB=new Database();
        $result=$DB->read($query);
        if($result){
            $response['status']="success";
            $response['result']=$result[0];
            return $response;
        }else{
            $response['status']="fail";
            $response['result']="No user was found!";
        }
       
    }

    public function checkPassword($data){
        $user_id=$data['user_id'];
        $password=$data['password'];
        $DB=new Database();
        $query="select * from users where user_id='$user_id' limit 1";
        $result=$DB->read($query);

         if($result){
            $row=$result[0];
            $email=$row['email'];
            $key = "koko&yoe"; 
            $encrypt_password = md5(md5($email . $password) . $key);
            if($row['password']==$encrypt_password){
                $response['code']="50";
                $response['msg']="Please enter your new password.";
            }else{
                $response['code']="51";
                $response['msg']="Incorrect password! Please try again.";
            }
        }else{
            $response['code']="51";
            $response['msg']="Unexpected Error! Please try again later.";
          
        }

        return $response;
    }

    public function resetPassword($data){

        $user_id=$data['user_id'];
        $currentPassword=$data['currentPassword'];
        $newPassword=$data['newPassword'];

        $DB=new Database();
        $query="select * from users where user_id='$user_id' limit 1";
        $result=$DB->read($query);

         if($result){
            $row=$result[0];
            $email=$row['email'];
            $key = "koko&yoe"; 
            $encrypt_password = md5(md5($email . $currentPassword) . $key);
            if($row['password']==$encrypt_password){
                $encrypt= md5(md5($email . $newPassword) . $key);
                $query="update users set password='$encrypt' where user_id=$user_id";
                $saving=$DB->save($query);
                if($saving){
                    $response['code']="50";
                    $response['msg']="Your password has been reset successfully.";
                }else{
                    $response['code']="51";
                    $response['msg']="Unexpected Error! Please try again later.";
                }

            }else{
                $response['code']="51";
                $response['msg']="Incorrect password! Please try again.";
            }
        }else{
            $response['code']="51";
            $response['msg']="Unexpected Error! Please try again later.";
          
        }

        return $response;
    }


    public function generateOTP($data){
        $email=$data['email'];

        $otp=$this->OTP();
        $DB=new Database();
        $query="select * from users where email='$email' limit 1";
        $result=$DB->read($query);
        if($result){
            $query="update users set otp=$otp where email='$email'";
            $DB->save($query);
            mail($email,"OTP - ACA Mobile","Your code is ".$otp);
            $response['code']="50";
            $response['msg']="We sent 6 OTP code to your emaill address. Please check your email and then enter the code here";

        }else{
            $response['code']="51";
            $response['msg']="This email address had not registered yet!";
        }

        return $response;

    }

    private function OTP(){
    
        $length=6;
        $number="";
        for($i=0;$i<$length;$i++){
            $new_rand=rand(0,9);	
            $number=$number.$new_rand;
        }
        
        return $number;
    }
    
    public function checkOTP($data){
        $email=$data['email'];
        $otp=$data['otp'];
        
        $DB=new Database();
        $query="select * from users where email='$email' limit 1";
        $result=$DB->read($query);
        if($result){
            $code=$result[0]['otp'];
            if($code==$otp){
                $response['code']="50";
                $response['msg']="OTP is correct.Now, You can enter your new password.";
            }else{
                $response['code']="51";
                $response['msg']="OTP is not correct. Please try again!";
            }
        }else{
            $response['code']="51";
            $response['msg']="Unexpected error occurred!";
        }
        
    
        return $response;
        
    }
    
    public function resetPasswordByOTP($data){
        $email=$data['email'];
        $otp=$data['otp'];
        $new_password=$data['new_password'];
        $key = "koko&yoe"; 
        $encrypt_password = md5(md5($email . $new_password) . $key);
        
        $DB=new Database();
        $query="update users set password='$encrypt_password' where email='$email' and otp=$otp";
        $DB->save($query);
        
        $response['code']="50";
        $response['msg']="Success! Please press Login to login your account now.";
        
        return $response;
        
    }

    public function getUserValidData($data){
        $user_id=$data['user_id'];

        $DB=new Database();
        $query="select * from users where user_id=$user_id limit 1";
        $result=$DB->read($query);

        return $result[0];
    }

    public function promoteRank($data){
        $user_id=$data['user_id'];
        $rank_id=$data['rank_id'];
        $query="update users set rank_id=$rank_id where user_id=$user_id";
        $DB=new Database();
        $result=$DB->save($query);
        if($result){
            $response['status']="success";
            return $response;
        }else{
            $response['status']="fail";
            return $response;
        }

    }


}

?>