<?php
include('connect.php');
include('auth.php');

class Filter{
    public function getOverView($data){
        $user_id=$data['user_id'];
        $initial_time=$data['initial_time']/1000;
        $final_time=$data['final_time']/1000;
        $is_sold_out=$data['is_sold_out'];

        $DB=new Database();

        $query="
        select 
        sum(admin_extra_cost) as extra_cost,
        sum(total_amount) as total_amount
        from businesses
        join business_details
        using (voucher_id)
        where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id and is_sold_out=$is_sold_out
        ";

        $result1=$DB->read($query);
        $response['overview']=$result1;

      $query3="
            select
            sum(quantity) as quantity, 
            product_id,
            foc,
            point,
            amount
            from business_details
            join businesses
            using(voucher_id)
            where admin_id=$user_id and is_sold_out=$is_sold_out
            group by product_id
        ";

        $result3=$DB->read($query3);
        $response['products']=$result3;
        
        return $response;

    }
}

?>