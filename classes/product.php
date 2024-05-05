<?php
include_once('connect.php');

class Product {
    public function getProducts(){
        $query="Select * from products";
        $DB=new Database();
        $products=$DB->read($query);

        $query2="Select * from brands";
        $result['brands']=$DB->read($query2);
    
        $query3="select * from ranks";
        $result['ranks']=$DB->read($query3);

        $query4="SELECT * FROM prices";
        $prices=$DB->read($query4);



        $query5="SELECT quantity FROM prices GROUP BY quantity";
        $quantities=$DB->read($query5);

        for($j=0;$j<count($products);$j++){
            $product=$products[$j];

            $i=0;
            $index;
            foreach($quantities as $qty){

                
                for($k=0;$k<count($prices);$k++){
                    $price=$prices[$k];
                    
                    if($product['product_id']==$price['product_id']){
                    
                        if($qty['quantity']==$price['quantity']){
                            $index=$k;
                        }   
                    }
                }
                
                $products[$j]['prices'][$i]['quantity']=$qty['quantity'];
                $products[$j]['prices'][$i]['price']=$prices[$index]['price'];
                $i++; 

            }
        }

        $result['main_product']=$products;
       

        return $result;
    }
   
}


?>