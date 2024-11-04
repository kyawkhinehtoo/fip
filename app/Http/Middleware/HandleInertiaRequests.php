<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = Auth::user();
        $role = null;
        if($user != null){
    
            $role = Role::join('users','users.role','=','roles.id')
                ->where('users.id','=',$user->id)
                ->first();
        }
        return array_merge(parent::share($request), [
            'role'=>$role,
            // 'errors' => [
            //     $request->session()->get('errors')
            //     ? $request->session()->get('errors')->getBag('default')->getMessages()
            //     : (object) []
            // ],
            // 'flash' => [
            //     'message' =>$request->session()->get('message')
            //     ? $request->session()->get('message')
            //     : (object) [],
            // ],
            // 'response' => [
            //     'id' => $request->session()->get('id')
            //     ? $request->session()->get('id')
            //     : (object) [],
            // ],
        ]);
    }
}
