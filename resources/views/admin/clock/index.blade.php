@extends('layouts.admin')

@section('css')
#filters {background: #ccc;padding:10px;}
#filters select{width:200px;}
#filters input{width:100px;padding:5px;}
.right{text-align:right;float:right;}
.clear{clear:both;}
@endsection
@section('content')

@php($filterString = "")
@php($filterUrl = "")
@foreach($filters as $name=>$value)
	@if($value)
		@if(strlen($filterString))
			@php($filterString.= " AND ")
			@php($filterUrl .= "&")
		@endif
		@php($filterString.= $name." = ".$value)
		@php($filterUrl .= $name."=".$value)
	@endif
@endforeach

@php($filterUrl = "/admin/clock/?filters=true&".$filterUrl.(strlen($filterUrl) ? "&" : ""))
<div class="row right">
	<a style="margin:20px;" class="btn btn-success" href="{{url('/admin/clock/create')}}">Add Time Clock Event</a><br/>
	<a style="margin:20px" class="btn btn-info" href="{{ $filterUrl }}csv=true">Export</a>
</div>


@if (strlen($filterString))
	<h3>Searching events WHERE {{ $filterString }}</h3>
@endif
{{ $clock->links() }}
<div class="row clear" id="filters">
	<div class="col-sm-1">
		<h4>Filters</h4>
	</div>
	<div class="col-sm-3">
	    <select class="form-control" id="staff_id" name="staff_id">
	    <option>Staff Member</option>

	    @foreach($staff as $id=>$name)
	    	<option value="{{ $id }}" @if($filters['staff_id'] && $filters['staff_id'] == $id) selected="selected" @endif>{{ $name }}</option>
	    @endforeach
	    </select>
	</div>
	<div class="col-sm-3">
	    <select class="form-control" id="action" name="action">
	      	  <option>Select Action</option>
		      @foreach(['clockin'=>'Clock In','clockout'=>'Clock Out','breakstart'=>'Start Break','breakend'=>'End Break'] as $opt=>$val)
		      	<option class="{{ $opt }}" value="{{ $opt }}" @if($filters['action'] && $filters['action'] == $opt) selected="selected" @endif >{{ $val }}</option>
		      @endforeach
	    </select>
	</div>
	<div class="col-sm-4">
	    <input type="text" name="fromDate" class="datetimepicker" placeholder="From Date"
	    @if($filters['fromDate']) value="{{$filters['fromDate'] }}" @endif >
	    <input type="text" name="toDate" class="datetimepicker" placeholder="To Date"
	    @if($filters['toDate']) value="{{$filters['toDate'] }}" @endif > 
	    <strong>OR</strong>
	    <input type="text" name="period" placeholder="Period" @if($filters['period']) value="{{$filters['period'] }}" @endif >
	</div>
	<div class="col-sm-1">
		<button class="btn btn-success" onClick="searchWithFilters()">Filter</button>
	</div>
</div>
<div class="row clear">
<table class="table table-dark dark-bg">
	<thead>
		<th>ID</th>
		<th>Date</th>
		<th>Action</th>
		<th>Staff Member</th>
		<th>Changed By</th>
		<th>Last Changed</th>
		<th>Manage</th>
		<th></th>
	</thead>
	<tbody>
		@foreach($clock as $event)
			<tr class="{{ $event->action }}">
				<td>{{ $event->id }}</td>
				<td>{{ $event->time }}</td>
				<td><a href="#" onClick="return addEventFilter('action','{{ $event->action }}');">{{ $event->action }}</a></td>
				<td>@if (isset($staff) && isset($staff[$event->staff_id])) <a href="#" onClick="return addEventFilter('staff_id',{{ $event->staff_id }});">{{ $staff[$event->staff_id] }}</a> @else {{ $event->staff_id }} @endif</td>
				<td>@if ($event->editor && isset($adminUsers) && isset($adminUsers[$event->staff_id]))
					{{ $adminUsers[$event->staff_id] }}
					@else
						-
					@endif
				</td>
				<td>{{ $event->updated_at }}</td>
				<td><a href="{{action('ClockController@edit', $event['id'])}}" class="btn btn-warning">Edit</a></td>
				<td><form action="{{action('ClockController@destroy',$event['id'])}}" method="post">
					@csrf <input name="_method" type="hidden" value="DELETE"><a href="#" onClick="return confirmDelete($(this))" class="btn btn-danger">Delete</a></form></td>
			</tr>
		@endforeach
	</tbody>
</table>
</div>
{{ $clock->links() }}
@endsection

@section('javascript')
	function confirmDelete(ob) {
		r = confirm("Are you sure you want to delete this event?");
		if (r == true) {
			$($(ob).parents('form')[0]).submit()
		}
		return false;
	}

function addEventFilter(filter,value) {
	var filters = $('#filters');
	var filter = $(filters).find("[name='"+filter+"']");
	if ($(filter).prop("tagName") === "SELECT") {
		$(filter).find('option[value='+value+']').attr('selected','selected');
		searchWithFilters();
	}
	return false;	
}

function searchWithFilters() {
	var filters = $('#filters');
	var staff = $(filters).find("[name='staff_id']").val();

	if ($($(filters).find("[name='staff_id']").children()[0]).text() === staff)
		staff = 0;
	var action = $(filters).find("[name='action']").val();
	if ($($(filters).find("[name='action']").children()[0]).text() === action)
		action = 0;
	var fromDate = $(filters).find("[name='fromDate']").val();
	var toDate = $(filters).find("[name='toDate']").val();
	var period = $(filters).find("[name='period']").val();


		var location = window.location;
		var path = location.origin+location.pathname+"?filters=true";

			path += "&staff_id="+staff;

			path += "&action="+action;

			path += "&fromDate="+fromDate;

			path += "&toDate="+toDate;

			path += "&period="+period;
		location.href = path;
		console.log(path);
	
}
@endsection

@section('javascript-ready')
$('.datetimepicker').datetimepicker({
	'dateFormat':'yy-mm-dd',
	'setDate':'{{ \Carbon\Carbon::parse()->format('Y-m-d H:i') }}' 
});

$.each($('.pagination a'),function(i,v){
	var page = $(v).attr('href').split("?")[1];
	$(v).attr('href',"{!! $filterUrl !!}"+page);
});

@endsection