<?php 
include('connect.php');
include('auth.php');

class Stock{
     
    public function addNewStock($data){
        $owner_id=$data['user_id'];
        $name=$data['value'];

        $query="insert into stocks (owner_id,name) values ('$owner_id','$name')";
 
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

        if($errChecker){
            $response['status']="success";
            return $response;
        }else{
            $response['status']="fail";
            return $response;
        }

         
    }

    public function getStocks($data){
        $owner_id=$data['owner_id'];
        $query="select * from stocks where owner_id=$owner_id";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function getProductLeftByStock($data){
        $owner_id=$data['owner_id'];
        $query="select * from stocks where owner_id=$owner_id";
        $DB=new Database();

        $stocks=$DB->read($query);
        for($i=0;$i<count($stocks);$i++){
            $stock_id=$stocks[$i]['stock_id'];
            $data[$i]['stock']=$stocks[$i];
            $query2="select products.product_id,product_name,count from stock_items 
            join products on products.product_id=stock_items.product_id
            where stock_id=$stock_id";
            $result=$DB->read($query2);
            $stocks[$i]['items']=$result;

        }

        return $stocks;
        
    }

    public function getProductLeftByOneStock($data){
        $stock_id=$data['stock_id'];
        $query="select stock_items.id, products.product_id,product_name,count 
                from stock_items 
                join products on products.product_id=stock_items.product_id
                where stock_id=$stock_id";

        
        $DB=new Database();
        $result=$DB->read($query);

        return $result;
    }


    public function updateItemLeft($data){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];
        $key=$data['key'];
        $value=$data['value'];
        $id=$data['content_id'];
        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData!=null){
            $query="update stock_items set $key=$value where id=$id";
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


    public function transferProduct($data){
        $user_id=$data['user_id'];
        $auth_token=$data['auth_token'];

        $initial_stock_id=$data['initial_stock_id'];
        $target_stock_id=$data['target_stock_id'];

        $initial_products=json_decode($data['initial_json'],true);
        $target_products=json_decode($data['target_json'],true);

        $Auth=new Auth();
        $userData=$Auth->checkAuthAndGetData($user_id,$auth_token);
        if($userData==null){
            $response['status']="fail";
            return $response;
        }


        $DB=new Database();
        for($i=0;$i<count($initial_products);$i++){
            $product_id=$initial_products[$i]['product_id'];
            $count=$initial_products[$i]['count'];

            $query="update stock_items set count=$count where product_id=$product_id and stock_id=$initial_stock_id";
            $DB->save($query);

        }

        for($i=0;$i<count($target_products);$i++){
            $product_id=$target_products[$i]['product_id'];
            $count=$target_products[$i]['count'];

            $query="update stock_items set count=$count where product_id=$product_id and stock_id=$target_stock_id";
            $DB->save($query);
            
        }

        $response['status']="success";
        return $response;
    }
    

    public function getInvestment($data){
        $owner_id=$data['owner_id'];
        $query ="select 
        product_id, SUM(count) as total,product_name 
        from stock_items 
        join products using(product_id)
        where owner_id=$owner_id group by product_id";
        $DB=new Database();
        $result=$DB->read($query);

        return $result;
    }
}

?>