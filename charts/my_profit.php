<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
            <!-- User activity statistics -->
            <div class="card card-default analysis_card p-0" id="user-activity">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div data-scroll-height="450">	
                            <div class="card-header justify-content-between">
                                <h2 class="m-0">My Profit</h2>

                                <div style="display:flex">
                                    <div class="curntusr145" style="flex:1">
                                        <p class="my-2">From</p>
                                        <input style="" type="date" id="initial_date2" name="initial_date">
                                    </div>
                                    <div class="curntusr145"  style="flex:1">
                                        <p class="my-2">To</p>
                                        <input style="" type="date" id="final_date2" name="final_date">
                                    </div>
                                </div>

                            </div>

                            <canvas id="chart2" class="chartjs p-4" style="height: 450px;"></canvas>
                            <div class="card-footer d-flex flex-wrap bg-white">
                                <div class="text-uppercase py-3 ovrvew-1">Total</div>
                                <table>
                                    <tr>
                                        <td>
                                            <br>
                                            <h5>Total Investment</h5>
                                            <span id="total_investemt2" style="font-size:15px;"> </span>
                                            <br>
                                        </td>
                                        <td>
                                            <br>
                                            <h5>Total Sale</h5>
                                            <span id="total_sale2" style="font-size:15px;"> </span>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <br>
                                            <h5>Total Extra Cost</h5>
                                            <span id="total_extra_cost2" style="font-size:15px;">  </span>
                                            <br>
                                        </td>
                                        <td>
                                            <br>
                                            <h4>Profit</h4>
                                            <span id="total_profit2" style="font-size:15px;">  </span>
                                            <br>
                                        </td>
                                    </tr>
                                </table>
                                
                            </div>
                        </div>
                         
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
    {

        let user_id=<?php echo $user['user_id'] ?>;
        let auth_token ="<?php echo $user['auth_token'] ?>";
        let products = <?php echo json_encode($products['main_product']) ?>;

        const currentDate = new Date();
        const firstDayofMonth = new Date(currentDate.getFullYear(),currentDate.getMonth(),1);
        
        let initial_default = firstDayofMonth.toISOString().split('T')[0];
        let target_default = currentDate.toISOString().split('T')[0];

        let initial_time = 0;
        let final_time = 0;

        $(document).ready(()=>{
            $('#initial_date2').val(initial_default);
            $('#final_date2').val(target_default);

            initial_time = Date.parse(initial_default);
            final_time = Date.parse(target_default);

            $('#initial_date2').on('change',()=>{
                initial_time= Date.parse($('#initial_date2').val());
                getChartData();
            })

                $('#final_date2').on('change',()=>{
                final_time = Date.parse($('#final_date2').val());
                getChartData();
            })

            getChartData();
            
        });

        function getChartData(){
            $.get(`api/chart/orderandsale.php?user_id=${user_id}&start_date=${initial_time}&end_date=${final_time}`,(data,status)=>{
              
                data = JSON.parse(data);
                setupChart(data);
                 
            });
        }

        
        function setupChart(response){

           

            let chart_labels = [];
            let chart_dataset_sales = [];
            let chart_dataset_orders = [];
            let chart_dataset_profits = [];

            let total_sale_amount = 0;
            let total_investment = 0;
            let total_profit = 0;
            let total_extra_cost = response.extra_cost;

            products.map((product,key) =>{
                chart_labels[key]= product.product_name; 

                let product_id =product.product_id; 


                if(response.hasOwnProperty("orders")){ //investment
                    if(response.orders.hasOwnProperty(product_id)){
                        let count = parseInt(response.orders[product_id].count);
                        let amount = parseInt(response.orders[product_id].amount);
                        chart_dataset_orders[key] = amount;
                        total_investment+=amount;

                    }else{
                        chart_dataset_orders[key]=0;
                    }
                }else{
                    chart_dataset_orders[key]=0;
                }


                
                if(response.hasOwnProperty("sales")){ // sale
                    if(response.sales.hasOwnProperty(product_id)){
                        let count =  parseInt(response.sales[product_id].count);
                        let amount =  parseInt(response.sales[product_id].amount);
                        chart_dataset_sales[key] = amount;
                        total_sale_amount+=amount;
                    }else{
                        chart_dataset_sales[key]=0;
                    }
                }else{
                    chart_dataset_sales[key]=0;
                }

                let diff = chart_dataset_sales[key]-chart_dataset_orders[key]
                if(diff>0){
                    chart_dataset_profits[key]=diff;
                   
                }else{
                    chart_dataset_profits[key]=0;

                }

            })

            total_profit = total_sale_amount - total_investment -total_extra_cost;
            if(total_profit<0) total_profit =0;

            $('#total_investemt2').html(total_investment);
            $('#total_sale2').html(total_sale_amount);
            $('#total_extra_cost2').html(total_extra_cost);
            $('#total_profit2').html(total_profit);
           

            var dual = document.getElementById("chart2");
            dual.innerHTML="";
            if (dual !== null) {
                var urChart = new Chart(dual, {
                type: "line",
                data: {
                    labels: chart_labels ,
                    datasets: [
                    {
                        label: "Investment",
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(255,255,255,1)",
                        pointBorderWidth: 2,
                        fill: false,
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        borderColor: "#ffc136",
                        data: chart_dataset_orders
                    },
                    {
                        label: "Sale",
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(255,255,255,1)",
                        pointBorderWidth: 2,
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        borderColor: "#ed2a26",
                        data: chart_dataset_sales
                    }
                    ,{
                        label: "Profit",
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(255,255,255,1)",
                        pointBorderWidth: 2,
                        backgroundColor: "transparent",
                        borderWidth: 2,
                        borderColor: "#0f0",
                        data: chart_dataset_profits
                    }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    layout: {
                    padding: {
                        right: 10
                    }
                    },

                    legend: {
                    display: true
                    },
                    scales: {
                    xAxes: [
                        {
                        gridLines: {
                            drawBorder: true,
                            display: true
                        },
                        ticks: {
                            display: true, // hide main x-axis line
                            beginAtZero: true
                        },
                        barPercentage: 1.8,
                        categoryPercentage: 0.2
                        }
                    ],
                    yAxes: [
                        {
                        gridLines: {
                            drawBorder: true, // hide main y-axis line
                            display: true
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true
                        }
                        }
                    ]
                    },
                    tooltips: {
                    titleFontColor: "#333",
                    bodyFontColor: "#686f7a",
                    titleFontSize: 12,
                    bodyFontSize: 14,
                    backgroundColor: "rgba(256,256,256,0.95)",
                    displayColors: true,
                    borderColor: "rgba(220, 220, 220, 0.9)",
                    borderWidth: 2
                    }
                }
                });
            }
        }

    }
</script>