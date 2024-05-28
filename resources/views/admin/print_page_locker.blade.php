<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<style type="text/css">
		@page { margin: 0; }
		body { margin: 0; }
		.main{
			width: 300px;
		}
		h4{
			
			font-size: 14px;
		}
		h4,h5,p{
			text-align: center;
			margin: 0;
		}
		.m-space{
			margin: 4px 0;
		}
		.table-div{
			display: table;
			width: 100%;
		}
		.table-div > div{
			display: table-cell;
			vertical-align: middle;
			padding: 2px;
		}
		.w-50{
			width: 50%;
		}
		.w-16{
			width: 16.66%;
		}
		td,span,p{
			font-size: 12px;
		}
		.text-right{
			text-align: right;
		}
		.name{
			text-align: left;
		}
	</style>
</head>
<body>
	<div class="main" id="printableArea">
		<h4>
			M/s New Nabaratna Hospitality Pvt. Ltd.
		</h4>
		<p class="m-space">
			Cloakroom, KyqInd Guwahati Railway Station, PF No. 1
		</p>
		<h5>
			GSTIN: 18AAICN4763E1ZA
		</h5>
		<h5>
			LOCKER
		</h5>
		<div class="table-div">
			<div class="w-50">
				<span class="text">Bill No: <b>{{ $print_data->unique_id }}</b></span>
			</div>
			<div class="w-50">
				<span class="text">Date: <b><?php // echo date("d-m-Y"); ?></b></span>
			</div>
		</div>
		<div class="table-div">
			<div class="w-50">
				<span class="text">Name: <b>{{ $print_data->name }}</b></span>
			</div>
			<div class="w-50">
				<span class="text">Mobile:<b>{{$print_data->mobile_no}}</b></span>
			</div>

		</div>
		<div class="table-div">
			<!-- <div class="w-50">
				<span class="text">Locker No: <b>{{ $print_data->locker_ids }}</b></span>
			</div> -->

			<div class="w-50">
				<span class="text">NOS: <b>{{ $print_data->nos }}</b></span>
			</div>
		</div>
		<div class="table-div">
			<div class="w-50">
				<span class="text">PNR/ID No.: <b>{{$print_data->pnr_uid}}</b></span>
			</div>
			<div class="w-50">
				<span class="text">Paid Amount: <b>{{ $print_data->paid_amount }}</b></span>
			</div>
		</div>
		<div style="margin-bottom:10px;">
			<div>
				<span class="text">In Time: <b>{{date("h:i a, d M y",strtotime($print_data->checkin_date))}}</b></span>
			</div>
			<div>
				<span class="text">Out Time: <b>{{date("h:i a, d M y",strtotime($print_data->checkout_date))}}</b></span>
			</div>
		</div>
		<table style="width:100%;margin: -1;" border="1" cellpadding="4" cellspacing="0" >
			<tbody>
				<tr>
					<td class="w-46">Description</td>
					<td class="w-20">Fee Type</td>
					<td class="w-16">No of luggage</td>
					<td class="w-16">Amount</td>
				</tr>
				<tr>
					<td class="w-46">For first 24 hours or part there of</td>
					<td class="w-20">70/- Per Package</td>
					<td class="w-16" rowspan="2">{{$print_data->no_of_bag}}</td>
					<td class="w-16">{{$print_data->for_first_day}}</td>
				</tr>
				<tr>
					<td class="w-46">For each subsequent 24 hours or part thee of</td>
					<td class="w-20">105/- Per Package</td>
					<td class="w-16">{{$print_data->for_other_day}}</td>
				</tr>
	
				<tr>
					<td colspan="1">Day: <b>{{$print_data->no_of_day}}</b></td>

					<td colspan="2"><b>Total</b></td>
					<td class="w-20">{{$print_data->for_first_day+$print_data->for_other_day}}</td>
					
				</tr>
			</tbody>
		</table>
		<div style="margin-top:10px;text-align: right;">
			<span style="text-align:right;font-weight: bold;">Note: We are not responsible for keeping eatable items inside your bag. Rats can be destroy your food and bags</span>
		</div>
		<div style="margin-top:10px;text-align:center;">

			
			<p style="margin-top:10px;font-size: 16px;">
				<strong>Thanks Visit Again</strong>
			</p>
		</div>
	</div>
	<script type="text/javascript">
		window.onload = function(e){ 
		    var printContents = document.getElementById("printableArea").innerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents; 
		}
	</script>
</body>
</html>


