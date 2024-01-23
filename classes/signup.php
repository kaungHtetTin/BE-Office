<?php
include('connect.php');
class SignUp{
    
    private $result="";

    public function validateData($data){
        foreach($data as $key=>$value){
            if(empty($value)){
                $err[$key]="Required";
                $this->result=$err;
            }

            if($key=='email'){
                if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$value)){
                    $err['email']="Invalid email address";
                    $this->$result=$err;
                }

                $DB=new Database();
                $queryCheckEmail="select email from users where email='$value' limit 1";
                $resultEmail=$DB->read($queryCheckEmail);

                if($resultEmail){
                    $err['email']="This email already exists!";
                    $this->result=$err;
                }
            }

            // if($key=="name"){
            //     if(is_numeric($value)){
            //         $err['name']="The name cannot be a number!";
            //         $this->result=$err;
            //     }      
            // }
        }

        if($this->result==""){
            $response=$this->create_user($data);
            return $response;
        }else{
            $response['validate']="fail";
            $response['register']="fail";
            $response['error']=$err;
            return $response;
        }
    }

    public function create_user($data){
        $name=$data['name'];
        $profile_image="";
        $phone=$data['phone'];
        $email=$data['email'];
        $password=$data['password'];
        $fcm_token=$data['fcmToken'];
        $auth_token= uniqid();
        $join_time=time();
        $verified=0;
        $key = "koko&yoe"; 
        $encrypt_password = md5(md5($email . $password) . $key);
        $user_id=$this->create_userid();

        $valid_date=$join_time+(60*60*24*90);

        $querySignUp="insert into users 
        (user_id,name,profile_image,email,phone,password,fcm_token,auth_token,join_time,verified,rank_id,valid_date)
        values
        ('$user_id','$name','$profile_image','$email','$phone','$encrypt_password','$fcm_token','$auth_token',$join_time,$verified,1,$valid_date)
        ";

        $DB=new Database();
        $result=$DB->save($querySignUp);
        if($result){
            $arr['auth_token']=$auth_token;
            $arr['user_id']=$user_id;
            $this-> addNewStock($user_id);

            $response['validate']="success";
            $response['register']="success";
            $response['data']=$arr;
            return $response;
        }else{
            
            $response['register']="fail";
            $response['error']="Unexpected error had occurred!";
            return $response;
        }
        
    }

    private function create_userid(){
    
        $length=rand(7,19);
        $number="";
        for($i=0;$i<$length;$i++){
            
            $new_rand=rand(0,9);	
            $number=$number.$new_rand;
        }
        
        return $number;
    }

      public function addNewStock($user_id){
        $owner_id=$user_id;
        $name="My Stock";

        $query="insert into stocks (owner_id,name,my_stock) values ('$owner_id',\"$name\",1)";
 
        $DB=new Database();
        $result=$DB->save($query);
    
        
        $query2="select stock_id from stocks where owner_id=$owner_id and name='$name' limit 1";
        $result2=$DB->read($query2);
 
        $stock_id=$result2[0]['stock_id'];

        $query3="select product_id from products";
        $product_ids=$DB->read($query3);

        $errChecker=true;

        for($i=0;$i<count($product_ids);$i++){
            $product_id=$product_ids[$i]['product_id'];
            $query4="insert into stock_items (stock_id,product_id,owner_id,count) values($stock_id,$product_id,$owner_id,0)";
            $result=$DB->save($query4);
            if(!$result){
                $errChecker=false;
            }
        }
         
    }

}

?>