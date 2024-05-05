<style>
    .btn_yr{
        padding:10px;
        border-radius:50px;
        border:1px solid black;
        margin:10px;
        cursor:pointer;
    }

    #tv_year{
        padding:10px;
        border-radius:50px;
        border:1px solid black;
        margin:10px;
    }

    .btn_yr:hover{
        background:#F6EAEC;
        color:#D79DA9;
    }
</style>

<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
            <!-- User activity statistics -->
            <div class="card card-default analysis_card p-0" id="user-activity">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div data-scroll-height="450">	
                            <div class="card-header justify-content-between">
                                <h2 class="m-0">Monthly Profit</h2>
                                <br>
                                <div style="display:flex">
                                    <span id="btn_previous_yr" class="btn_yr"> < </span>
                                    <span id="tv_year"> 2024 </span>
                                    <span id="btn_next_yr" class="btn_yr" > > </span>
                                </div>

                            </div>

                            <canvas id="chart3" class="chartjs p-4" style="height: 450px;"></canvas>
                            <div class="card-footer d-flex flex-wrap bg-white">
                                <div class="text-uppercase py-3 ovrvew-1">Total</div>
                                <table>
                                    <tr>
                                        <td>
                                            <br>
                                            <h5>Total Investment</h5>
                                            <span id="total_investemt3" style="font-size:15px;">  </span>
                                            <br>
                                        </td>
                                        <td>
                                            <br>
                                            <h5>Total Sale</h5>
                                            <span id="total_sale3" style="font-size:15px;"> </span>
                                            <br>
                                        </td>
                                        <td>
                                            <br>
                                            <h4>Profit</h4>
                                            <span id="total_profit3" style="font-size:15px;">  </span>
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

        const currentYear = currentDate.getFullYear();
        let targetYear = currentDate.getFullYear(); // initialize the target year
        
        let initial_time = 0;
        let final_time = 0;

        function calculateDuration(){
            initial_time = Date.parse(new Date(targetYear,0,1));
            final_time =Date.parse( new Date(targetYear,11,32));

            if(targetYear == currentYear){
                $('#tv_year').html('Current Year');
            }else{
                $('#tv_year').html(targetYear);
            }
        }

        $(document).ready(()=>{
            
            calculateDuration();

            
            $('#btn_previous_yr').click(()=>{
                targetYear--;
                calculateDuration();
                getChartData();
            })

            $('#btn_next_yr').click(()=>{
                targetYear++;
                calculateDuration();
                getChartData();
            })


            getChartData();
            
        });

        function getChartData(){
            $.get(`api/chart/profitpermonth.php?user_id=${user_id}&start_date=${initial_time}&end_date=${final_time}`,(data,status)=>{
              
                data = JSON.parse(data);
                setupChart(data);
                 
            });
        }

        
        function setupChart(response){

            let chart_labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            let chart_dataset_sales = [];
            let chart_dataset_orders = [];
            let chart_dataset_profits = [];

            let total_sale_amount = 0;
            let total_investment = 0;
            let total_profit = 0;

           
            for(var i =0;i<12;i++){
                let indexMonth = i+1+"";
                let investment_amount =0;
                let sale_amount = 0;

                if(response.hasOwnProperty("orders")){ //investment
                    if(response.orders.hasOwnProperty(indexMonth)){
                        let cost = parseInt(response.orders[indexMonth].agent_extra_cost);
                        let amount = parseInt(response.orders[indexMonth].total_amount);
                        investment_amount = cost+amount;
                        chart_dataset_orders[i]=investment_amount;
                        total_investment+=investment_amount;

                    }else{
                        chart_dataset_orders[i]=0;
                    }
                }else{
                    chart_dataset_orders[i]=0;
                }

                if(response.hasOwnProperty("sales")){ // sales
                    if(response.sales.hasOwnProperty(indexMonth)){

                        let cost = parseInt(response.sales[indexMonth].admin_extra_cost);
                        let amount = parseInt(response.sales[indexMonth].total_amount);
                        sale_amount = amount - cost;
                        chart_dataset_sales[i]=sale_amount;
                        total_sale_amount+=sale_amount;
                      
                    }else{
                        chart_dataset_sales[i]=0;
                    }
                }else{
                    chart_dataset_sales[i]=0;
                }

                let profit_amount = sale_amount -investment_amount;
                if(profit_amount>0){
                    chart_dataset_profits[i] = profit_amount;
                    total_profit+=profit_amount;
                }else{
                    chart_dataset_profits[i]=0;
                }
            }

            $('#total_investemt3').html(total_investment);
            $('#total_sale3').html(total_sale_amount);
            $('#total_profit3').html(total_profit);

            var dual = document.getElementById("chart3");
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