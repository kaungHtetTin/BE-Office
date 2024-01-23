<?php
include('connect.php');
Class Staff {
    public function get(){
        $DB=new Database();
        $query="SELECT * FROM staffs WHERE disable=0";
        $result=$DB->read($query);

        $response['staffs']=$result;

        $query2="SELECT department FROM staffs GROUP BY department";
        $result2=$DB->read($query2);

        $response['departments']=$result2;
 
        return $response;

    }

    public function add($data,$FILE){
        $name =$data['name'];
        $rank=$data['rank'];
        $department=$data['department'];
        $phone=$data['phone'];
        $email=$data['email'];
        $address=$data['address'];
        $gender=$data['gender'];
        $join_at=time();
        $profileImage="";
        if(isset($FILE['myfile'])){
            $file=$FILE['myfile']['name'];
            $file_loc=$FILE['myfile']['tmp_name'];
            $folder="../../uploads/profiles/";
            if(move_uploaded_file($file_loc,$folder.$file)){
                $profileImage=$file;
            }else{
                if($gender=="male"){
                    $profileImage="male_placeholder.jpg";
                }else{
                    $profileImage="female_placeholder.jpg";
                }
            }
        }else{
            if($gender=="male"){
                $profileImage="male_placeholder.jpg";
            }else{
                $profileImage="female_placeholder.jpg";
            }
        }

        $query="insert into staffs (name,gender,rank,department,phone,email,address,profile_image,join_at) values
           ('$name','$gender','$rank','$department','$phone','$email','$address','$profileImage',$join_at)";
        $DB=new Database();
        $res=$DB->save($query);
        if($res){
            $response['status']="success";
            return $response;
        }else{
            $response['status']="fail";
            $response['error']="Failed on server operation!";
            return $response;
        }
        
    }

    public function detail($id){
        $DB=new Database();
        $query="SELECT * FROM staffs WHERE id=$id";
        $result=$DB->read($query);
        $result=$result[0];

        return $result;
    }

    public function update($data){
        $id=$data['content_id'];
        $key=$data['key'];
        $value=$data['value'];

        $query="update staffs set $key='$value' where id=$id";
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

    public function updateImage($data,$FILE){
        $id=$data['content_id'];

        $file=$FILE['myfile']['name'];
        $file_loc=$FILE['myfile']['tmp_name'];
        $folder="../../uploads/profiles/";
        if(move_uploaded_file($file_loc,$folder.$file)){
            $query="update staffs set profile_image='$file' where id=$id";
            $DB=new Database();
            $save=$DB->save($query);
            if($save){
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

    public function remove($id){
        $DB=new Database();
        $query="UPDATE staffs SET disable=1 WHERE id=$id";
        $DB->save($query);
        
    }
}

?>