@extends('layouts.admin')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="post" action="{{url('/admin/clock')}}">
	@csrf
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<label for="Time">Time</label>
			<input type="text" class="form-control" name="time" id="datetimepicker" value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
		    <label for="action">Action</label>
		    <select class="form-control" id="action" name="action">
	    		<option>Select Action</option>
		      <option value="clockin" class="clockin">Clock In</option>
		      <option value="clockout" class="clockout">Clock Out</option>
		      <option value="breakstart" class="breakstart">Start Break</option>
		      <option value="breakend" class="breakend">End Break</option>
		    </select>
	  	</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
		    <label for="Staff">Staff Member</label>
		    <select class="form-control" id="staff_id" name="staff_id">
		    	<option>Select Staff Member</option>
		    @foreach($staff as $id=>$name)
		    	<option value="{{ $id }}">{{ $name }}</option>
		    @endforeach
		    </select>
	  	</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<button type="submit" class="btn btn-success">Add Time Event</button>
		</div>
	</div>
@endsection

@section('javascript-ready')
$('#datetimepicker').datetimepicker({
	'dateFormat':'yy-mm-dd',
	'setDate':'{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}' 
});
@endsection
