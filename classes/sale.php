<?php
include('connect.php');
include('auth.php');

class Sale{
    public function getCustomers($data){
        $user_id=$data['user_id'];

        $offset=30;
        $page=$data['page'];
        $page=$page-1;
        $count=$page*$offset;

        $query="SELECT 
            sales.is_agent,sales.customer_phone,sales.customer_name,sales.customer_address
            FROM sales
            JOIN businesses USING (voucher_id)
            WHERE admin_id=$user_id
            group BY (customer_phone)
            ORDER BY customer_name
            limit $count,$offset
        ";

        $DB=new Database();
        $result=$DB->read($query);

        return $result;
    }

    public function searchCustomers($data){
        $user_id=$data['user_id'];
        $search=$data['search'];

        // $offset=30;
        // $page=$data['page'];
        // $page=$page-1;
        // $count=$page*$offset;

        $query="SELECT 
            sales.is_agent,sales.customer_phone,sales.customer_name,sales.customer_address
            FROM sales
            JOIN businesses USING (voucher_id)
            WHERE admin_id=$user_id and 
            ( match (customer_name)against ('$search') 
            or match (customer_address) against('$search')
            or match (customer_phone) against('$search') )
            group BY (customer_phone)
            ORDER BY customer_name
           
        ";

        $DB=new Database();
        $result=$DB->read($query);

        return $result;
    }
}

?>