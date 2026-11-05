<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
   
    public function create(): View
    {
        return view('auth.login');
    }

   
   
   public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $usuario = Auth::user();

        // CLIENTE
        if ((int) $usuario->rol_id === 4) {
            $request->session()->forget('url.intended');

            return redirect()->route('portal-cliente.inicio');
        }

        // MECÁNICO
        if ((int) $usuario->rol_id === 2) {
            $request->session()->forget('url.intended');

            return redirect()->route('ordenServicio.listado');
        }

        // ADMINISTRADOR Y ENCARGADO
        if (in_array((int) $usuario->rol_id, [1, 3], true)) {
            $request->session()->forget('url.intended');

            return redirect()->route('home');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors([
                'email' => 'Tu usuario no tiene un rol autorizado.'
            ]);
    }


    
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
