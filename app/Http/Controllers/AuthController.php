<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Mail\otpemail;
use App\Models\UserApi;
use Illuminate\Http\Request;
use App\Services\PHPMailService;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AuthController
{
    public $email = '';
    public $password = '';

    public function LoginForm()
    {
        if(session()->has('mfa_user_id')){
            session(['auth_stage'=>'mfa']);
            return view('auth.two-step');
        }   
        session(['auth_stage'=>'login']);
        return view('auth.login');
    }
    public function RegisterForm()
    {
        return view('auth.register');
    }

    public function MfaForm()
    {
        if (!session()->has('emailMasked') || !session()->has('mfa_user_id')) {
            return redirect('/login');
        }elseif(session()->has('mfa_user_id')){
            session(['auth_stage'=>'mfa']);
            return view('auth.two-step');
        }
    }

    public function login(AuthRequest $req){
        $req->validated();

        $credentials = $req->only('email', 'password');
        $remember = $req->has('remember-me'); // if remember me is checked then true
        session(['remember' => $remember]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            $user = User::where('email', $credentials['email'])->first();

            session(['mfa_user_id' => $user->id]);
            $this->sendMail($user);            
            
            return redirect()->route('mfa');
        }else{
            return redirect()->route('login')
                        ->withErrors([
                            'error' => 'The provided credentials do not match our records.',
                        ]);
        }
    }

    public function mfa(AuthRequest $req){
        $req->validated();

        $otp = $req->input('otp');

        $userId = session('mfa_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Session expired, please login again.']);
        }   

        $user = User::find($userId);

        $otpData = DB::table('otps')
            ->where('user_code', $user->user_code)
            ->first();

        if ($otpData->otp != $req->otp) {
            return redirect()->route('mfa')->withErrors(['error' => 'Invalid OTP']);
        }

        if (Carbon::now()->greaterThan($otpData->expires_at)) {
            return redirect()->route('mfa')->withErrors(['error' => 'OTP expired']);
        }
        
        // OTP is valid
        DB::table('otps')->where('user_code', $user->user_code)->delete();

        if(Auth::loginUsingId($user->id, session('remember'))){
            // Regenerate session ID for security (prevents session fixation)
            session()->regenerate();
            
            session()->put('API_TOKEN', $user->user_api);
            
            // Clear only MFA-related session data
            session()->forget(['mfa_user_id', 'emailMasked','auth_stage', 'remember']);
            return redirect()->route('index');
        }

    }

    private function maskEmail($email){
        $mailParts = explode("@", $email);
        $name = $mailParts[0];
        $len = strlen($name);

        // First 2 chars visible, rest masked
        $nameMasked = substr($name, 0, 2) . str_repeat("*", $len - 4) . substr($name, -2);

        $domain = $mailParts[1];
        $domainParts = explode(".", $domain);
        $domainName = $domainParts[0];
        $domainLen = strlen($domainName);

        // First 2 chars visible in domain
        $domainMasked = substr($domainName, 0, 2) . str_repeat("*", $domainLen - 2);

        return $nameMasked . "@" . $domainMasked . "." . $domainParts[1];
    }

    public function sendMail(User $user){
        $userId = session('mfa_user_id');
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        $user = User::findOrFail($userId);

        // Store OTP with expiry (5 minutes)
        Otp::updateOrCreate(
            ['user_code' => $user->user_code],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
                'used_for' => 'login'
            ]
        );

        $email = $user->email;
        $name = $user->name;

        $emailMasked = $this->maskEmail($email);
        session(['emailMasked' => $emailMasked,'mfa_user_id' => $user->id]);

        Mail::send(new otpemail($otp, $email, $name, 'TechSpire - OTP'));


        return redirect()->route('mfa')->with(['success' => 'OTP sent successfully.']);
        
    }

    public function register(AuthRequest $req){
        $req->validated();

        $credentials = $req->only('name', 'email', 'password');

        $exists = User::where('email', $credentials['email'])->first();

        if ($exists) {
            return redirect()->route('register')
                ->withErrors([
                    'error' => 'User already exists.',
                ]);
        }

        try {
            // Registering new user
            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'password' => $credentials['password'], 
            ]);
        
            $API_TOKEN = $user->createToken('API Token')->plainTextToken;

            $user->update([
                'user_api' => Crypt::encryptString($API_TOKEN), // encrypt token
            ]);
            // Storing api in api table
            $token = [
                'token_type' => 'Bearer',
                'access_token' => $API_TOKEN,
                'user_code' => $user->user_code
            ];

            return redirect()->route('login')
                ->with([
                    'success' => 'User register successfully.',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors([
                    'error' => 'Unable to register user. Please try again.',
                ]);
        }
    }

    public function logout(){

        $user = Auth()->user();

        if($user){
            $user->setRememberToken(null);
            $user->save();
        }

        request()->session()->invalidate(); // Destroys the session
        request()->session()->regenerateToken(); // Regenerates the CSRF token

        Auth()->logout();
        return redirect()->route('login')
                        ->with('success', 'You have been logged out successfully.');
    }
}
