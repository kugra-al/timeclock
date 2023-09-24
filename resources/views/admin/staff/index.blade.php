@extends('layouts.admin')

@section('content')
<div class="row right">
	<a style="margin:20px;" class="btn btn-success" href="{{url('/admin/staff/create')}}">Add Staff Member</a>
</div>

{{ $staff->links() }}
<p style='padding:5px;border:1px solid #FF0000;display:block;float:left;font-weight:bold;background:#FFF000;'>Hours this week are now accurate up until the current time</p>
<table class="table table-dark">
	<thead>
		<th>ID</th>
		<th>Name</th>
		<th>Hours Last Week</th>
		<th>Hours This Week (so far)</th>
		<th>Last Clock Actions</th>
		<th>Manage</th>
	</thead>
	<tbody>
		@foreach($staff as $member)
			@php($lastEvents = $member->getLastEvent(4))
			@if(sizeof($lastEvents))
				@php($lastEvent = $lastEvents[0])
			@else
				@php($lastEvent = '');
			@endif
			<tr class="@if($lastEvent){{ $lastEvent->action }}@endif">
				<td><span style="font-size:22px;">{{ $member->id }}</span></td>
				<td><a style="font-size:22px;" href="/admin/staff/{{ $member->id }}">{{ $member->name }}</a></td>
				<td>@if(isset($hours[$member->id]['lastWeek']))
					<strong>Days</strong> {{ $hours[$member->id]['lastWeek']['days'] }}<br>
					<strong>Hours</strong> {{ $hours[$member->id]['lastWeek']['hours']}}<br>
					<strong>Breaks</strong> {{ $hours[$member->id]['lastWeek']['breaks']}}<br>
					<strong>Break Mins</strong> {{ $hours[$member->id]['lastWeek']['breakMinutes']}}
				</td>
				@endif</td>
				<td>@if(isset($hours[$member->id]['thisWeek']))
					<strong>Days</strong> {{ $hours[$member->id]['thisWeek']['days'] }}<br>
					<strong>Hours</strong> {{ $hours[$member->id]['thisWeek']['hours']}}<br>
					<strong>Breaks</strong> {{ $hours[$member->id]['thisWeek']['breaks']}}<br>
					<strong>Break Mins</strong> {{ $hours[$member->id]['thisWeek']['breakMinutes']}}
				</td>
				@endif</td>
				<td>@foreach($lastEvents as $event)<div class="{{ $event->action }}">{{ $event->time }} {{ $event->action }}</div>@endforeach </td>
				<td><a href="{{action('StaffController@edit', $member['id'])}}" class="btn btn-warning">Edit</a><br><a href="{{action('StaffController@show', $member['id'])}}" class="btn btn-info">View</a></td>
			</tr>
		@endforeach
	</tbody>
</table>
@endsection
