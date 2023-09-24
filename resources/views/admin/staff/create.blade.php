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
<form method="post" action="{{url('/admin/staff')}}">
	@csrf
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<label for="Name">Name</label>
			<input type="text" class="form-control" name="name">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<label for="Pin">Pin</label>
			<input type="text" class="form-control" name="pin">
		</div>
	</div>
	<div class="row">
		<div class="col-md-4"></div>
		<div class="form-group col-cm-4">
			<button type="submit" class="btn btn-success">Save User</button>
		</div>
	</div>
@endsection