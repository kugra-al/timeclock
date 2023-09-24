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
@if ($clock)
<form method="post" action="{{action('ClockController@update',$id)}}">
	@csrf
	<input name="_method" type="hidden" value="PATCH">
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<label for="Time">Time</label>
			<input type="text" class="form-control" name="time" id="datetimepicker" value="{{ \Carbon\Carbon::parse($clock->time)->format('Y-m-d H:i') }}">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
		    <label for="action">Action</label>
		    <select class="form-control {{ $clock->action }}" id="action" name="action">
		    	<option>Select Action</option>
		      @foreach(['clockin'=>'Clock In','clockout'=>'Clock Out','breakstart'=>'Start Break','breakend'=>'End Break'] as $opt=>$val)
		      	<option class="{{ $opt }}" value="{{ $opt }}" @if($opt == $clock->action) selected="selected" @endif >{{ $val }}</option>
		      @endforeach
		    </select>
	  	</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
		    <label for="Staff">Staff Member</label>
		    <select class="form-control" id="staff_id" name="staff_id">
		    	<option>Select Staff</option>
		    @foreach($staff as $id=>$name)
		    	<option value="{{ $id }}" @if($id == $clock->staff_id) selected="selected" @endif >{{ $name }}</option>
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
@else
	Nothing to edit, go back
@endif
@endsection

@section('javascript-ready')
$('#datetimepicker').datetimepicker({
	'dateFormat':'yy-mm-dd',
	'setDate':'{{ \Carbon\Carbon::parse()->format('Y-m-d H:i') }}' 
});
@endsection
