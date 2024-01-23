<?php
include('connect.php');
include('auth.php');

Class Cost{
    public function addCostCategory($data){
        $title=$data['value'];
        $DB=new Database();
        $query="INSERT INTO cost_categories (title) VALUES ('$title')";
        $result=$DB->save($query);

        
        $response['status']="success";
        return $response;

    }

    public function getCostCategory(){
        $DB=new Database();
        $query="SELECT * FROM cost_categories";
        $result=$DB->read($query);
        return $result;
    }

    public function addCost($data){
        $title=$data['title'];
        $category_id=$data['category_id'];
        $amount=$data['amount'];
        $voucher_id=$data['voucher_id']; // 0 for cases that are not relative to vouchers and id for salary
        $time=time();

        $DB=new Database();
        $query="INSERT INTO costs (title,category_id,amount,voucher_id,time) VALUES
        ('$title',$category_id,$amount,$voucher_id,$time)";
        $DB->save($query);

        $response['status']="success";
        $response['query']=$query;
        return $response;

    }

    public function getCostByVoucher($data){
        $voucher_id=$data['voucher_id'];
        $DB=new Database();

        $query="SELECT * FROM costs WHERE voucher_id=$voucher_id";
        $result =$DB->read($query);
        return $result;
    }

    public function get($data){
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;

        $DB=new Database();
        $query="SELECT * FROM costs
        WHERE  time>=$initial_time and time<= $final_time ORDER BY id DESC
        ";

        $result=$DB->read($query);
        return $result;
    }

    public function delete($id){
        $DB=new Database();
        $query="DELETE FROM costs WHERE id=$id";
        $DB->save($query);

        $response['status']="success";
        $response['query']=$query;
        return $response;

    }

    public function getPayments($id){
        $DB=new Database();
        $query="SELECT * FROM costs WHERE voucher_id=$id";
        $result=$DB->read($query);
        return $result;
    }
}

?>