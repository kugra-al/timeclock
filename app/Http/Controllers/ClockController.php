<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;

use App\Clock;
use App\Staff;

use Auth;
use App\User;

class ClockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $filters = array(
            'staff_id' => 0,
            'action'   => 0,
            'fromDate' => 0,
            'toDate'   => 0,
            'period'   => 0,
        );
        $clock = Clock::orderByDesc('time');

        $csv = false;
        if ($request->get('filters')) {
            if ($request->get('staff_id')) {
                $filters['staff_id'] = $request->get('staff_id');
                $clock = $clock->where('staff_id',$filters['staff_id']);
            }
            if ($request->get('action')) {
                $filters['action'] = $request->get('action');
                $clock = $clock->where('action',$filters['action']);
            }
            if ($request->get('fromDate')) {
                $filters['fromDate'] = $request->get('fromDate');
                $clock = $clock->where('time','>',$filters['fromDate']);
            }
            if ($request->get('toDate')) {
                $filters['toDate'] = $request->get('toDate');
                $clock = $clock->where('time','<',$filters['toDate']);
            }
            if ($request->get('period'))
                $filters['period'] = $request->get('period');
            if ($request->get('csv'))
                $csv = true;
        }


        
        $staff = Staff::pluck('name','id');
        $adminUsers = User::pluck('name','id');
        if ($csv) {
            $csvData = array();
            foreach($clock->get() as $c) {
                $csvData[] = array($c->time,$c->action,$staff[$c->staff_id]);
            }
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=timeclock.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $columnNames = array('time','action','name');
            $callback = function() use ($columnNames, $csvData ) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columnNames);
                foreach ($csvData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);


           // return $csvData;
//            return $clock->get();
        } else {
            $clock = $clock->paginate();
        }
        return view('admin.clock.index',['clock'=>$clock,'staff'=>$staff,'adminUsers'=>$adminUsers,'filters'=>$filters]);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $staff = Staff::pluck('name','id');
        return view('admin.clock.create',['staff'=>$staff]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time' => 'required',
            'action' => 'required',
            'staff_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/clock/create')
                ->withErrors($validator)
                ->withInput();
        }

        $editor = Auth::user()->id;
        $clock = new clock;
        $clock->time = \Carbon\Carbon::parse($request->get('time'))->toDateTimeString();
        $clock->action  = $request->get('action');
        $clock->staff_id = $request->get('staff_id');
        $clock->editor = $editor;
        $clock->save();
        return redirect('/admin/clock')->with('success','Time event created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $clock = Clock::find($id);
        $staff = Staff::pluck('name','id');
        return view('admin.clock.edit',compact('clock','id','staff'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'time' => 'required',
            'action' => 'required',
            'staff_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/admin/clock/'.$id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }
        $editor = Auth::user()->id;

        $clock = clock::find($id);
        $clock->time = \Carbon\Carbon::parse($request->get('time'))->toDateTimeString();
        $clock->action  = $request->get('action');
        $clock->staff_id = $request->get('staff_id');
        $clock->editor = $editor;
        $clock->save();
        return redirect('/admin/clock')->with('success','Time event edited');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $clock = Clock::find($id);
        $clock->delete();
        return back()->with('success','Time Clock Event deleted');
    }

    public function csv(Request $request) {
        dd($request);
    }
}
