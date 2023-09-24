<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clock extends Model
{
	public static function getDefaultDates() {
		return array(
			"now" => \Carbon\Carbon::now()->addHour(1)->toDateTimeString(),
			"this week" => \Carbon\Carbon::now()->startOfWeek()->toDateTimeString(),
			"last week" => \Carbon\Carbon::parse("monday last week")->toDateTimeString(),
			"this month" =>  \Carbon\Carbon::parse("first day of ".\Carbon\Carbon::now()->format('F'))->toDateTimeString(),
			"last month" =>  \Carbon\Carbon::parse("first day of last month")->toDateTimeString()
		);
	}
}
