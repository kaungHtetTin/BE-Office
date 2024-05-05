<?php
        include('connect.php');

        $query="Select * from products";
        $DB=new Database();
        $products=$DB->read($query);

    


       
        echo json_encode($products);

?>