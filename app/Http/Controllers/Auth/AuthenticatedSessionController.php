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
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $usuario = Auth::user();

        // El cliente no puede ingresar por el login principal
        if ((int) $usuario->rol_id === 4) {

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->forget('url.intended');

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Los clientes deben ingresar desde Mi Portal.'
                ]);
        }

        if ((int) $usuario->rol_id === 2) {
            $request->session()->forget('url.intended');

            return redirect()->route('ordenServicio.listado');
        }

        if (in_array((int) $usuario->rol_id, [1, 3], true)) {
            $request->session()->forget('url.intended');

            return redirect()->route('home');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Tu usuario no tiene un rol autorizado.']);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
