<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Artisan;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use App\Mail\TemporaryPasswordEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;


class UsersController extends Controller {

	use ValidatesRequests;

    public function list(Request $request) {
        if(!auth()->user()->hasPermissionTo('show_users'))abort(401);
        $query = User::select('*');
        $query->when($request->keywords, 
        fn($q)=> $q->where("name", "like", "%$request->keywords%"));
        $users = $query->get();
        return view('users.list', compact('users'));
    }

	public function register(Request $request) {
        return view('users.register');
    }

    public function doRegister(Request $request) {

    	try {
    		$this->validate($request, [
	        'name' => ['required', 'string', 'min:5'],
	        'email' => ['required', 'email', 'unique:users'],
	        'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
	    	]);
    	}
    	catch(\Exception $e) {

    		return redirect()->back()->withInput($request->input())->withErrors('Invalid registration information.');
    	}

    	
    	$user =  new User();
	    $user->name = $request->name;
	    $user->email = $request->email;
	    $user->password = bcrypt($request->password); //Secure
	    $user->save();

        // Send verification email
        $token = Crypt::encryptString(json_encode([
        'id' => $user->id,
        'email' => $user->email,
    ]));

    $link = route('verify', ['token' => $token]);

    Mail::to($user->email)->send(new VerificationEmail($link, $user->name));

    return redirect()->route('login')->with('message', 'Please check your email to verify your account.');
}

    public function login(Request $request) {
        return view('users.login');
    }

    public function doLogin(Request $request) {
    	
    	if(!Auth::attempt(['email' => $request->email, 'password' => $request->password]))
            return redirect()->back()->withInput($request->input())->withErrors('Invalid login information.');

        $user = User::where('email', $request->email)->first();

         // ❗️Check if email is verified
        if (!$user->email_verified_at) {
        Auth::logout(); // تأمين زيادة علشان ميكملش السيشن
        return redirect()->back()->withInput($request->input())->withErrors('Your email is not verified.');
    }
        Auth::setUser($user);
        
        if (Hash::check($request->password, $user->password)) {
            if (Str::length($request->password) === 5) {
                // Assuming temp passwords are always 5 chars
                session(['force_password_change' => true]);
                return redirect()->route('edit_password');
            }
        }
        

        return redirect('/');
    }



    public function verify(Request $request)
{
    $decryptedData = json_decode(Crypt::decryptString($request->token), true);
    $user = User::find($decryptedData['id']);

    if (!$user) abort(401);

    $user->email_verified_at = Carbon::now();
    $user->save();

    return view('users.verified', compact('user'));
}

    public function doLogout(Request $request) {
    	
    	Auth::logout();

        return redirect('/');
    }


    public function forgotPasswordForm()
{
    return view('users.forgot_password');
}




public function sendTemporaryPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors('Email not found.');
    }

    // Generate temp password
    $tempPassword = Str::random(5);
    $user->password = bcrypt($tempPassword);
    $user->save();


    Mail::to($user->email)->send(new TemporaryPasswordEmail($user->name, $tempPassword));


    return redirect()->route('login')->with('message', 'Temporary password sent to your email.');
}


// _________________________________________________________________________________________


    public function profile(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $permissions = [];
        foreach($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach($user->roles as $role) {
            foreach($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('users.profile', compact('user', 'permissions'));
    }

    public function edit(Request $request, User $user = null) {
   
        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }
    
        $roles = [];
        foreach(Role::all() as $role) {
            $role->taken = ($user->hasRole($role->name));
            $roles[] = $role;
        }

        $permissions = [];
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach(Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }      

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user) {

        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if(auth()->user()->hasPermissionTo('admin_users')) {

            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            Artisan::call('cache:clear');
        }

        //$user->syncRoles([1]);
        //Artisan::call('cache:clear');

        return redirect(route('profile', ['user'=>$user->id]));
    }

    public function delete(Request $request, User $user) {

        if(!auth()->user()->hasPermissionTo('delete_users')) abort(401);

        $user->delete();

        return redirect()->route('users');
    }

    public function editPassword(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user) {

        if(auth()->id()==$user?->id) {
            
            $this->validate($request, [
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);

            if(!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {
                
                Auth::logout();
                return redirect('/');
            }
        }
        else if(!auth()->user()->hasPermissionTo('edit_users')) {

            abort(401);
        }

        $user->password = bcrypt($request->password); //Secure
        $user->save();

        return redirect(route('profile', ['user'=>$user->id]));
    }

   public function addCredit(Request $request, User $user) {
    // تحقق من صلاحية المستخدم
    if (!auth()->user()->hasPermissionTo('add_credit')) {
        abort(403, 'You do not have permission to add credit.');
    }

    // تحقق من وجود الكريدت في الحقل
    $credit = $request->input('credit');

    // إضافة الكريدت للمستخدم
    $user->credit += $credit;
    $user->save();

    // إعادة التوجيه إلى صفحة المستخدم بعد إضافة الكريدت
    return redirect()->route('users', ['user' => $user->id]);
}

    
    
} 