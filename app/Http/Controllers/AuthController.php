<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Services\TotpService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller {
    public function showLogin(){return view('auth.login');}
    public function showRegister(Request $request){return view('auth.register-form',['type'=>$request->query('type','customer')]);}
    public function register(Request $request,TotpService $totp){
        $data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:190|unique:users,email','phone'=>'nullable|string|max:40','password'=>'required|string|min:8|confirmed','type'=>'required|in:customer,b2b_customer,vendor']);
        $secret=$totp->generateSecret();
        $user=User::create(['name'=>$data['name'],'email'=>$data['email'],'phone'=>$data['phone']??null,'password'=>$data['password'],'role'=>$data['type'],'status'=>$data['type']==='vendor'?'pending':'active','totp_secret'=>encrypt($secret),'two_factor_enabled'=>true]);
        if($user->role==='vendor') Vendor::create(['user_id'=>$user->id,'business_name'=>$user->name,'status'=>'pending']); DB::table('wallets')->insert(['user_id'=>$user->id,'currency'=>'USD','balance'=>0,'status'=>'active','created_at'=>now(),'updated_at'=>now()]);
        $this->sendOtp($user);
        session(['pending_auth_user'=>$user->id,'show_totp_setup'=>true]);
        return redirect()->route('auth.otp')->with('success','Verification code sent to your email.');
    }
    public function login(Request $request){
        $data=$request->validate(['email'=>'required|email','password'=>'required|string']);
        $user=User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password) || in_array($user->status,['blocked','suspended'])) return back()->withErrors(['email'=>'Invalid credentials or account unavailable.']);
        $this->sendOtp($user); session(['pending_auth_user'=>$user->id]);
        return redirect()->route('auth.otp')->with('success','Verification code sent to your email.');
    }
    public function otp(){abort_unless(session('pending_auth_user'),403);return view('auth.otp');}
    public function verifyOtp(Request $request,TotpService $totp){
        $request->validate(['code'=>'required|digits:6']);
        $user=User::findOrFail(session('pending_auth_user'));
        $otp=$user->otpCodes()->where('purpose','login')->whereNull('consumed_at')->where('expires_at','>',now())->latest()->first();
        if(!$otp || !Hash::check($request->code,$otp->code_hash)) return back()->withErrors(['code'=>'Invalid or expired verification code.']);
        $otp->update(['consumed_at'=>now()]);
        session(['pending_totp_user'=>$user->id]); return redirect()->route('auth.totp');
    }
    public function totp(){abort_unless(session('pending_totp_user'),403);$user=User::findOrFail(session('pending_totp_user'));$setup=null;if(session('show_totp_setup')){$secret=decrypt($user->totp_secret);$setup=['secret'=>$secret,'uri'=>app(TotpService::class)->uri($user->email,$secret)];}return view('auth.totp',compact('setup'));}
    public function verifyTotp(Request $request,TotpService $totp){
        $request->validate(['code'=>'required|digits:6']); $user=User::findOrFail(session('pending_totp_user'));
        if(!$user->totp_secret || !$totp->verify(decrypt($user->totp_secret),$request->code)) return back()->withErrors(['code'=>'Invalid authenticator code.']);
        Auth::login($user,true); $request->session()->regenerate(); session()->forget(['pending_auth_user','pending_totp_user','show_totp_setup']);
        return match($user->role){'super_admin','admin','staff'=>redirect()->route('admin.dashboard'),'vendor'=>redirect()->route('vendor.dashboard'),default=>redirect()->route('customer.dashboard')};
    }
    public function logout(Request $request){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('home');}
    private function sendOtp(User $user): void {
        $code=(string)random_int(100000,999999);
        $user->otpCodes()->create(['purpose'=>'login','code_hash'=>Hash::make($code),'expires_at'=>now()->addMinutes((int)env('OTP_EXPIRY_MINUTES',10))]);
        Mail::raw("Your Chacha Prime verification code is {$code}. It expires soon.",fn($m)=>$m->to($user->email)->subject('Chacha Prime verification code'));
    }
}