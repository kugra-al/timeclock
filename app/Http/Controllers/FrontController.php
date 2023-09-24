<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Clock;
use App\Staff;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staff = Staff::get();
      // dd($staff->isClockedIn());
        return view('front.index',['staff'=>$staff]);
        //
    }

	/**

     * Create a new controller instance.

     *

     * @return void

     */

    public function ajaxRequestPost()

    {

        $input = request()->all();

        $errors = array();
        $success = array();
        $staff = false;
        $auth = false;

        if (isset($input['action']) && $input['action'] == "getSigninList") {
            $staff = Staff::get();
            $out = array();
            foreach($staff as $member) {
                if ($member->isClockedIn()) {
                    if ($member->isOnBreak())
                            $out[] = array('type'=>'breakstart','name'=>$member->name,'time'=>\Carbon\Carbon::parse($member->isOnBreak())->format('H:i'));
                        else
                            $out[] = array('type'=>'clockin','name'=>$member->name,'time'=>\Carbon\Carbon::parse($member->isClockedIn())->format('H:i'));
                }
            }
            $response = array('data'=>json_encode($input));
            $response['staff'] = $out;
            return response()->json($response);
        }
        if (isset($input['staff_id'])) {
        	$staff = Staff::find($input['staff_id']);
        	if (!$staff) {
        		$errors[] = "Staff with id ".$input['staff_id']." not found";
        	} else {
        		if (isset($input['pin']) && $input['pin'] === $staff->pin) {
        	//		$success[] = "Staff logged in";
        			$auth = true;
        		} else {
        			$errors[] = "Incorrect login for ".$staff->id;
        		}
        	}
        } else {
        	$errors[] = "No staff_id found";
        }

        if (isset($input['check'])) {
        	if ($auth && $staff) {
        		$success[] = array('isClockedIn'=>$staff->isClockedIn(),'isOnBreak'=>$staff->isOnBreak());
        	} else {
        		$errors[] = "No login";
        	}
        }

        if (!isset($input['check']) && $auth && $staff) {

        	if (isset($input['action'])) {
    			$actionSuccess = false;
        		switch ($input['action']) {
        			case 1 : // check in
        					$clockedIn = $staff->isClockedIn();
        					if ($clockedIn) {
        						$errors[] = "You are already clocked in since $clockedIn . You should clock out first";
        						break;
        					} else {
        						$clock = $staff->clockIn();
        					//	$success[] = $clock;
        						$success[] = "You have clocked in.";
        					}
        					$actionSuccess = "checkin";
        					break;
        			case 2 :
        					$clockedOut = $staff->isClockedOut();
        					if ($clockedOut) {
        						$errors[] = "You are already clocked out since $clockedOut . You should clock in first";
        					} else {
        						$breakStatus = $staff->isOnBreak();
        						if ($breakStatus) {
        							$staff->endBreak();
        							$success[] = "You have ended your break";
        						}
        						$staff->clockOut();
        						$success[] = "You have clocked out";
        					}
        					$actionSuccess = "checkout";
        					break;
        			case 3 :$breakStatus = $staff->isOnBreak();
        					if ($breakStatus) {
        						$errors[] = "You are already on a break since $breakStatus . You should end your break first";
        					} 
        					$clockedOut = $staff->isClockedOut();
        					if ($clockedOut) {
        						$errors[] = "You are clocked out since $clockedOut . You should clock back in first";
        					}
        					if (!$breakStatus && !$clockedOut) {
        						$staff->startBreak();
        						$success[] = "You have started your break";
        					}
        					$actionSuccess = "breakstart";
        					break;
        			case 4 :$breakStatus = $staff->isOnBreak();
        					if (!$breakStatus) {
        						$errors[] = "You are are not marked as being on a break. You should start one first";
        					} 
        					$clockedOut = $staff->isClockedOut();
        					if ($clockedOut) {
        						$errors[] = "You are clocked out since $clockedOut . You should clock in first";
        					}
        					if ($breakStatus && !$clockedOut) {
        						$staff->endBreak();
        						$success[] = "You have ended your break";
        					}
        					$actionSuccess = "breakend";
        					break;
        			case 5 : $actionSuccess = "checkstatus";
        					 $status = $staff->getStatus();
        					 if ($status)
        					 	$success[] = $status;
        					 else
        					 	$errors[] = "Unable to find your data";
        					 break;
        			case 6 : $actionSuccess = "cancelLast";
        					 $errors[] = "This is not setup yet";
        					break;
        			default : //
        					$errors[] = "Unknown action ".$input['action'];
        					break;
        		}
        	//	if ($actionSuccess)
        	//		$success[] = "attempting action ".$input['action']." ".$actionSuccess;
        	} else {
        		$errors[] = "No action supplied";
        	}
        }

        $response = array('data'=>json_encode($input));
        if (sizeof($success))
        	$response['success'] = $success;
        if (sizeof($errors))
        	$response['errors'] = $errors;
        return response()->json($response);

    }
}
