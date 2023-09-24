<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Clock;

use Auth;

class AdminController extends Controller
{
    public function index() {
    	return view('admin.index');
    }

    public function ajaxRequestPost(Request $request) {

       $input = request()->all();

       $req = 0;
       $success = array();
       $errors = array();

       $editor = Auth::user()->id;
       if (isset($input['request'])) {
       		$req = $input['request'];
       }
       if ($req) {
       		switch($req) {
       			case 'getEvents' :	
       				if (isset($input['events'])) {
       					$events = Clock::whereIn('id',$input['events'])->get();
       					$success['events'] = $events;
       				} else {
       					$errors[] = "No events supplied";
       				}
       				
					break;
				case 'deleteEvents' :
       				if (isset($input['events'])) {
       					$tmp = array();
       					foreach($input['events'] as $e) {
       						$tmp[] = $e['id'];
       					}
       					$events = Clock::whereIn('id',$tmp)->get();
       					//dd($events);
       					
       					$deleted = array();
       					foreach($events as $event) {

       						$deleted[] = $event->id;
       						$event->delete();
       					}
       					$success[] = "Deleted events ".json_encode($deleted);
       	
       				} else {
       					$errors[] = "No events supplied";
       				}
       				break;
				case 'editEvents' :
       				if (isset($input['events'])) {
       					$tmp = array();
       					$edits = array('time');
       					foreach($input['events'] as $e) {
       						$tmp[] = $e['id'];
       						$time[$e['id']] = $e['time'];
       					}
       					$events = Clock::whereIn('id',$tmp)->get();
       					//dd($events);
       					
       					$edited = array();
       					foreach($events as $event) {
       						if (isset($time[$event->id])) {
       							$event->time = \Carbon\Carbon::parse($time[$event->id])->toDateTimeString();
                    $event->editor = $editor;
       							$event->save();
       							$edited[] = $event->id;
       						}
       						//$event->delete();
       					}
       					$success[] = "Edited events ".json_encode($edited);
       	
       				} else {
       					$errors[] = "No events supplied";
       				}
       				break;
				case 'createEvents' :
       				if (isset($input['events'])) {
       					$created = array();
       					foreach($input['events'] as $e) {
       						if ($e['staff_id'] && $e['action'] && $e['time']) {
       							$clock = new Clock;
                    $clock->editor = $editor;
       							$clock->staff_id = $e['staff_id'];
       							$clock->action = $e['action'];
       							$clock->time = \Carbon\Carbon::parse($e['time'])->toDateTimeString();
       							$clock->save();
       							$created[] = $clock->id;
       						}
       					}
       				
       					
       					$success[] = "Created events ".json_encode($created);
       	
       				} else {
       					$errors[] = "No events supplied";
       				}
       				break;
       			default 		:   
       				$errors[] = "Unknown request ".$req;
       				break;
       		}       		
       } else {
       		$errors[] = "No request supplied";
       }
        $response = array('data'=>json_encode($input));
        if (sizeof($success))
        	$response['success'] = $success;
        if (sizeof($errors))
        	$response['errors'] = $errors;
        return response()->json($response);
    }
}
