<?php
include('connect.php');
include('auth.php');

class Group{

    function create_group($data,$FILE){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];
        $group_name=$data['group_name'];
        $group_description=$data['group_description'];
        $image_path="";
        $time=time();

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $file=$FILE['myfile']['name'];
            $file_loc=$FILE['myfile']['tmp_name'];
            $folder="../../uploads/groups/";
            if(move_uploaded_file($file_loc,$folder.$file)){
                $query="insert into groups (group_name,group_description,group_image,admin_id,time) values
                    (\"$group_name\",\"$group_description\",'$file',$user_id,$time);";
                $DB=new Database();
                $res=$DB->save($query);
                if($res){
                    $response['status']="success";
                    return $response;
                }else{
                    $response['status']="fail";
                    $response['error']="error 900";
                    return $response;
                }
            }else{
                $response['status']="fail";
                $response['error']="902";
                return $response;
            }


        }else{
            $response['status']="fail";
            $response['error']="901";
            return $response;
        }

    }


    public function getMyGroup($data){

        $user_id=$data['user_id'];
        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        $query="select * from groups where admin_id=$user_id and disable=0 limit $count,$offset";
       

        $DB=new Database();
        $result=$DB->read($query);
     

        if($result){
            $response['status']="success";
            $response['groups']=$result;
           
            
        }else{
            $response['status']="fail";
            $response['error']="900";
        }
        return $response;

    }


    public function getGroupMembers($data){

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        
        $group_id=$data['group_id'];
        $count=$page*$offset;

        $query="select user_id,name,profile_image from group_members 
                join users on member_id=user_id
                where group_id=$group_id and disable=0
                limit $count,$offset ";

        $DB=new Database();
        $result=$DB->read($query);
        $response['members']=$result;
        return $response;


    }

    public function addMembers($data){
        $user_id=$data['user_id'];
        $group_id=$data['group_id'];
        $auth_token=$data['auth_token'];
        $time=time();

        $members=$data['members'];
        $members=json_decode($members,true);

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);

        if($userData!=null){
          
            for($i=0;$i<count($members);$i++){
                $member_id=$members[$i]['member_id'];
                $member_name=$members[$i]['name'];
                $DB=new Database();
                
                if($user_id!=$member_id){
                    
                    $query="update users set verified=1 where user_id=$member_id";
                    $DB->save($query);
    
                    $queryCheck="select * from group_members where group_id=$group_id and member_id=$member_id limit 1";
                   
                    $check=$DB->read($queryCheck);
                    
                    
                    if($check){
                        $Info[$member_name]="had already added in this group";
                    }else{
                            $query="insert into group_members (group_id,member_id,time) values($group_id,$member_id,$time)";
                            $save=$DB->save($query);
           
                            $Info[$member_name]="had successfully added in this group";
                    
                    }
                }
                
            }
            return $Info;
            
        }

    }

    public function disableGroupMember($data){
        $group_id=$data['group_id'];
        $member_id=$data['member_id'];
        $auth_token=$data['auth_token'];
        $user_id=$data['user_id'];
        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        
        if($userData!=null){
            $query="update group_members set disable=1 where group_id=$group_id and member_id=$member_id";
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

    public function getPartnerGroups($data){

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        $user_id=$data['user_id'];

        $query ="select group_id,group_name,group_image,group_description from groups
        join group_members using (group_id)
        where member_id=$user_id and groups.disable=0 limit $count,$offset";

        $DB=new Database();
        $result=$DB->read($query);
        $response['groups']=$result;
        return $response;
    }

    public function getOrderGroups($data){
        $user_id=$data['user_id'];

        $query="select group_id,group_name,group_image,users.name as admin
        from groups
        join group_members using (group_id)
        join users on groups.admin_id=users.user_id
        where group_members.member_id=$user_id and group_members.disable=0 and groups.disable=0
        ";

      
        $DB=new Database();
        $result=$DB->read($query);
        $response['groups']=$result;
        return $response;

    }

      public function updateGroup($data){
        $user_id=$data['user_id'];
        $group_id=$data['content_id'];
        $auth_token=$data['auth_token'];
        $key=$data['key'];
        $value=$data['value'];

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $query="update groups set $key=\"$value\" where admin_id=$user_id and group_id=$group_id";
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

    public function updateGroupImage($data,$FILE){

        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];
        $group_id=$data['content_id'];

        if(!is_numeric($user_id)||!is_numeric($group_id)){
            $response['status']="fail";
            return $response;
        }

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);

        if($userData!=null){
            $file=$FILE['myfile']['name'];
            $file_loc=$FILE['myfile']['tmp_name'];
            $folder="../../uploads/groups/";
            if(move_uploaded_file($file_loc,$folder.$file)){
                $query="update groups set group_image='$file' where admin_id=$user_id and group_id=$group_id";
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

        }else{
            $response['status']="fail";
            return $response;
        }
    }

     public function getDetail($data){
        $group_id=$data['group_id'];
        $query ="select * from groups where group_id=$group_id";
        $DB=new Database();
        $result=$DB->read($query);

        $query ="select * from target_plans where user_id=$group_id";
        $target_plan=$DB->read($query);

        if($target_plan){
            $result[0]['target_plan']=$target_plan[0];
        }
        return $result[0];
    }

     public function getAboutGroup($group_id){
        $query="select
        group_name,group_description,group_image,groups.time as group_create_at,
        name as founder,profile_image,phone,group_members.time as join_at
        from groups 
        join users on users.user_id=groups.admin_id 
        join group_members using(group_id)
        where group_id=$group_id
        ";
        $DB=new Database();
        $result=$DB->read($query);

        return $result[0];

    }

    public function disableGroup($data){
        $user_id=$data['user_id'];
        $group_id=$data['group_id'];
        $auth_token=$data['auth_token'];

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        
        if($userData!=null){
            $query="update groups set disable=1 where group_id=$group_id and admin_id=$user_id";
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

    public function getMemberProfile($data){
        $member_id=$data['member_id'];
        $group_id=$data['group_id'];

        $DB=new Database();
        $query="select
            name,profile_image,email,phone,address,official_agent_id,rank_id
            from users where user_id=$member_id limit 1
        ";
        $info=$DB->read($query);
        $response['info']=$info[0];

        $query="select time from group_members where group_id=$group_id and member_id=$member_id limit 1";
        $joinedDate=$DB->read($query);
        $response['joinDate']=$joinedDate[0];

        return $response;

    }

    public function getTargetPlanAndOrderRate($data){
        $group_id=$data['group_id'];
        $member_id=$data['member_id'];

        $DB=new Database();
        $query1="select * from target_plans where user_id=$group_id";
        $result1=$DB->read($query1);
        $response['target_plan']=$result1[0];
        
        $target_plan_id=$result1[0]['target_plan_id'];
        $initial_time=$result1[0]['start_date'];
        $final_time=$result1[0]['end_date'];

        $query2="select * from target_plan_details where target_plan_id=$target_plan_id";
        $result2=$DB->read($query2);

        if($result2){
            for($i=0;$i<count($result2);$i++){
                $product_id=$result2[$i]['product_id'];
                $plan_detail[$product_id]['count']=$result2[$i]['count'];
            }

            $response['target_plan_detail']=$plan_detail;
        }

        $query3="
            select 
            product_id,
            sum(quantity) as count
            from businesses
            join business_details
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and agent_id=$member_id
            group by product_id
        ";

        $result3=$DB->read($query3);
        if($result3){
            for($i=0;$i<count($result3);$i++){
                $product_id=$result3[$i]['product_id'];
                $order_detail[$product_id]['count']=$result3[$i]['count'];
            }
            $response['sale_detail']=$order_detail; //this is sale rate
        }

        return $response;
    }

}