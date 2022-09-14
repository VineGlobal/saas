@extends('theme::lclayouts.app')

@section('head')
    <title>LandedCost.io - Admin Dashboard</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
jQuery(document).ready(function(){
  //$("button").click(function(){
    jQuery.get("lc-api/totalLCtransactionscount", function(data, status){
     // alert("Data: " + data + "\nStatus: " + status);
      jQuery("#totalTransactions").text(data);
    });
  //});
  
   jQuery.get("lc-api/yearlyLCTransactions", function(data, status){ 
       jQuery("#yearlyTransactions").text(data);
   });
    
   jQuery.get("lc-api/monthlyLCTransactions", function(data, status){
        jQuery("#monthlyTransactions").text(data);
   });
    
   jQuery.get("lc-api/dailyLCTransactions", function(data, status){  
        jQuery("#dailyTransactions").text(data);
   });
     
  
});
</script>

 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  

<script> 
 
 var myLineChart;
 $(document).ready(function(){  
 

// for now, let's assume sample data
/* let _data = {
  "Dates" : [
    "2021-08-02",
    "2021-08-03",
    "2021-08-04",
    "2021-08-05",
    "2021-08-06"
  ],
  "Calls": [
    6,
    4,
    3,
    8,
    2
  ]
};
*/

jQuery('#currentMonthButton').attr("disabled", "disabled");
jQuery('#currentYearButton').attr("disabled", "disabled");
var _data = new Array();  
 
jQuery.ajax({
    url: "lc-api/currentMonthLCChart",
    type: 'GET', 
    dataType:"json",
    success: function(data) {
       // alert(data.calls[0]);
        _data = data;
        
        const ctx = document.getElementById('lineChart').getContext('2d');

        myLineChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: _data.dates,
            datasets: [{
              label: 'API Calls',
              data: _data.calls,
              backgroundColor: 'rgb(30, 64, 175)',
              borderColor: 'rgb(30, 64, 175)', 
              animations: false
            }]
          }
        });  
        
        jQuery('#chartTitle').text(_data.title);
        jQuery('#currentMonthButton').removeClass('btn-secondary').addClass('btn-primary');
        jQuery('#currentYearButton').removeAttr("disabled");
        
   }
});     
}); 
  
 
function currentMonth() {
jQuery('#chartTitle').text("Loading...");
 jQuery('#currentMonthButton').removeClass('btn-secondary').addClass('btn-primary');
 jQuery('#currentYearButton').removeClass('btn-primary').addClass('btn-secondary');
 jQuery('#currentMonthButton').attr("disabled", "disabled");
 jQuery('#currentYearButton').attr("disabled", "disabled");
 var _data = new Array(); 
  jQuery.ajax({
    url: "lc-api/currentMonthLCChart",
    type: 'GET', 
    dataType:"json",
    success: function(data) { 
        _data = data;  
        myLineChart.data.labels = _data.dates;
        myLineChart.data.datasets[0].data = _data.calls; 
        myLineChart.update();
        $('#chartTitle').text(_data.title);
        $('#currentYearButton').removeAttr("disabled"); 
   }
}); 
};   


function currentYear() {  
   
  jQuery('#chartTitle').text("Loading...");
  jQuery('#currentYearButton').removeClass('btn-secondary').addClass('btn-primary');
  jQuery('#currentMonthButton').removeClass('btn-primary').addClass('btn-secondary');
  jQuery('#currentMonthButton').attr("disabled", "disabled");
  jQuery('#currentYearButton').attr("disabled", "disabled");
  
  var _data = new Array(); 
  jQuery.ajax({
    url: "lc-api/currentYearLCChart",
    type: 'GET', 
    dataType:"json",
    success: function(data) { 
        _data = data;  
        myLineChart.data.labels = _data.dates;
        myLineChart.data.datasets[0].data = _data.calls; 
        myLineChart.update();
        $('#chartTitle').text(_data.title);
        $('#currentMonthButton').removeAttr("disabled"); 
   }
});
    
}   
  
</script>  
    
@endsection

@section('content') 
     
     <!----- START of Notifications -->
     
     
     
     <!-- END of Notificaitons -->
       <!-- BEGIN: General Report -->
                <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Landed Cost Calculator API Metrics</h2>
                        <a href="" class="ml-auto flex items-center text-primary">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Reload Data
                        </a>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-5">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5"> 
                                    <div class="text-3xl font-medium leading-8 mt-6" id="totalTransactions">Loading...</div>
                                    <div class="text-base text-slate-500 mt-1"># Total API Calls</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5"> 
                                    <div class="text-3xl font-medium leading-8 mt-6" id="yearlyTransactions">Loading...</div>
                                    <div class="text-base text-slate-500 mt-1"># API Calls This Year</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5"> 
                                    <div class="text-3xl font-medium leading-8 mt-6" id="monthlyTransactions">Loading...</div>
                                    <div class="text-base text-slate-500 mt-1"># API Calls This Month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5"> 
                                    <div class="text-3xl font-medium leading-8 mt-6"  id="dailyTransactions">Loading...</div>
                                    <div class="text-base text-slate-500 mt-1"># API Calls Today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: General Report -->
    
   <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Landed Cost Calculator API Charts</h2>
                        <a href="" class="ml-auto flex items-center text-primary">
                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Reload Data
                        </a>
                    </div>
   
        <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="text-2xl font-medium leading-2" id="chartTitle">Loading...</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        
                        <button class="btn btn-secondary w-28 inline-block mr-1 mb-2" onclick="currentMonth()" id="currentMonthButton">Current Month</button>
                        <button class="btn btn-secondary w-28 inline-block mr-1 mb-2" onclick="currentYear()" id="currentYearButton">Current Year</button> 
                    </div>
                </div>
                <div id="vertical-bar-chart" class="p-5">
                    <div class="preview">
                        <div class="h-[400px]">    
                            <canvas id="lineChart" width="570" height="180" style="display: block; box-sizing: border-box; height: 180px; width: 570px;">
                            </canvas>
                        </div>
                    </div> 
                </div>
        </div>
   </div>
            
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Regular Table</h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 lg:col-span-6">
            <!-- BEGIN: Basic Table -->
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Basic Table</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-1">Show example code</label>
                        <input id="show-example-1" data-target="#basic-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="basic-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-dark mt-5">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-basic-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-basic-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table table-dark mt-5">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Basic Table -->
            <!-- BEGIN: Bordered Table -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Bordered Table</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-2">Show example code</label>
                        <input id="show-example-2" data-target="#bordered-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="bordered-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-bordered-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-bordered-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Bordered Table -->
            <!-- BEGIN: Hoverable Table -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Hoverable Table</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-3">Show example code</label>
                        <input id="show-example-3" data-target="#hoverable-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="hoverable-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-hoverable-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-hoverable-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Hoverable Table -->
            <!-- BEGIN: Table Row States -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Table Row States</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-4">Show example code</label>
                        <input id="show-example-4" data-target="#row-states-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="row-states-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-primary text-white">
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr class="bg-danger text-white">
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr class="bg-warning">
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-row-states-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-row-states-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="bg-primary text-white">
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr class="bg-danger text-white">
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr class="bg-warning">
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Table Row States -->
        </div>
        <div class="intro-y col-span-12 lg:col-span-6">
            <!-- BEGIN: Table Head Options -->
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Table Head Options</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-5">Show example code</label>
                        <input id="show-example-5" data-target="#head-options-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="head-options-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table mt-5">
                                <thead class="table-light">
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-head-options-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-head-options-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mt-5">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Table Head Options -->
            <!-- BEGIN: Responsive Table -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Responsive Table</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-6">Show example code</label>
                        <input id="show-example-6" data-target="#responsive-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="responsive-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                        <th class="whitespace-nowrap">Email</th>
                                        <th class="whitespace-nowrap">Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="whitespace-nowrap">1</td>
                                        <td class="whitespace-nowrap">Angelina</td>
                                        <td class="whitespace-nowrap">Jolie</td>
                                        <td class="whitespace-nowrap">@angelinajolie</td>
                                        <td class="whitespace-nowrap">angelinajolie@gmail.com</td>
                                        <td class="whitespace-nowrap">
                                            260 W. Storm Street New York, NY 10025.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap">2</td>
                                        <td class="whitespace-nowrap">Brad</td>
                                        <td class="whitespace-nowrap">Pitt</td>
                                        <td class="whitespace-nowrap">@bradpitt</td>
                                        <td class="whitespace-nowrap">bradpitt@gmail.com</td>
                                        <td class="whitespace-nowrap">
                                            47 Division St. Buffalo, NY 14241.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="whitespace-nowrap">3</td>
                                        <td class="whitespace-nowrap">Charlie</td>
                                        <td class="whitespace-nowrap">Hunnam</td>
                                        <td class="whitespace-nowrap">@charliehunnam</td>
                                        <td class="whitespace-nowrap">charliehunnam@gmail.com</td>
                                        <td class="whitespace-nowrap">
                                            8023 Amerige Street Harriman, NY 10926.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-responsive-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-responsive-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                        <th class="whitespace-nowrap">Email</th>
                                                        <th class="whitespace-nowrap">Address</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="whitespace-nowrap">1</td>
                                                        <td class="whitespace-nowrap">Angelina</td>
                                                        <td class="whitespace-nowrap">Jolie</td>
                                                        <td class="whitespace-nowrap">@angelinajolie</td>
                                                        <td class="whitespace-nowrap">angelinajolie@gmail.com</td>
                                                        <td class="whitespace-nowrap">
                                                            260 W. Storm Street New York, NY 10025.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="whitespace-nowrap">2</td>
                                                        <td class="whitespace-nowrap">Brad</td>
                                                        <td class="whitespace-nowrap">Pitt</td>
                                                        <td class="whitespace-nowrap">@bradpitt</td>
                                                        <td class="whitespace-nowrap">bradpitt@gmail.com</td>
                                                        <td class="whitespace-nowrap">
                                                            47 Division St. Buffalo, NY 14241.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="whitespace-nowrap">3</td>
                                                        <td class="whitespace-nowrap">Charlie</td>
                                                        <td class="whitespace-nowrap">Hunnam</td>
                                                        <td class="whitespace-nowrap">@charliehunnam</td>
                                                        <td class="whitespace-nowrap">charliehunnam@gmail.com</td>
                                                        <td class="whitespace-nowrap">
                                                            8023 Amerige Street Harriman, NY 10926.
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Responsive Table -->
            <!-- BEGIN: Small Table -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Small Table</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-7">Show example code</label>
                        <input id="show-example-7" data-target="#small-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="small-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-small-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-small-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Small Table -->
            <!-- BEGIN: Striped Rows -->
            <div class="intro-y box mt-5">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                    <h2 class="font-medium text-base mr-auto">Striped Rows</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <label class="form-check-label ml-0" for="show-example-8">Show example code</label>
                        <input id="show-example-8" data-target="#striped-rows-table" class="show-code form-check-input mr-0 ml-3" type="checkbox">
                    </div>
                </div>
                <div class="p-5" id="striped-rows-table">
                    <div class="preview">
                        <div class="overflow-x-auto">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">First Name</th>
                                        <th class="whitespace-nowrap">Last Name</th>
                                        <th class="whitespace-nowrap">Username</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Angelina</td>
                                        <td>Jolie</td>
                                        <td>@angelinajolie</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Brad</td>
                                        <td>Pitt</td>
                                        <td>@bradpitt</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Charlie</td>
                                        <td>Hunnam</td>
                                        <td>@charliehunnam</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="source-code hidden">
                        <button data-target="#copy-striped-rows-table" class="copy-code btn py-1 px-2 btn-outline-secondary">
                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Copy example code
                        </button>
                        <div class="overflow-y-auto mt-3 rounded-md">
                            <pre class="source-preview" id="copy-striped-rows-table">
                                <code class="html">
                                    {{ str_replace('>', 'HTMLCloseTag', str_replace('<', 'HTMLOpenTag', '
                                        <div class="overflow-x-auto">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="whitespace-nowrap">#</th>
                                                        <th class="whitespace-nowrap">First Name</th>
                                                        <th class="whitespace-nowrap">Last Name</th>
                                                        <th class="whitespace-nowrap">Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Angelina</td>
                                                        <td>Jolie</td>
                                                        <td>@angelinajolie</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Brad</td>
                                                        <td>Pitt</td>
                                                        <td>@bradpitt</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Charlie</td>
                                                        <td>Hunnam</td>
                                                        <td>@charliehunnam</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    ')) }}
                                </code>
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Striped Rows -->
        </div>
    </div>
@endsection

