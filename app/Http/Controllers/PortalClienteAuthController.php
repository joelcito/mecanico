<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PortalClienteAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if ((int) Auth::user()->rol_id === 4) {
                return redirect()->route('portal-cliente.inicio');
            }

            if (in_array((int) Auth::user()->rol_id, [1, 3], true)) {
                return redirect()->route('home');
            }

            if ((int) Auth::user()->rol_id === 2) {
                return redirect()->route('ordenServicio.listado');
            }
        }

        return view('portal-cliente.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credenciales, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Las credenciales ingresadas son incorrectas.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ((int) Auth::user()->rol_id !== 4) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('portal-cliente.login')
                ->withErrors([
                    'email' => 'Este acceso es exclusivo para clientes.',
                ]);
        }

        return redirect()->route('portal-cliente.inicio');
    }
}
