<?php

namespace App\Http\Controllers\Admin;;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Auth;
use App\Notifications\WelcomeNotification;

use Illuminate\Support\Facades\Hash;

use Helper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check user has access to this file
        if(!Helper::is_user_access('user')){
            return redirect('/');
        }

        if(Auth::user()->hasRole('superadmin')){
            $userlist = User::role('admin')->get();
        }else{
            $userlist = User::where('created_by',Helper::get_parent_id())->get();
        }
        $role_list = Role::where('created_by',Helper::get_parent_id())->get();

        return view('admin.user.index',compact('userlist','role_list'));
        
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|confirmed|min:8',
        // ]);

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_by'=>Helper::get_parent_id(),
            'api_token' => Hash::make($request->username),
        ]);
        // assign role
        $user->assignRole($request->role);

        return redirect()->back()->with('success',"User Created successfully !");
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
        // check user has access to this file
        if(!Helper::is_user_access('user')){
            return redirect('/');
        }

        $userdetail = User::find($id);
        return view('admin.user.edit',compact('userdetail'));
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
        // dd($request->all());
        $updateData = $request->only('name','status');
        $status = User::find($id)->update($updateData);
        if($status)
            return redirect()->back()->with('success',"Profile updated successfully !");
        else
            return redirect()->back()->with('warning',"Unable to updated  !");
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function home(){
        $user = User::find(1);
        $data = ['rrrr'];
        $user->notify(new WelcomeNotification($data));
    
    
    }
}
