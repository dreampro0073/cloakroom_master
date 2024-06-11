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
			height: 600px;
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

		
		
		@for($i = 1; $i <= $print_data->no_of_bag; $i++)
		<div style="height: 200px;display: flex;align-items: center;justify-content: center;border-bottom: 2px dashed #000;">
			<div style="text-align: center;">
				<h5>
					Cloakroom - {{$print_data->id}}
				</h5>
				<h4>
				    <span class="text">Name: <b>{{ $print_data->name }}</b></span>
    			</h4>
    			<h4>
    			    <span class="text">Bill No: <b>{{ $print_data->unique_id }}</b></span>
    			</h4>
				<h4>
					<span class="text">Luggage No: <b>{{ $i }}</b></span>
				</h4>

				<div style="margin-bottom:10px;">
					<div>
						<span class="text">In Time: <b>{{date("h:i a, d M y",strtotime($print_data->checkin_date))}}</b></span>
					</div>
					<div>
						@if($print_data->is_late == 0) 
							<span class="text">Out Time: <b>{{date("h:i a, d M y",strtotime($print_data->checkout_date))}}</b></span>
						@else
							<span class="text">Out Time: <b>{{date("h:i a, d M y",strtotime($print_data->checkout_time))}}</b></span>
						@endif
					</div>
				</div>
			</div>
		</div>
		@endfor
		
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


