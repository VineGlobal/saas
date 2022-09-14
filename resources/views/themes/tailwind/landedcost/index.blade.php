@extends('theme::lclayouts.app')    
 

@section('head') 
     <style> 
      //Hide last td for modal popup
      #modaldata tbody tr > td:last-of-type{display:none;}
    </style>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <!-- <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet"> -->
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <!-- <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">  -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>   --> 

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script> 
<!-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->

 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
 <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>  -->
 
<link href="{{ asset('themes/' . $theme->folder . '/css/data-table-custom.css') }}" rel="stylesheet">
    

 
<script type="text/javascript"> 
 jQuery(document).ready(function($) { 
    var table = $('.yajra-datatable').DataTable({
         lengthMenu: [
            [10, 25],
            [10, 25],
        ],
        processing: true,
        serverSide: true,
        ajax: "lc-api/transactions",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'utcDateTimeStamp', name: 'utcDateTimeStamp'},
            {data: 'code', name: 'code'},
            {data: 'dutiesTotal', name: 'dutiesTotal'},
            {data: 'taxesTotal', name: 'taxesTotal'},
            {data: 'feesTotal', name: 'feesTotal'},
            {data: 'grandTotal', name: 'grandTotal'},
            {
                data: 'action', 
                name: 'action', 
                orderable: true, 
                searchable: true,
                paging: true,
                autoWidth: true
            },
        ]
    });  
  });
  
  
  function showModal(_id)  {
          var _data = new Array(); 
          jQuery.ajax({
            url: "lc-api/transactionLookup?_id="+_id,
            type: 'GET', 
            dataType:"json",
            success: function(data) {  
              const el = document.querySelector("#basic-modal");  
              document.getElementById('subTotal').placeholder       = data.transaction.subTotal;
              document.getElementById('dutiesTotal').placeholder    = data.transaction.dutiesTotal;
              document.getElementById('taxesTotal').placeholder     = data.transaction.taxesTotal;
              document.getElementById('feesTotal').placeholder      = data.transaction.feesTotal;
              document.getElementById('grandTotal').placeholder     = data.transaction.grandTotal;
              document.getElementById('transactionId').placeholder   = data.transaction.id;
              
              const table = document.getElementById("itemsTable");
              data.transaction.items.forEach( item => {
                  row = table.insertRow();
                  name_ = row.insertCell(0);
                  name_.innerHTML = item.name;
                  price = row.insertCell(1);
                  price.innerHTML = item.price;
                  qty = row.insertCell(2);
                  qty.innerHTML = item.quantity; 
                  hsCode = row.insertCell(3);
                  hsCode.innerHTML = item.hsCode;  
                  dutiesAmount = row.insertCell(4);
                  dutiesAmount.innerHTML = item.dutiesAmount;  
                  taxesAmount = row.insertCell(5);
                  taxesAmount.innerHTML = item.taxesAmount;            
            });
              
              const modal = tailwind.Modal.getOrCreateInstance(el); 
              modal.show();
           }
        });
     
      
  }
  
</script> 
 
    
 
 @endsection
   
@section('content') 
    
    <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Landed Cost Calcululations</h2> 
                    </div>
   
        <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="text-2xl font-medium leading-2" id="chartTitle">Loading...</h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        
                        <button class="btn btn-secondary w-28 inline-block mr-1 mb-2" onclick="currentMonth()" id="currentMonthButton">Current Month</button>
                        <button class="btn btn-secondary w-28 inline-block mr-1 mb-2" onclick="currentYear()" id="currentYearButton">Current Year</button> 
                    </div>
                </div> 
                       
                       <div class="max-w-4xl px-5 mx-auto mt-10 lg:px-0 container p-5">  
                        <table class="table stripe yajra-datatable" style="width:100%">
                            <thead>
                                <tr class="bg-primary text-white    ">
                                    <th>#</th>
                                    <th>Date/Time</th>
                                    <th>HTTP Code</th>
                                    <th>Duties Total</th>
                                    <th>Taxes Total</th>
                                    <th>Fees Total</th>
                                    <th>Grand Total</th>
                                    <th>View Details</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>  
                             
                        </div> 
                
        </div>
   </div>                   
                                         <!-- BEGIN: Modal Content -->
                                        <div id="basic-modal" class="modal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- BEGIN: Modal Header -->
                                                    <div class="modal-header">
                                                        <h2 class="font-medium text-base mr-auto">Details</h2>
                                                       <!-- <button class="btn btn-outline-secondary hidden sm:flex">
                                                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs
                                                        </button>
                                                        -->
                                                        <div class="dropdown sm:hidden">
                                                            <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown">
                                                                <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i>
                                                            </a>
                                                            <div class="dropdown-menu w-40">
                                                                <ul class="dropdown-content">
                                                                    <li>
                                                                        <a href="javascript:;" class="dropdown-item">
                                                                            <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- END: Modal Header -->
                                                    <!-- BEGIN: Modal Body -->
                                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                         <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="form-label">ID</label>
                                                            <input id="transactionId" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="form-label">Sub Total</label>
                                                            <input id="subTotal" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="form-label">Duties Total</label>
                                                            <input id="dutiesTotal" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-3" class="form-label">Taxes Total</label>
                                                            <input id="taxesTotal" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-4" class="form-label">Fees Total</label>
                                                            <input id="feesTotal" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-5" class="form-label">Grand Total</label>
                                                            <input id="grandTotal" type="text" class="form-control" placeholder="" disabled></input>
                                                        </div> 
                                                        
                                                        <div class="col-span-12 sm:col-span-10 overflow-x-auto relative">
                                                           <label for="modal-form-5" class="form-label">Items</label>
                                                            <table id="itemsTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                            <thead>
                                                                    <tr class="bg-primary text-white">
                                                                        <th>Name</th>
                                                                        <th>Price</th>
                                                                        <th>Qty.</th>
                                                                        <th>HS Code</th>
                                                                        <th>Duties Amount</th>
                                                                        <th>Taxes Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody> 
                                                                </tbody>
                                                            </table>
                                                            
                                                        </div> 
                                                        
                                                    </div>
                                                    <!-- END: Modal Body -->
                                                    <!-- BEGIN: Modal Footer -->
                                                    <div class="modal-footer">
                                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Close</button> 
                                                    </div>
                                                    <!-- END: Modal Footer -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END: Modal Content -->
                                    
                                
<!-- END: Modal Content --
                                     
                                    
                                

@endsection
