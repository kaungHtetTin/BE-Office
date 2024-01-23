<?php
include('connect.php');
include('auth.php');
include('notificationpusher.php');

class Business{
    public function sentOrder($data){
        $agent_id=$data['agent_id'];
        $voucher_id=time();
        $total_amount=$data['total_amount'];
        $group_id=$data['group_id'];
        $productJSON=$data['productJSON'];
        $products=json_decode($productJSON,true);
        $price_edit=0;
        if(isset($data['price_edit'])) $price_edit=$data['price_edit'];

        $DB=new Database();
        $adminSelectQuery="select admin_id,group_name from groups where group_id=$group_id";
        $admin=$DB->read($adminSelectQuery);
        $admin_id=$admin[0]['admin_id'];
        $group_name=$admin[0]['group_name'];

        $tokenQuery="select fcm_token from users where user_id=$admin_id limit 1";
        $fcm_token=$DB->read($tokenQuery);
        $fcm_token=$fcm_token[0]['fcm_token'];

        $query1="insert into businesses (agent_id,voucher_id,total_amount,group_id,admin_id,price_edit) values
        ($agent_id,$voucher_id,$total_amount,$group_id,$admin_id,$price_edit)
        ";

      
        $DB->save($query1);

        for($i=0;$i<count($products);$i++){
            $product_id=$products[$i]['product_id'];
            $quantity=$products[$i]['quantity'];
            $foc=$products[$i]['foc'];
            $amount=$products[$i]['amount'];
            $price=$products[$i]['price'];
            $discount=$products[$i]['discount'];
            $point=$products[$i]['point'];

            $query2="insert into business_details (voucher_id,product_id,quantity,foc,amount,price,discount,point) values
            ($voucher_id,$product_id,$quantity,$foc,$amount,$price,$discount,$point)
            ";
            $DB->save($query2);

            $response['query'][$i]=$query2;
        }


        $pusher =new NotificationPusher();
        $pusher->pushNotificationToSingleUser($fcm_token,"New Order!",$group_name." group received a new order.");


        $response['status']="success";
        return $response;

    }

    public function getMyOrders($data){
        $agent_id=$data['agent_id'];
        $is_received=$data['is_received'];

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        if(isset($data['group_id'])){
            $group_id=$data['group_id'];
            $query="select  
                voucher_id,total_amount,group_name,seen,is_sold_out,is_received,group_image
                from businesses
                join groups using (group_id)
                where businesses.agent_id=$agent_id and is_received=$is_received and group_id=$group_id
                order by businesses.id desc
                limit $count,$offset
                ";
        }else{
            $query="select  
                voucher_id,total_amount,group_name,seen,is_sold_out,is_received,group_image
                from businesses
                join groups using (group_id)
                where businesses.agent_id=$agent_id and is_received=$is_received
                order by businesses.id desc
                limit $count,$offset
                ";
        }

        $DB=new Database();
        $result=$DB->read($query);
        $response['orders']=$result;
        return $response;
    }

    public function getOrders($data){
        $user_id=$data['user_id'];
        $is_sold_out=$data['is_sold_out'];

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        if(isset($data['group_id'])){
            $group_id=$data['group_id'];
            $query="select  
            voucher_id,total_amount,seen,is_sold_out,is_received,name,profile_image
            from businesses
            join users on users.user_id=businesses.agent_id
            where admin_id=$user_id and is_sold_out=$is_sold_out and group_id=$group_id
            order by businesses.id desc
            limit $count,$offset
            ";
        }else{
            $query="select  
            voucher_id,total_amount,group_name,seen,is_sold_out,is_received,group_image
            from businesses
            join groups using (group_id)
            where groups.admin_id=$user_id and is_sold_out=$is_sold_out
            order by businesses.id desc
            limit $count,$offset
            ";
        }
       

        $DB=new Database();
        $result=$DB->read($query);
        $response['orders']=$result;

        return $response;

    }



    public function getOrdersByAgent($data){
        $user_id=$data['user_id'];
        $is_sold_out=$data['is_sold_out'];
        $group_id=$data['group_id'];
        $agent_id=$data['agent_id'];

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        
        $query="select  
            voucher_id,total_amount,group_name,seen,is_sold_out,is_received,group_image
            from businesses
            join groups using (group_id)
            where businesses.group_id=$group_id and is_sold_out=$is_sold_out and businesses.agent_id=$agent_id
            order by businesses.id desc
            limit $count,$offset
            ";
       

        $DB=new Database();
        $result=$DB->read($query);
        $response['orders']=$result;

        return $response;

    }

    public function getOrderDetail($data){
        $voucher_id=$data['voucher_id'];
        $user_id=$data['user_id'];

        $DB=new Database();
        $query1="select * from businesses where voucher_id=$voucher_id";
        $result1=$DB->read($query1);
        if($result1)$response['order']=$result1[0];

        $group_id=$result1[0]['group_id'];
        $seen=$result1[0]['seen'];
        $query2="select group_id,group_name,group_image,admin_id from groups where group_id=$group_id";
        $result2=$DB->read($query2);
        if($result2)$response['group']=$result2[0];

        $admin_id=$result2[0]['admin_id'];
        $query3="select user_id,name,profile_image,phone,fcm_token from users where user_id=$admin_id";
        $result3=$DB->read($query3);
        if($result3)$response['admin']=$result3[0];

        $agent_id=$result1[0]['agent_id'];
        $query4="select user_id,name,profile_image,phone,fcm_token,address from users where user_id=$agent_id";
        $result4=$DB->read($query4);
        if($result4)$response['agent']=$result4[0];

        $query5="select * from business_details where voucher_id=$voucher_id";
        $result5=$DB->read($query5);
        if($result5)$response['details']=$result5;

        if($admin_id==$user_id&&$seen==0){
            $query6="update businesses set seen=1 where voucher_id=$voucher_id";
            $DB->save($query6);
        }

        $query7="SELECT * FROM costs WHERE voucher_id=$voucher_id";
        $result7=$DB->read($query7);
        if($result7) $response['costs']=$result7;
        return $response;
    }

    public function updateExtraCost($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['content_id'];
        $auth_token=$data['auth_token'];
        $key=$data['key'];
        $value=$data['value'];

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $query="update businesses set $key='$value' where voucher_id=$voucher_id";
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

     public function updateDetailPrice($data){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];

        $voucher_id=$data['content_id'];
        $product_id=$data['extra1'];

        $key=$data['key'];
        $value=$data['value'];

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $DB=new Database();
            $packQuery="select pack,pack_price from products where product_id=$product_id";
            $product=$DB->read($packQuery);
            $pack=$product[0]['pack'];
            $pack_price=$product[0]['pack_price'];
            
            $quantityQuery="select quantity from business_details where voucher_id=$voucher_id and product_id=$product_id";
            $resultQty=$DB->read($quantityQuery);
            
            $quantity=$resultQty[0]['quantity'];
          
            
            $quantity2=$resultQty[0]['quantity'];
            
            $quantity=$quantity/$pack;
            $updateAmount=floor($quantity)*$value;
            
             $temp=($quantity2%$pack);
             $temp=$temp*$pack_price;
             $updateAmount=$updateAmount+$temp;
        
          
            $query="update business_details set $key='$value' , amount='$updateAmount' where voucher_id=$voucher_id and product_id=$product_id";
            $result=$DB->save($query);
          
            
            
            $voucherQuery="select * from business_details where voucher_id=$voucher_id";
            $voucherDetails=$DB->read($voucherQuery);
            
            $totalAmount=0;
            for($i=0;$i<count($voucherDetails);$i++){
                $amount=$voucherDetails[$i]['amount'];
                
                $totalAmount+=$amount;
            }
            
            $query1="update businesses set price_edit=1, total_amount=$totalAmount where voucher_id=$voucher_id";
            $result1=$DB->save($query1);
            
            $response['status']="success";
            return $response;
           
        }else{
            $response['status']="fail";
            return $response;
        }
    }


    public function soldOutOrder($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['voucher_id'];
        $auth_token=$data['auth_token'];
        $stock_id=$data['stock_id'];
        $DB=new Database();

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            $response['error']="auth fail";
            return $response;
        }
        
        //check has enough qty in stock
        $query1="SELECT * FROM business_details where voucher_id=$voucher_id";
        $details=$DB->read($query1);
        $noErr=true;
        if($details){
            for($i=0;$i<count($details);$i++){
                $product_id=$details[$i]['product_id'];
                $qty=$details[$i]['quantity'];
                $foc=$details[$i]['foc'];
                $requiredQty=$qty+$foc;

                $query2="select * from stock_items where product_id=$product_id and stock_id=$stock_id";
                $product=$DB->read($query2);
                $count=$product[0]['count'];
                if($requiredQty>$count){
                    $noErr=false;
                }
            }
        }else{
            $response['status']="fail";
            $response['error']="fail in getting stock_item";
            return $response;
        }

        if($noErr){
            for($i=0;$i<count($details);$i++){
                $product_id=$details[$i]['product_id'];
                $qty=$details[$i]['quantity'];
                $foc=$details[$i]['foc'];
                $requiredQty=$qty+$foc;
                
                $query2="select * from stock_items where product_id=$product_id and stock_id=$stock_id";
                $product=$DB->read($query2);
                $count=$product[0]['count'];

                $item_left=$count-$requiredQty;

                $query3="update stock_items set count=$item_left where product_id=$product_id and stock_id=$stock_id";
                $DB->save($query3);
            }
        }else{
            $response['status']="fail";
             $response['error']="not enough qty in stock";
            return $response;
        }

       //send notification
       $query="select * from businesses where voucher_id=$voucher_id limit 1";
       $result=$DB->read($query);
       $agent_id=$result[0]['agent_id'];

       $query="select * from users where user_id=$agent_id limit 1";
       $result=$DB->read($query);
       $token=$result[0]['fcm_token'];

        $pusher =new NotificationPusher();
        $pusher->pushNotificationToSingleUser($token,"Order Delivered","Your business partner has delivered an order");

       
        $query="update businesses set is_sold_out=1,stock_id=$stock_id where voucher_id=$voucher_id and admin_id=$user_id";
        $result=$DB->save($query);
        if($result){
            $response['status']="success";
            return $response;
        }else{
            $response['status']="fail";
            $response['error']="business update fail";
            return $response;
        }


    }

    public function revceivedOrder($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['voucher_id'];
        $auth_token=$data['auth_token'];

        $DB=new Database();

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            $response['error']="auth fail";
            return $response;
        }

        $query="update businesses set is_received=1 where voucher_id=$voucher_id and agent_id=$user_id";

        $result=$DB->save($query);
        if(!$result){
            $response['status']="fail";
            $response['error']="business update fail";
            return $response;
        }

        $query1="select stock_id from stocks where owner_id=$user_id and my_stock=1";
        $stock=$DB->read($query1);
        if($stock){
            $stock_id=$stock[0]['stock_id'];
        }else{
            $response['status']="fail";
            return $response;
        }

        $query2="SELECT * FROM business_details where voucher_id=$voucher_id";
        $details=$DB->read($query2);
        if($details){
            for($i=0;$i<count($details);$i++){
                $product_id=$details[$i]['product_id'];
                $qty=$details[$i]['quantity'];
                $foc=$details[$i]['foc'];
                $requiredQty=$qty+$foc;

                $query3="select * from stock_items where product_id=$product_id and stock_id=$stock_id";
                $product=$DB->read($query3);
                $count=$product[0]['count'];

                $item_left=$count+$requiredQty;
                $query4="update stock_items set count=$item_left where product_id=$product_id and stock_id=$stock_id";
                $DB->save($query4);

            }
        }else{
            $response['status']="fail";
            return $response;
        }

        $response['status']="success";
        return $response;
    }

    public function cancelOrder($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['voucher_id'];
        $auth_token=$data['auth_token'];

        $DB=new Database();

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            $response['error']="auth fail";
            return $response;
        }

        $query1="delete from business_details where voucher_id=$voucher_id";
        $DB->save($query1);
        $query2="delete from businesses where voucher_id=$voucher_id";
        $DB->save($query2);

        $response['status']="success";
        return $response;
    }

    public function cancelOrderByAdmin($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['voucher_id'];
        $auth_token=$data['auth_token'];

        $DB=new Database();
       
        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            $response['error']="auth fail";
            return $response;
        }

        $query="select * from businesses where voucher_id=$voucher_id";
        $result=$DB->read($query);
        $stock_id=$result[0]['stock_id'];


        $query1="SELECT * FROM business_details where voucher_id=$voucher_id";
        $details=$DB->read($query1);
        $noErr=true;

        if($details){
            for($i=0;$i<count($details);$i++){
                $product_id=$details[$i]['product_id'];
                $qty=$details[$i]['quantity'];
                $foc=$details[$i]['foc'];
                $requiredQty=$qty+$foc;
                
                $query2="select * from stock_items where product_id=$product_id and stock_id=$stock_id";
                $product=$DB->read($query2);
                $count=$product[0]['count'];

                $item_left=$count+$requiredQty;

                $query3="update stock_items set count=$item_left where product_id=$product_id and stock_id=$stock_id";
                $DB->save($query3);
            }
        }else{
            $response['status']="fail";
            $response['error']="Unexpected Error Occur!";
            return $response;
        }
       
      


        if(isset($data['voucher_cancel'])){
            $del1="DELETE FROM business_details where voucher_id=$voucher_id";
            $del2="DELETE FROM businesses where voucher_id=$voucher_id";
            $del3="DELETE FROM sales where voucher_id=$voucher_id";
            $DB->save($del1);
            $DB->save($del2);
            $DB->save($del3);
        }else{
            $query="update businesses set is_sold_out=0,stock_id=0 where voucher_id=$voucher_id and admin_id=$user_id";
        }

        
        $result=$DB->save($query);
        if($result){
            $response['status']="success";
            return $response;
        }else{
            $response['status']="fail";
            $response['error']="business update fail";
            return $response;
        }
    }


    public function addSale($data){
        $admin_id=$data['admin_id'];
        $auth_token=$data['auth_token'];
        $voucher_id=$data['voucher_id'];
        $total_amount=$data['total_amount'];
        $productJSON=$data['productJSON'];
        $stock_id=$data['stock_id'];
        $extra_cost=$data['extra_cost'];

        $is_agent=$data['is_agent'];
        $customer_name=$data['customer_name'];
        $customer_phone=$data['customer_phone'];
        $customer_address=$data['customer_address'];

        $group_id=0;

        $products=json_decode($productJSON,true);

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($admin_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            $response['error']="auth fail";
            return $response;
        }


        $DB=new Database();

        $query1="insert into businesses (agent_id,voucher_id,total_amount,group_id,admin_id,stock_id,admin_extra_cost) values
        (0,$voucher_id,$total_amount,$group_id,$admin_id,$stock_id,$extra_cost)
        ";

        $DB->save($query1);

        for($i=0;$i<count($products);$i++){
            $product_id=$products[$i]['product_id'];
            $quantity=$products[$i]['quantity'];
            $foc=$products[$i]['foc'];
            $amount=$products[$i]['amount'];
            $price=$products[$i]['price'];
            $discount=$products[$i]['discount'];
            $point=$products[$i]['point'];

            $query2="insert into business_details (voucher_id,product_id,quantity,foc,amount,price,discount,point) values
            ($voucher_id,$product_id,$quantity,$foc,$amount,$price,$discount,$point)
            ";
            $DB->save($query2);

            $query3="select * from stock_items where product_id=$product_id and stock_id=$stock_id";
            $product=$DB->read($query3);
            $count=$product[0]['count'];

            $item_left=$count-$quantity-$foc;
            $query4="update stock_items set count=$item_left where product_id=$product_id and stock_id=$stock_id";
            $DB->save($query4);
        }


        // add into sale

        $query5="insert into sales (voucher_id,is_agent,customer_name,customer_phone,customer_address) values
        ($voucher_id,$is_agent,\"$customer_name\",'$customer_phone',\"$customer_address\")";
        $DB->save($query5);

        $response['status']="success";
        return $response;

    }


    public function getSales($data){
        $user_id=$data['user_id'];
        $customer=$data['customer'];

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        if($customer=="yoe"){
            $query="select  
            voucher_id,total_amount,customer_name,is_agent
            from businesses
            join sales
            using (voucher_id)
            where businesses.admin_id=$user_id
            order by businesses.id desc
            limit $count,$offset
            ";
        }else{
            $query="select  
            voucher_id,total_amount,customer_name,is_agent
            from businesses
            join sales
            using (voucher_id)
            where businesses.admin_id=$user_id and sales.customer_phone=$customer
            order by businesses.id desc
            limit $count,$offset
            ";
        }

        $DB=new Database();
        $result=$DB->read($query);
        $response['sales']=$result;

        return $response;
    }

    public function getSaleDetail($data){
        $user_id=$data['user_id'];
        $voucher_id=$data['voucher_id'];

        $DB=new Database();
        $query1 ="select stock_id,admin_extra_cost from businesses where voucher_id=$voucher_id and admin_id=$user_id";
        $voucher=$DB->read($query1);
        $response['voucher']=$voucher[0];

        $stock_id=$voucher[0]['stock_id'];
        $query2="select name from stocks where stock_id=$stock_id";
        $stock=$DB->read($query2);
        $response['stock_name']=$stock[0]['name'];

        $query3="select is_agent,customer_phone,customer_name,customer_address,delivery_fee from sales where voucher_id=$voucher_id";
        $sale=$DB->read($query3);
        $response['sale']=$sale[0];

        $query4="select
        product_name,products.product_id,quantity,foc,business_details.amount,business_details.price,business_details.discount,business_details.point
        from business_details 
        join products using (product_id)
        where voucher_id=$voucher_id";
        $details=$DB->read($query4);
        $response['details']=$details;

        $query5="SELECT * FROM costs WHERE voucher_id=$voucher_id";
        $result5=$DB->read($query5);
        if($result5) $response['costs']=$result5;


        return $response;
    }

}

?>