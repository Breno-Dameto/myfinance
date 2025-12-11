<?php

namespace App\Http\Controllers;

use App\Models\AuthCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOtpMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $code = rand(100000, 999999);
        
        AuthCode::create([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        Mail::to($request->email)->send(new LoginOtpMail($code));
        
        return redirect()->route('auth.verify', ['email' => $request->email])
            ->with('success', "Código enviado para {$request->email}!");
    }

    public function showVerify(Request $request)
    {
        return view('auth.verify', ['email' => $request->query('email')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric'
        ]);

        $authCode = AuthCode::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$authCode) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => 'Usuário']
        );

        // Criar categorias padrão para novos usuários
        if ($user->wasRecentlyCreated) {
            $defaults = [
                ['name' => 'Salário', 'type' => 'income'],
                ['name' => 'Freelance', 'type' => 'income'],
                ['name' => 'Alimentação', 'type' => 'expense'],
                ['name' => 'Moradia', 'type' => 'expense'],
                ['name' => 'Transporte', 'type' => 'expense'],
                ['name' => 'Lazer', 'type' => 'expense'],
            ];
            foreach ($defaults as $cat) {
                $user->categories()->create($cat);
            }
        }

        $authCode->delete();
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('info', 'Você foi desconectado, acesse novamente para voltar a utilizar.');
    }
}
