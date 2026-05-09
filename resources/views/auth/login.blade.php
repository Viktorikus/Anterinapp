<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="text-align: left;">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control @error('email') input-error @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control @error('password') input-error @enderror" type="password" name="password" required autocomplete="current-password" />
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input id="remember_me" type="checkbox" name="remember" style="accent-color: var(--accent);">
            <label for="remember_me" style="color: var(--text-secondary); font-size: 13px; margin: 0;">{{ __('Remember me') }}</label>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 24px;">
            <button class="btn btn-primary w-full" style="justify-content: center; padding: 12px;">
                {{ __('Log in') }}
            </button>

            <div style="display: flex; justify-content: space-between; font-size: 12px;">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color: var(--accent); text-decoration: none;">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" style="color: var(--text-secondary); text-decoration: none;">
                        {{ __('Register') }}
                    </a>
                @endif
            </div>
        </div>
    </form>
</x-guest-layout>
