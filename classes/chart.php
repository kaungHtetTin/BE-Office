<?php
include('connect.php');
include('auth.php');

class Chart{


    public function orderAndSale($data){
        $user_id=$data['user_id'];
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;
        $response['hello']="world";
        $DB=new Database();
        $query1=" select 
            product_id,
            sum(quantity) as count,
            sum(foc) as foc,
            sum(amount) as amount
            from businesses
            join business_details
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and agent_id=$user_id
            group by product_id";
        $result1=$DB->read($query1);

        if($result1){
            for($i=0;$i<count($result1);$i++){
                $product_id=$result1[$i]['product_id'];
                $orders[$product_id]['count']=$result1[$i]['count']+$result1[$i]['foc'];
                $orders[$product_id]['amount']=$result1[$i]['amount'];
            }

            $response['orders']=$orders;
        }

        $query2=" select 
            product_id,
            sum(quantity) as count,
            sum(foc) as foc,
            sum(amount) as amount
            from businesses
            join business_details
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id
            group by product_id";
        $result2=$DB->read($query2);
        if($result2){
            for($i=0;$i<count($result2);$i++){
                $product_id=$result2[$i]['product_id'];
                $sales[$product_id]['count']=$result2[$i]['count']+$result2[$i]['foc'];
                $sales[$product_id]['amount']=$result2[$i]['amount'];
            }

            $response['sales']=$sales;
        }

        $query3="select sum(admin_extra_cost) as admin_extra_cost
        from businesses
        where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id
        ";


        $adminCost=$DB->read($query3);
        if(count($adminCost)>0)$adminCost=$adminCost[0]['admin_extra_cost'];
        else $adminCost=0;

        $query4="select sum(agent_extra_cost) as agent_extra_cost
        from businesses
        where voucher_id>=$initial_time and voucher_id<= $final_time and agent_id=$user_id";
        $agentCost=$DB->read($query4);

        if(count($agentCost)>0)$agentCost=$agentCost[0]['agent_extra_cost'];
        else $agentCost=0;
        $totalCost=$adminCost+$agentCost;

        $response['extra_cost']=$totalCost;

        // this is only for CEO
        $query5="SELECT sum(amount) AS cost FROM costs 
        where costs.time>=$initial_time and costs.time<= $final_time";
        $CEOcost=$DB->read($query5);
        $response['CEOcost']=$CEOcost[0]['cost'];

        return $response;
    }

    public function getProfitPerMonth($data){

        $user_id=$data['user_id'];
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;


        $query1="SELECT Month(FROM_UNIXTIME(voucher_id)) as month, sum(total_amount) as total_amount,
        sum(agent_extra_cost) as agent_extra_cost
        FROM businesses 
        WHERE  voucher_id>=$initial_time and voucher_id<= $final_time and  agent_id=$user_id
        GROUP BY Month(FROM_UNIXTIME(voucher_id)); ";

        $query2="SELECT Month(FROM_UNIXTIME(voucher_id)) as month, sum(total_amount) as total_amount, 
        sum(agent_extra_cost) as admin_extra_cost 
        FROM businesses 
        WHERE voucher_id>=$initial_time and voucher_id<=$final_time and admin_id=$user_id
        GROUP BY Month(FROM_UNIXTIME(voucher_id)); ";


        $DB=new Database();
        $orders=$DB->read($query1);
        $sales=$DB->read($query2);

        $response['hello']="world";
        if($orders){
            for($i=0;$i<count($orders);$i++){
               $month=$orders[$i]['month'];
               $orderResponse[$month]=$orders[$i];
            }
            $response['orders']=$orderResponse;
        }
        
        if($sales){
            for($i=0;$i<count($sales);$i++){
                $month=$sales[$i]['month'];
                $saleResponse[$month]=$sales[$i];
            }
            $response['sales']=$saleResponse;
        }
        return $response;
    }

    public function getRetailAndAgentRate($data){

        $user_id=$data['user_id'];
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;

        $response['hello']="world";
        $DB=new Database();

        $query1=" select 
            product_id,
            sum(quantity) as count,
            sum(business_details.amount) as amount,
            sum(foc) as foc
            from businesses
            join business_details
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id and agent_id!=0
            group by product_id

            union

            select 
            product_id,
            sum(quantity) as count,
            sum(business_details.amount) as amount,
            sum(foc) as foc
            from businesses
            join business_details
            using (voucher_id)
            join sales 
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id and sales.is_agent=1
            group by product_id
            ";

        $query2=" select 
            product_id,
            sum(quantity) as count,
            sum(business_details.amount) as amount,
            sum(foc) as foc
            from businesses
            join business_details
            using (voucher_id)
            join sales 
            using (voucher_id)
            where voucher_id>=$initial_time and voucher_id<= $final_time and admin_id=$user_id and sales.is_agent=0
            group by product_id
            ";

        $result1=$DB->read($query1);
        if($result1){
            for($i=0;$i<count($result1);$i++){
                $product_id=$result1[$i]['product_id'];
                $agents[$product_id]['count']=$result1[$i]['count']+$result1[$i]['foc'];
                $agents[$product_id]['amount']=$result1[$i]['amount'];
            }

            $response['agents']=$agents;
        }

        $result2=$DB->read($query2);
        if($result2){
            for($i=0;$i<count($result2);$i++){
                $product_id=$result2[$i]['product_id'];
                $retails[$product_id]['count']=$result2[$i]['count']+$result2[$i]['foc'];
                $retails[$product_id]['amount']=$result2[$i]['amount'];
            }

            $response['retails']=$retails;
        }

        return $response;

    }

    public function saleRateForAItem($data){

        $user_id=$data['user_id'];
        $product_id=$data['product_id'];
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;


        //my order rate
        $query1="SELECT 
        Month(FROM_UNIXTIME(voucher_id)) as month, 
        sum(quantity) as quantity,
        sum(foc) as foc
        FROM business_details
        JOIN businesses
        USING (voucher_id)
        WHERE  voucher_id>=$initial_time and voucher_id<= $final_time and  agent_id=$user_id and product_id=$product_id
        GROUP BY Month(FROM_UNIXTIME(voucher_id)); ";

        //sale rate
        $query2="SELECT 
        Month(FROM_UNIXTIME(voucher_id)) as month, 
        sum(quantity) as quantity,
        sum(foc) as foc
        FROM business_details
        JOIN businesses
        USING (voucher_id)
        WHERE  voucher_id>=$initial_time and voucher_id<= $final_time and  admin_id=$user_id and product_id=$product_id
        GROUP BY Month(FROM_UNIXTIME(voucher_id)); ";

        $DB=new Database();
        $orders=$DB->read($query1);
        $sales=$DB->read($query2);

        $response['hello']="world";
        if($orders){
            for($i=0;$i<count($orders);$i++){
               $month=$orders[$i]['month'];
               $orderResponse[$month]=$orders[$i];
            }
            $response['orders']=$orderResponse;
        }
        
        if($sales){
            for($i=0;$i<count($sales);$i++){
                $month=$sales[$i]['month'];
                $saleResponse[$month]=$sales[$i];
            }
            $response['sales']=$saleResponse;
        }

        $response['query1']=$query1;
        $response['query2']=$query2;
        return $response;
    }

    public function costAnalysis($data){
        $initial_time=$data['start_date']/1000;
        $final_time=$data['end_date']/1000;

        $DB=new Database();
        $query="SELECT title,category_id,SUM(amount) as amount FROM costs 
        where time>=$initial_time and time<= $final_time
        GROUP BY category_id";

        $result1=$DB->read($query);
        if($result1){
            for($i=0;$i<count($result1);$i++){
                $category_id=$result1[$i]['category_id'];
                $costs[$category_id]['amount']=$result1[$i]['amount'];
            }
        }

        return $costs;
    }

}

?>