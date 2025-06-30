<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
  
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
  
    use AuthenticatesUsers;
  
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Create a new controller instance.
     *
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {   
        $lang = session('app_locale', 'ro');

        $messages = $lang == 'ro' ? [
            'email.required' => 'Email este obligatoriu.',
            'email.email' => 'Email-ul trebuie să fie valid.',
            'password.required' => 'Parola este obligatorie.',
        ] : [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'password.required' => 'Password is required.',
        ];

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], $messages);

        $redirectTo = session('redirect_to', route('homepage'));

        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            session()->forget('redirect_to');
            if (auth()->user()->is_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif (auth()->user()->is_type == '2') {
                return redirect()->route('staff.home');
            } elseif (auth()->user()->is_type == '0') {
                return redirect()->to($redirectTo);
            } else {
                return redirect()->route('homepage');
            }
        } else {
            $errorMessage = $lang == 'ro' 
                ? 'Datele de autentificare sunt greșite. Încearcă din nou.' 
                : 'Wrong credentials. Please try again.';
            
            return redirect()->route('login')->withInput()->with('error', $errorMessage);
        }
    }

    public function showPasswordRequestForm()
    {
        return view('auth.passwords.email');
    }
    
}