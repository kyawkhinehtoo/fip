<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pop;
use App\Models\PopDevice;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PopController extends Controller
{
    //
    public function index(Request $request)
    {
        $pops = DB::table('pops')
        ->when($request->keyword, function ($query, $keyword){
            $query->where('site_name','LIKE','%'.$keyword.'%');
            $query->orwhere('site_description','LIKE','%'.$keyword.'%');
        })
        ->paginate(10);
        $pop_devices = PopDevice::get();
        $pops->appends($request->all())->links();
        return Inertia::render('Setup/Pop',
         ['pops' => $pops,'pop_devices'=>$pop_devices]);
    }
    public function getIdByName(Request $request)
    {
        $sn = null;
        if($request->name){

            $sn= DB::table('sn_ports')
            ->join('dn_ports','sn_ports.dn_id','=','dn_ports.id')
            ->where('dn_ports.name','=',$request->name)
            ->select('sn_ports.id as id','sn_ports.name as name','sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
            ->get();
        }
        return response()
            ->json($sn,200);
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'site_name' => ['required'],
        ])->validate();

        
            $check_dup = Pop::where('site_name',$request->name)
                        ->exists();
            if($check_dup)
            {
                return redirect()->back()->withErrors('Pops Name Already Exist!');
               
            }else{
                $pop = new Pop();
                $pop->site_name = $request->site_name;
                $pop->site_description = $request->site_description;
                $pop->site_location = $request->site_location;
                $pop->save();
                    if($pop->id && $request->devices){
                    foreach ($request->devices as $devices) {
                            $pop_device = new PopDevice();
                            $pop_device->pop_id = $pop->id;
                            $pop_device->device_name = $devices['device_name'];
                            $pop_device->qty = $devices['qty'];
                            $pop_device->remark = $devices['remark'];
                            $pop_device->save();
                        }
                    }
                return redirect()->back()->with('message', 'POP Site Created Successfully.');
            }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'site_name' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $pop = Pop::find($request->input('id'));
            $pop->site_name = $request->site_name;
            $pop->site_description = $request->site_description;
            $pop->site_location = $request->site_location;
            $pop->update();
            if($pop->id && $request->devices){
                PopDevice::where('pop_id','=',$pop->id)->delete();
                foreach ($request->devices as $device) {
                        
                            $pop_device = new PopDevice();
                            $pop_device->pop_id = $pop->id;
                            $pop_device->device_name = $device['device_name'];
                            $pop_device->qty = (isset($device['qty']))?$device['qty']:1;
                            $pop_device->remark = (isset($device['remark']))?$device['remark']:null;
                            $pop_device->save();
                        
                       
                    }
                }
            return redirect()->back()
                    ->with('message', 'POP Site Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Pop::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'POP Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id){
        if ($request->has('id')) {
            Pop::where('id','=',$request->input('id'))->delete();
            return redirect()->back()->with('message', 'POP Deleted Successfully.');
        }
    }
}
