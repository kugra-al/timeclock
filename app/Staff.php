<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Clock;

class Staff extends Model
{
    public function isClockedIn () {
    	$clockIn = Clock::where('staff_id',$this->id)->where('action','clockin')->orderByDesc('time')->first();
    	$clockOut = Clock::where('staff_id',$this->id)->where('action','clockout')->orderByDesc('time')->first();
//dd(array($clockIn,$clockOut));
    	if (!$clockIn)
    		return false;
    	if ($clockIn && !$clockOut)
    		return $clockIn->time;
    	if (\Carbon\Carbon::parse($clockIn->time)->timestamp > \Carbon\Carbon::parse($clockOut->time)->timestamp)
    		return $clockIn->time;

    	//return array(\Carbon\Carbon::parse($clockIn->time)->timestamp,\Carbon\Carbon::parse($clockOut->time)->timestamp);
    	return false;
    }

    public function isClockedOut () {
    	$clockIn = Clock::where('staff_id',$this->id)->where('action','clockin')->orderByDesc('time')->first();
    	$clockOut = Clock::where('staff_id',$this->id)->where('action','clockout')->orderByDesc('time')->first();

    	if (!$clockOut)
    		return false;
    	if (!$clockIn)
    		return true;
    	if ($clockIn->time < $clockOut->time)
    		return $clockOut->time;
    	return false;
    }

    public function isOnBreak () {
    	$breakStart = Clock::where('staff_id',$this->id)->where('action','breakstart')->orderByDesc('time')->first();
    	$breakEnd = Clock::where('staff_id',$this->id)->where('action','breakend')->orderByDesc('time')->first();
    	if (!$breakStart)
    		return false;
    	if (!$breakEnd)
    		return true;
    	if ($breakStart->time > $breakEnd->time)
    		return $breakStart->time;
    	return false;
    }

    // logic checked in FrontController. Not here because admins need to change values
    public function startBreak($time = null) {
    //	if (!$this->isOnBreak()) {
    		$clock = new Clock;
    		if ($time)
    			$clock->time = $time;
    		else
    			$clock->time = \Carbon\Carbon::now();
    		$clock->action = "breakstart";
    		$clock->staff_id = $this->id;
    		$clock->save();
    		return $clock;
    //	}
    //	return false;    		
	}

    public function endBreak($time = null) {
    //	if ($this->isOnBreak()) {
    		$clock = new Clock;
    		if ($time)
    			$clock->time = $time;
    		else
    			$clock->time = \Carbon\Carbon::now();
    		$clock->action = "breakend";
    		$clock->staff_id = $this->id;
    		$clock->save();
    		return $clock;
    //	}
    //	return false;    		
    }

    public function clockOut($time = null) {
    //	if (!$this->isClockedOut()) {
    		$clock = new Clock;
    		if ($time)
    			$clock->time = $time;
    		else
    			$clock->time = \Carbon\Carbon::now();
    		$clock->action = "clockout";
    		$clock->staff_id = $this->id;
    		$clock->save();
    		return $clock;
    //	}
    //	return false;
    }

    public function clockIn($time = null) {
    //	if (!$this->isClockedIn()) {
    		$clock = new Clock;
    		if ($time)
    			$clock->time = $time;
    		else
    			$clock->time = \Carbon\Carbon::now();
    		$clock->action = "clockin";
    		$clock->staff_id = $this->id;
    		$clock->save();
    		return $clock;
    //	}
    //	return false;
    }




    public function getLastEvent($amt = 0) {
		$events = Clock::where('staff_id',$this->id)->orderByDesc('time');
		if (!$amt || $amt == 1)
			return $events->first();
		return $events->take($amt)->get();
    }

    public function getStatus($hideHeader = false) {
    	$timeNow = \Carbon\Carbon::now();
    	$breakStart = Clock::where('staff_id',$this->id)->where('action','breakstart')->orderByDesc('time')->first();
    	$breakEnd = Clock::where('staff_id',$this->id)->where('action','breakend')->orderByDesc('time')->first();
    	$clockIn = Clock::where('staff_id',$this->id)->where('action','clockin')->orderByDesc('time')->first();
    	$clockOut = Clock::where('staff_id',$this->id)->where('action','clockout')->orderByDesc('time')->first();
    	$clockedIn = $this->isClockedIn();
    	$onBreak = $this->isOnBreak();

    	$status = array(
    		"ID"=>$this->id,
    		"Name"=>$this->name,
    	);
    	if ($clockedIn) {
    		$status['Clocked In Since'] = "<span class='clockin'>".$clockIn->time." (".$timeNow->diffInMinutes($clockIn->time)." minutes)</span>";
    	} else {
    		if ($clockOut) {
    			$status['Clocked Out Since'] = "<span class='clockout'>".$clockOut->time." (".$timeNow->diffInMinutes($clockOut->time)." minutes)</span>";
    		} else {
    			$status['Clocked Status'] = "<span class='clockout'>Never Clocked In or Out</span>";
    		}
    	}
    	if ($onBreak && $clockedIn) {
    		$status['On Break Since'] = "<span class='breakstart'>".$breakStart->time." (".$timeNow->diffInMinutes($breakStart->time)." minutes)</span>";
    	} else {
    		if ($clockedIn && $breakEnd)
    			$status['Last Break'] = "<span class='breakend'>".$breakEnd->time." (".$timeNow->diffInMinutes($breakEnd->time)." minutes)</span>";
    	}

    	$tmp = "<ul>";
    	if (!$hideHeader)
    		$tmp .= "<li>Visit http://".$_SERVER['SERVER_ADDR']." to see more</li>";
    	foreach($status as $key=>$stat) {
    		$tmp .= "<li><strong>$key</strong> $stat</li>";
    	}
    	
    	return $tmp."</ul>";
    }

    public static function convertMinsToTime($mins) {
       return floor($mins/60)." hr ".($mins%60)." min";
    }

    public function getEventsSince($timeFrom = null,$timeUntil = null) {
    	$events =  Clock::where('staff_id',$this->id);
    	if ($timeFrom) {
    		$events = $events->where('time','>',$timeFrom);
    	}
    	if ($timeUntil) {
    		$events = $events->where('time','<',$timeUntil);
    	}
    	$events = $events->orderBy('time')->get();
    	$tmp = array();
    	$break = array('start'=>null,'end'=>null);
    //	$clock = array('start'=>null,'end'=>null);
    	$today = \Carbon\Carbon::now();
    
    	foreach($events as $id => $event) {
    		
    		$time = \Carbon\Carbon::parse($event->time);
    		if (!isset($tmp[$time->year]))
    			$tmp[$time->year] = array();
    		if (!isset($tmp[$time->year][$time->dayOfYear]))
	    		$tmp[$time->year][$time->dayOfYear] = array('breaks'=>array(),'clocks'=>array());
    		
	    	if ($event->action == "breakstart" || $event->action == "breakend") {
	    		if ($event->action == "breakstart")
		    		$break['start'] = $event;
		    	if ($event->action == "breakend" || $id = 0) {
		    		if ($id !== 0)
		    			$break['end'] = $event;
		    		if ($break['end'] && $break['start'])
		    			$tmp[$time->year][$time->dayOfYear]['breaks'][] = $break;
		    		$break = array('start'=>null,'end'=>null);
		    	}
	    	}
	    	else {
	    		if ($event->action == "clockin") {
		    			$tmp[$time->year][$time->dayOfYear]['clocks'][] = $event;
	    		}
	    		//dd($id);
		    	if ($event->action == "clockout") {

		    			$tmp[$time->year][$time->dayOfYear]['clocks'][] = $event;

		    	}

	    	}


	    		$tmp[$time->year][$time->dayOfYear]['day'] = $time;

    	}
//dd($tmp);
    	return $tmp;
    }

    public function getAdminStatus($timeFrom = null,$timeUntil = null) {
    	$events = $this->getEventsSince($timeFrom,$timeUntil);
    	$daysCount = 0;
    	$total = 0;
    	$breakMinutes = 0;
    	//$breaks = "";
    	$tmp = array();

    	foreach($events as $year=>$days) {
    		//dd($day);
    		$days = array_reverse($days,true);
    		//dd($days);
//dd($days[187]);
    		foreach($days as $id=>$day) {
         //       dd($day['clocks']);
    			$total += sizeof($day['breaks']);
    			$day['breaks'] = array_reverse($day['breaks']);
    			foreach($day['breaks'] as $break) {
    				
    				if ($break['start']) {
    					$end = 0;
    					if ($break['end'])
    						$end = \Carbon\Carbon::parse($break['end']->time);
    					else
    						$end = \Carbon\Carbon::now();
    					$breakMinutes += \Carbon\Carbon::parse($break['start']->time)->diffInMinutes($end);
    				}
    			}
    			$daysCount++;

    			$days[$id] = $day;
    		}
    		$events[$year] = $days;
    	}
    	//dd($events);
        krsort($events);
  
  	if (!$daysCount) {
    		return array('events'=>'','summary'=>$this->getStatus(true));
    	}
	if (!$total)
		$total = 1;
    	$avg = round($total/$daysCount);
    	$breakMinutesAvg = round($breakMinutes/$total);


      	return array('events'=>$events,'summary'=>"$total breaks over $daysCount days - Average breaks per day = $avg<br>Total minutes on breaks = $breakMinutes, Avg minutes per break = $breakMinutesAvg <br>".$this->getStatus(true));
   	}

   	public function addTestFields() {
   		$since = \Carbon\Carbon::parse('2018-05-01 00:00:00');
   		$until = \Carbon\Carbon::now();
//   		dd($until);
   	//	dd(\Carbon\Carbon::parse($until));
   		$days = $until->diffInDays($since);
   		$tmp = array();
   		for ($x = 0; $x < $days; $x++) {
   			$newDay = \Carbon\Carbon::parse($since)->day($since->day+$x);
   			if ($newDay->dayOfWeek != 6 && $newDay->dayOfWeek != 7 && $newDay->dayOfWeek != 0) {
	   			$signIn =  \Carbon\Carbon::parse($newDay)->hour(9);
	   			$signOut = \Carbon\Carbon::parse($newDay)->hour(17);
	   			$breaks = array();
	   			$this->clockIn($signIn);
	   			$breakOffset = 10;
	   			for ($y = 0; $y < 4+(rand(0,4)); $y++) {
	   				$breakStart = \Carbon\Carbon::parse($newDay)->hour(rand($breakOffset,$breakOffset+1))->addMinutes(rand(0,59));
	   				$breakOffset += 2;
	   				if ($breakStart->hour > 16)
	   					continue;
	   				$breakEnd = \Carbon\Carbon::parse($breakStart)->addMinutes(rand(1,15));
	   				$this->startBreak($breakStart);
	   				$this->endBreak($breakEnd);
	   				$breaks[] = array('start'=>$breakStart,'end'=>$breakEnd);
	   			}
	   			$this->clockOut($signOut);
	   			$tmp[] = array('new'=>$newDay,'signIn'=>$signIn,'signOut'=>$signOut,'breaks'=>$breaks);
	   		}
   		}
//   		dd($tmp);
   	}
}
