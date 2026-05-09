<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" style="text-align: left;">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" class="form-control @error('name') input-error @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" class="form-control @error('email') input-error @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control @error('password') input-error @enderror" type="password" name="password" required autocomplete="new-password" />
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-control @error('password_confirmation') input-error @enderror" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 24px;">
            <button class="btn btn-primary w-full" style="justify-content: center; padding: 12px;">
                {{ __('Register') }}
            </button>

            <div style="text-align: center; font-size: 12px;">
                <a href="{{ route('login') }}" style="color: var(--text-secondary); text-decoration: none;">
                    {{ __('Already registered?') }} <span style="color: var(--accent);">Login</span>
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
