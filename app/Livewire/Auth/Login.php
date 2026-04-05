<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    /**
     * Define the validation rules for the component.
     */
    protected function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Handle the authentication attempt.
     */
    public function login()
    {
        $this->validate();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            // Throw the identical validation exception Breeze throws so users see the traditional error
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        session()->regenerate();

        // Redirect to the intended page or default to the dashboard exactly like Breeze does
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
