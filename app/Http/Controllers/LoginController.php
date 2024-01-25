<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\UsermasterModel;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::check()){
            // if user already logged in not allowing them view this page
            return redirect('/');
        }
		if($request->ajax()){
			return '{
				"data": [],
				"action":"page.refresh"
			  }';
		}else{
			return view('auth.login');
		}
    }  

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email:filter',
            'password' => 'required',
        ]);

        $flag_remember_user = false;
        if($request->input('remember') !== null && (strtolower($request->input('remember')) == 'on')){
            $flag_remember_user = true;
        }

        if (Auth::attempt(array('email' => $request->input('email'), 'password' => $request->input('password'), 'is_drm_user' => 1, 'status' => 1), $flag_remember_user)) {
            // getting logged in user id and storing it in session
            $logged_in_user_id = intval(Auth::user()->id)??0;
            // retrieving the permission for a logged in user and storing it in session
            $logged_in_user_roles_and_permissions=\App\Models\UsermasterModel::get_specific_user_role_and_permissions($logged_in_user_id);
            $request->session()->regenerate();
            session(array('logged_in_user_id' => $logged_in_user_id,
                          'logged_in_user_roles_and_permissions' => $logged_in_user_roles_and_permissions));
            unset($logged_in_user_id, $logged_in_user_roles_and_permissions);
            return redirect()->intended('/');
        }
  
        return redirect("login")->withErrors(array('email' => 'These credentials do not match our records.'));
    }

    public function registration()
    {
        if(Auth::check()){
            // if user already logged in not allowing them view this page
            return redirect('/');
        }
        return view('auth.register');
    } 

    public function customRegistration(Request $request)
    {  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,is_drm_user',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->uncompromised()],
        ]);
           
        $data = $request->all();
        $check = $this->create($data);
        
        Auth::login($check);
        return redirect("/");//->withSuccess('You have signed-in');
    }

    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }
    
    public function profile(Request $request)
    {
        $logged_in_user_id = intval(Auth::user()->id)??0;
        if(empty($logged_in_user_id)){
            // if logged in user details not found then redirecting them to home page
            return redirect('/')->with('error','Unable to fetch your profile details.');
        }

        //checking whether data posted or not
        if(count($request->all()) > 0){
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);

            $input_password = trim(strip_tags($request['password']));
            $where_conditions = array(array('id', '=', $logged_in_user_id));
            $update_data = array('password' => Hash::make($input_password));

            $update = UsermasterModel::UpdateProfile(array('where' => $where_conditions, 'data' => $update_data));

            if($update == true){
                return redirect('/')->with('success','Your profile details updated successfully.');
            }
            else{
                return redirect('/')->with('error','Unable to update your profile details.');
            }
        }
        else{
            return view('auth.profile');
        }
    }

}
