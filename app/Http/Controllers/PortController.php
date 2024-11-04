<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\Pop;
use Hamcrest\Arrays\IsArray;
use Hamcrest\Type\IsNumeric;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    public function index(Request $request)
    {

        // $dns = DB::table('dn_ports')
        //     ->when($request->general, function ($query, $general) {
        //         $query->where('name', 'LIKE', '%' . $general . '%');
        //         $query->orwhere('description', 'LIKE', '%' . $general . '%');
        //     })
        //     ->paginate(10);
        $pops = Pop::all();
        $dns_all = DnPorts::get();
        $overall = DB::table('dn_ports')
            ->leftjoin('sn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            // ->select(DB::raw('dn_ports.name,dn_ports.description, count(sn_ports.port) as ports'))
            ->select('dn_ports.id', 'dn_ports.name', 'dn_ports.description', 'dn_ports.location', 'dn_ports.pop', 'dn_ports.input_dbm', DB::raw('count(sn_ports.id) as ports'))
            ->when($request->general, function ($query, $general) {
                $query->where('dn_ports.name', 'LIKE', '%' . $general . '%');
                $query->orwhere('dn_ports.description', 'LIKE', '%' . $general . '%');
            })
            ->when($request->pop, function ($query, $pops) {
                $pop_id = array();
                foreach ($pops as $pop) {
                    array_push($pop_id, $pop['id']);
                }
                $query->where(function ($q) use ($pop_id) {
                    foreach ($pop_id as $id) {
                        $q->orWhereJsonContains('dn_ports.pop->id', $id);
                    }
                });
            })
            ->groupBy('dn_ports.id', 'dn_ports.name', 'dn_ports.description')
            ->orderBy('dn_ports.id')
            ->paginate(10);
        $overall->appends($request->all())->links();
        return Inertia::render(
            'Setup/Ports',
            ['overall' => $overall, 'dns_all' => $dns_all, 'pops' => $pops]
        );
    }
    public function getIdByName(Request $request)
    {
        $sn = null;
        if ($request->name) {

            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->where('dn_ports.name', '=', $request->name)
                ->select('sn_ports.id as id', 'sn_ports.name as name', 'sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function getDNByPOP($request)
    {
        $dn = null;

        if ($request && is_numeric($request)) {
            $dn = DnPorts::whereJsonContains('pop->id', (int)$request)
                ->get();
        }
        return response()
            ->json($dn, 200);
    }
    public function getSNByDN($request)
    {

        $sn = null;
        if ($request && is_numeric($request)) {

            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->where('dn_ports.id', '=', $request)
                ->select('sn_ports.id as id', 'sn_ports.name as name', 'sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function getDNBySN($request)
    {

        $sn = null;
        if ($request && is_numeric($request)) {

            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->where('dn_ports.id', '=', $request)
                ->select('sn_ports.id as id', 'sn_ports.name as name', 'sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();


        $check_dup = DnPorts::where('name', $request->name)
            ->exists();
        if ($check_dup) {
            return redirect()->back()->withErrors('DN Already Exist!');
        } else {
            $dnport = new DnPorts();
            $dnport->name = $request->name;
            $dnport->description = $request->description;
            $dnport->pop = $request->pop;
            $dnport->location = $request->location;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->save();
            return redirect()->back()->with('message', 'DN Port Created Successfully.');
        }

        // }else{
        // for($n=1; $n <=$request->port ; $n++ ){
        //     $dnport = new DnPorts();
        //     $dnport->name = $request->name;
        //     $dnport->port = $n;
        //     $dnport->description = $request->description;
        //     $dnport->save();
        // }

        //  return redirect()->back()->with('message', 'DN Created Successfully.');
        // }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $dnport = DnPorts::find($request->input('id'));
            $dnport->name = $request->name;
            $dnport->pop = $request->pop;
            $dnport->description = $request->description;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->update();
            return redirect()->back()
                ->with('message', 'Port Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            DnPorts::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id)
    {
        if ($request->has('id')) {
            DnPorts::where('name', '=', $request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
}