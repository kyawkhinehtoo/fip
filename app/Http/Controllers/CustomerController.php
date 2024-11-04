<?php

namespace App\Http\Controllers;

use App\Models\BundleEquiptment;
use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Township;
use App\Models\Role;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\CustomerHistory;
use App\Models\FileUpload;
use App\Models\Pop;
use App\Models\PublicIpAddress;
use App\Models\Subcom;
use Inertia\Inertia;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->show($request);
    }
    public function show(Request $request)
    {
        //   dd($request);
        $user = User::join('roles', 'roles.id', '=', 'users.role')->select('users.*', 'roles.name as role_name')->where('users.id', '=', Auth::user()->id)->first();
        $role = Role::join('users', 'roles.id', '=', 'users.role')->select('roles.*')->where('users.id', '=', Auth::user()->id)->first();
        $packages = Package::get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();
        $dn = DnPorts::get();
        $bundle_equiptments = BundleEquiptment::get();
        $active = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->whereIn('status.type', ['active', 'disabled'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->count();

        // $relocation = DB::table('customers')
        // ->join('status', 'customers.status_id', '=', 'status.id')
        // ->where('status.name', 'LIKE', '%Relocation%')
        // ->where('customers.deleted', '<>', '1')
        // ->count();
        $suspense = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'suspense')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->count();
        $installation_request = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'new')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->count();
        $terminate = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'terminate')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->count();


        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

        $all_township = Township::select('id')
            ->get()
            ->toArray();
        $all_packages = Package::select('id')
            ->get()
            ->toArray();
        $package_speed = $packages = Package::select('speed', 'type')
            ->groupBy('speed', 'type')
            ->orderBy('speed', 'ASC')->get();
        $customers =  DB::table('customers')
            ->leftjoin('packages', 'customers.package_id', '=', 'packages.id')
            ->leftjoin('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->leftjoin('sn_ports', 'customers.sn_id', '=', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
                });
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', [$installation['from'], $installation['to']]);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', [$order['from'], $order['to']]);
            })
            ->when($request->prefer, function ($query, $prefer) {

                $query->whereBetween('customers.prefer_install_date', [$prefer['from'], $prefer['to']]);
            })
            ->when($request->dn, function ($query, $dn_2) {
                $query->where('dn_ports.id', '=', $dn_2);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('sn_ports.id', '=', $sn);
            })
            ->when($request->package, function ($query, $package) use ($all_packages) {
                if ($package == 'empty') {
                    $query->whereNotIn('customers.package_id', $all_packages);
                } else {
                    $query->where('customers.package_id', '=', $package);
                }
            })
            ->when($request->package_speed, function ($query, $package_speed) {
                $speed_type =  explode("|", $package_speed);
                $speed = $speed_type[0];
                $type = $speed_type[1];
                $query->where('packages.speed', '=', $speed);
                $query->where('packages.type', '=', $type);
            })
            ->when($request->township, function ($query, $township) use ($all_township) {
                if ($township == 'empty') {
                    $query->whereNotIn('customers.township_id', $all_township);
                } else {
                    $query->where('customers.township_id', '=', $township);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort == 'cid') {
                    $sort_by = 'customers.id';
                } elseif ($sort == 'cname') {
                    $sort_by = 'customers.name';
                } elseif ($sort == 'township') {
                    $sort_by = 'townships.name';
                } elseif ($sort == 'package') {
                    $sort_by = 'packages.name';
                } elseif ($sort == 'order') {
                    $sort_by = 'customers.order_date';
                }

                $query->orderBy($sort_by, 'desc');
            }, function ($query) {
                $query->orderBy('customers.id', 'desc');
            })
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.prefer_install_date as prefer_install_date', 'customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color', 'customers.pppoe_account as pppoe_account')
            ->paginate(10);
        $radius = RadiusController::checkRadiusEnable();
        if ($radius) {
            foreach ($customers as $key => $value) {
                if ($value->pppoe_account)
                    $value->radius_status = RadiusController::checkCustomer($value->pppoe_account);
                else
                    $value->radius_status = 'no account';
            }
        }
        // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Customer', [
            'packages' => $packages,
            'package_speed' => $package_speed,
            'townships' => $townships,
            'projects' => $projects,
            'status' => $status,
            'customers' => $customers,
            'dn' => $dn,
            'active' => $active,
            'suspense' => $suspense,
            'installation_request' => $installation_request,
            'terminate' => $terminate,
            'radius' => $radius,
            'user' => $user,
            'role' => $role,
            'bundle_equiptments' => $bundle_equiptments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $packages = Package::get();
        $sn = SnPorts::get();
        $projects = Project::get();
        $pops = Pop::get();
        $bundle_equiptments = BundleEquiptment::get();
        $dn = DB::table('dn_ports')
            ->get();
        $sale_persons = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'LIKE', '%sale%')
            ->select('users.name as name', 'users.id as id')
            ->get();

        $auth_role = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.name', 'NOT LIKE', '%Admin%')
            ->where('users.id', '=', Auth::user()->id)
            ->select('users.name as name', 'users.id as id')
            ->get();

        if (!$auth_role->isEmpty()) {


            $sale_persons = $auth_role;
        }

        $subcoms = Subcom::all();
        $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
        $status_list = Status::get();
        $roles = Role::get();
        $user = User::join('roles', 'roles.id', '=', 'users.role')->select('users.*', 'roles.name as role_name')->where('users.id', '=', Auth::user()->id)->first();
        $role = Role::join('users', 'roles.id', '=', 'users.role')->select('roles.*')->where('users.id', '=', Auth::user()->id)->first();
        $max_id = $this->getmaxid();
        return Inertia::render(
            'Client/AddCustomer',
            [
                'packages' => $packages,
                'projects' => $projects,
                'sale_persons' => $sale_persons,
                'townships' => $townships,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'roles' => $roles,
                'user' => $user,
                'sn' => $sn,
                'dn' => $dn,
                'pops' => $pops,
                'max_id' => $max_id,
                'role' => $role,
                'bundle_equiptments' => $bundle_equiptments,
            ]
        );
    }

    public function store(Request $request)
    {
        $users = User::find(Auth::user()->id);
        $roles = Role::find($users->role);
        $user_perm = explode(',', $roles->permission);

        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            // 'latitude' => 'required|max:255',
            // 'longitude' => 'required|max:255',
            'sale_person' => 'required',
            'package' => 'required',
            'sale_channel' => 'required|max:255',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'status' => 'required',
            'order_date' => 'date',
            'township' => 'required',
            'installation_date' => 'nullable|date',

        ])->validate();


        $auto_ftth_id = $request->ftth_id;
        $check_id = Customer::where('ftth_id', '=', $auto_ftth_id)->first();
        if ($check_id) {
            //already exists
            if ($request->township && $request->package) {
                $max_c_id = $this->getmaxid();
                $city_id = $request->township['city_id'];
                $pacakge_type = $request->package['type'];
                $result = null;
                foreach ($max_c_id as $value) {
                    if ((int)$value['id'] == (int)$city_id) {
                        $result = $value['value'];
                    }
                }
                if ($result) {
                    //   $max_id = $max_c_id [$request->city_id];
                    $auto_ftth_id = $request->township['city_code'] . str_pad($result + 1, 5, '0', STR_PAD_LEFT) . '-' . strtoupper($pacakge_type);
                }
            }
        }
        $customer = new Customer();
        foreach ($user_perm as $key => $value) {
            if ($value != 'id')
                $customer->$value = $request->$value;

            if ($value == 'ftth_id')
                $customer->$value = $auto_ftth_id;
            if ($value == 'location')
                $customer->$value = $request->latitude . ',' . $request->longitude;
            if ($value == 'status_id')
                $customer->$value = $request->status['id'];
            if ($value == 'township_id')
                $customer->$value = $request->township['id'];
            if ($value == 'package_id')
                $customer->$value = $request->package['id'];
            if ($value == 'sale_person_id')
                $customer->$value = $request->sale_person['id'];
            if ($value == 'subcom_id') {
                if (!empty($request->subcom))
                    $customer->$value = $request->subcom['id'];
            }
            if ($value == 'project_id') {
                if (!empty($request->project))
                    $customer->$value = $request->project['id'];
            }
            if ($value == 'sn_id') {
                if (!empty($request->sn_id))
                    $customer->$value = $request->sn_id['id'];
            }
            if ($value == 'pop_id') {
                if (!empty($request->pop_id))
                    $customer->$value = $request->pop_id['id'];
            }
            if ($value == 'bundle') {
                if (!empty($request->bundles)) {
                    $customer->bundle = '';
                    foreach ($request->bundles as $key => $value) {
                        if ($key !== array_key_last($request->bundles))
                            $customer->bundle .= $value['id'] . ',';
                        else
                            $customer->bundle .= $value['id'];
                    }
                }
            }
        }
        $customer->deleted = 0;

        $customer->save();

        $new_history = new CustomerHistory();

        $new_history->customer_id = $customer->id;
        $new_history->actor_id = Auth::user()->id;
        $new_history->new_status = $request->status['id'];
        $new_history->active = 1;
        $new_history->type = 'new_installation';
        $new_history->date = date("Y-m-j h:m:s");
        if ($request->start_date)
            $new_history->start_date = $request->start_date;

        if ($request->end_date)
            $new_history->end_date = $request->end_date;

        if (isset($request->status['relocation'])) {
            //new
            if ($request->new_address)
                $new_history->new_address = $request->new_address;
            if ($request->new_latitude && $request->new_longitude)
                $new_history->new_location = $request->new_latitude . ',' . $request->new_longitude;
            //old
            $new_history->old_address = $request->address;
            $new_history->old_location = $request->latitude . ',' . $request->longitude;
        }
        if ($request->package) {
            $new_history->new_package = $request->package['id'];
        }
        $new_history->save();
        if (RadiusController::checkRadiusEnable()) {
            RadiusController::createRadius($customer->id);
        }
        $logData = [];
        $changes = $customer->getChanges();
        foreach ($changes as $key => $newValue) {
            $logData[$key] = [
                'to' => $newValue                   // New value
            ];
        }
        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($customer)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Customer Created: ' . $customer->ftth_id);
        return redirect()->route('customer.index')->with('message', 'Customer Created Successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id) {
            $customer =  DB::table('customers')
                ->where('customers.id', '=', $id)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orWhereNull('customers.deleted');
                })
                ->first();
            $customer_history = CustomerHistory::where('customer_id', '=', $id)
                ->where('active', '=', 1)
                ->first();
            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->select('sn_ports.*', 'dn_ports.name as dn_name', 'dn_ports.pop as pop')
                ->get();
            $dn = DB::table('dn_ports')
                ->get();
            $packages = Package::get();
            $projects = Project::get();
            $sale_persons = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('disabled', '<>', 1)
                ->select('users.name as name', 'users.id as id')
                ->get();
            $auth_role = DB::table('users')
                ->join('roles', 'users.role', '=', 'roles.id')
                ->where('roles.name', 'NOT LIKE', '%Admin%')
                ->where('users.id', '=', Auth::user()->id)
                ->select('users.name as name', 'users.id as id')
                ->get();
            if (!$auth_role->isEmpty() && $customer->sale_person_id) {

                $sale_persons = DB::table('users')
                    ->join('roles', 'users.role', '=', 'roles.id')
                    ->where('users.id', '=', $customer->sale_person_id)
                    ->select('users.name as name', 'users.id as id')
                    ->get();
            }
            $subcoms = Subcom::all();


            $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
            $status_list = Status::get();
            $roles = Role::get();
            $users = User::find(Auth::user()->id);
            $user = User::join('roles', 'roles.id', '=', 'users.role')->select('users.*', 'roles.name as role_name')->where('users.id', '=', Auth::user()->id)->first();
            $role = Role::join('users', 'roles.id', '=', 'users.role')->select('roles.*')->where('users.id', '=', Auth::user()->id)->first();
            $pops = Pop::all();
            $radius = RadiusController::checkRadiusEnable();
            $bundle_equiptments = BundleEquiptment::get();
            $total_ips = PublicIpAddress::where('customer_id', $customer->id)->count();
            $total_documents = FileUpload::where('customer_id', $customer->id)->whereNull('incident_id')->count();

            return Inertia::render(
                'Client/EditCustomer',
                [
                    'customer' => $customer,
                    'packages' => $packages,
                    'projects' => $projects,
                    'sale_persons' => $sale_persons,
                    'townships' => $townships,
                    'status_list' => $status_list,
                    'subcoms' => $subcoms,
                    'roles' => $roles,
                    'role' => $role,
                    'users' => $users,
                    'user' => $user,
                    'sn' => $sn,
                    'dn' => $dn,
                    'customer_history' => $customer_history,
                    'radius' => $radius,
                    'pops' => $pops,
                    'total_ips' => $total_ips,
                    'total_documents' => $total_documents,
                    'bundle_equiptments' => $bundle_equiptments,
                ]
            );
        }
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
        $users = User::find(Auth::user()->id);
        $roles = Role::find($users->role);
        $user_perm = explode(',', $roles->permission);


        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            // 'latitude' => 'required|max:255',
            // 'longitude' => 'required|max:255',
            'sale_person' => 'required',
            'package' => 'required',
            'sale_channel' => 'required|max:255',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'order_date' => 'date',
            'status' => 'required',
            'installation_date' => 'nullable|date',

        ])->validate();
        if ($request->has('id') && !$roles->read_customer) {
            $customer = Customer::find($request->input('id'));
            foreach ($user_perm as $key => $value) {
                if ($value != 'id')
                    $customer->$value = $request->$value;

                if ($value == 'location')
                    $customer->$value = $request->latitude . ',' . $request->longitude;
                if ($value == 'status_id') {
                    $customer->$value = $request->status['id'];
                    CustomerHistory::where('customer_id', '=', $request->input('id'))->update(['active' => 0]);




                    $new_history = new CustomerHistory();
                    $old_c = Customer::find($request->input('id'));
                    if ($request->start_date)
                        $new_history->start_date = $request->start_date;

                    if ($request->end_date)
                        $new_history->end_date = $request->end_date;

                    $new_history->customer_id = $customer->id;
                    $new_history->actor_id = Auth::user()->id;
                    //if($old_c->status_id !=  $request->status['id']){
                    $new_history->old_status =  $old_c->status_id;
                    $new_history->new_status = $request->status['id'];
                    $new_history->start_date = $request->start_date;

                    // }
                    if ($old_c->status_id !=  $request->status['id'])
                        $new_history->type = $request->status['name'];

                    $new_history->active = 1;
                    $new_history->date = date("Y-m-j h:m:s");

                    if (isset($request->status['relocation'])) {
                        $new_history->type = 'relocation';
                        //new
                        if ($request->new_address)
                            $new_history->new_address = $request->new_address;
                        if ($request->new_latitude && $request->new_longitude)
                            $new_history->new_location = $request->new_latitude . ',' . $request->new_longitude;
                        //old
                        $new_history->old_address = $request->address;
                        $new_history->old_location = $request->latitude . ',' . $request->longitude;
                    }
                    if ($request->package) {
                        if ($request->package['id'] != $old_c->package_id) {
                            $new_history->type = 'plan_change';
                            $new_history->new_package = $request->package['id'];
                            $new_history->old_package = $old_c->package_id;
                            $myDateTime = new DateTime;
                            if ($request->start_date) {
                                $myDateTime = new DateTime($request->start_date);
                            }
                            $newtime = clone $myDateTime;
                            // if($myDateTime->format('d') <= 7){
                            //     $newtime->modify('first day of this month');
                            //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                            // }else{
                            //     $newtime->modify('+1 month');
                            //     $newtime->modify('first day of this month');
                            //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                            // }
                            $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                        }
                    }
                    $new_history->save();
                }
                if ($value == 'township_id')
                    $customer->$value = $request->township['id'];
                if ($value == 'package_id')
                    $customer->$value = $request->package['id'];
                if ($value == 'sale_person_id')
                    $customer->$value = $request->sale_person['id'];
                if ($value == 'subcom_id') {
                    if (!empty($request->subcom))
                        $customer->$value = $request->subcom['id'];
                }
                if ($value == 'project_id') {
                    if (!empty($request->project))
                        $customer->$value = $request->project['id'];
                }
                if ($value == 'sn_id') {
                    if (!empty($request->sn_id))
                        $customer->$value = $request->sn_id['id'];
                }
                if ($value == 'pop_id') {
                    if (isset($request->pop_id['id']))
                        $customer->$value = $request->pop_id['id'];
                }
                if ($value == 'bundle') {
                    if (!empty($request->bundles)) {

                        $customer->bundle = '';
                        foreach ($request->bundles as $key => $value) {
                            if ($key !== array_key_last($request->bundles))
                                $customer->bundle .= $value['id'] . ',';
                            else
                                $customer->bundle .= $value['id'];
                        }
                    }
                }
            }
            $original = $customer->getOriginal();  // Get the original values before update
            $customer->update();                   // Perform the update
            $changes = $customer->getChanges();    // Get the updated values after the update

            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'from' => $original[$key] ?? null,  // Original value
                    'to' => $newValue                   // New value
                ];
            }
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($customer)
                ->withProperties(['changes' => $logData])  // Log the changes with from-to values
                ->log('Customer updated: ' . $customer->ftth_id);

            if (RadiusController::checkRadiusEnable()) {
                RadiusController::updateRadius($customer->id);
            }
        }


        return redirect()->back()->with('message', 'Customer Updated Successfully.');
    }
    public function getHistory($id)
    {
        if ($id) {
            $customer_history =  CustomerHistory::where('customer_id', '=', $id)
                ->leftjoin('status', 'status.id', '=', 'customer_histories.old_status')
                ->leftjoin('status as s', 's.id', '=', 'customer_histories.new_status')
                ->join('users', 'users.id', '=', 'customer_histories.actor_id')
                ->leftjoin('packages as p1', DB::raw('p1.id'), '=', 'customer_histories.old_package')
                ->leftjoin('packages as p2', DB::raw('p2.id'), '=', 'customer_histories.new_package')
                ->select('customer_histories.*', 'status.name as old_status_name', 'status.color as status_color', 'users.name as actor_name', DB::raw('p1.name as old_package_name'), DB::raw('p2.name as new_package_name'), DB::raw('s.name as new_status_name'))
                ->OrderBy('customer_histories.id', 'DESC')
                ->get();
            return response()->json($customer_history, 200);
        }
    }
    public function getmaxid()
    {
        $customers = Customer::where(function ($query) {
            return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
        })->get();
        $cities = City::all();
        $max_c_id = array();

        foreach ($cities as $city) {

            $cid = array();
            foreach ($customers as $customer) {
                ///(^TCL[0-9]{5}-[A-Z]{3,})$/
                $reg = "/(^" . $city->short_code . "[0-9]{5}-[A-Z 0-9]{3,})$/";
                if (preg_match($reg, $customer->ftth_id)) {
                    $pattern = '/\d+/'; // Regular expression to match integers
                    preg_match($pattern, $customer->ftth_id, $matches);

                    if (isset($matches[0])) {
                        $integer = (int)$matches[0];
                        array_push($cid, $integer);
                    }
                }
            }
            if (!empty($cid)) {
                $max_id = max($cid);
                //  dd($max_id);
            } else {
                $max_id = 0;
            }


            $id_array = array('id' => $city->id, 'value' => $max_id);

            array_push($max_c_id, $id_array);
        }

        //   if(sizeof($max_c_id))
        //dd($max_c_id);
        return $max_c_id;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::find($request->input('id'));
            $customer->deleted = 1;
            $customer->update();
            return redirect()->back();
        }
    }
}
