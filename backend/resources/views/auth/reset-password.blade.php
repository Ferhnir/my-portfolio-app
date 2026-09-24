@extends('layouts.app')

@section('title', 'Login')

@section('content')

<main class="reset-container">
    <section class="reset-card">

        <h1 class="heading">RESET PASSWORD</h1>

        <p class="description">
            ENTER YOUR NEW PASSWORD BELOW. MAKE SURE IT MEETS THE REQUIREMENTS
            TO KEEP YOUR ACCOUNT SECURE.
        </p>

        <div class="divider"></div>

        <form
                id="reset-password-form"
                method="POST"
                action="{{ route('password.update') }}"
        >
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <!-- New password -->
            <div class="form-group">
                <label
                        class="form-label"
                        for="password"
                >
                    NEW PASSWORD *
                </label>

                <p class="form-helper">
                    USE AT LEAST 8 CHARACTERS WITH A MIX OF LETTERS,
                    NUMBERS AND SYMBOLS.
                </p>

                <div class="input-wrapper">
                    <span class="input-icon input-icon-start" aria-hidden="true">
                        @include('svg.lock')
                    </span>
                    <input
                            class="form-input"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                    >

                    <button
                            class="toggle-password"
                            type="button"
                            aria-label="Show password"
                            data-target="password"
                    >

                    <span class="input-icon input-icon-end" aria-hidden="true">
                        @include('svg.eye')
                    </span>
                    </button>
                </div>
            </div>

            <!-- Confirm password -->
            <div class="form-group">
                <label
                        class="form-label"
                        for="password_confirmation"
                >
                    CONFIRM PASSWORD *
                </label>

                <p class="form-helper">
                    RE-ENTER YOUR NEW PASSWORD TO CONFIRM.
                </p>

                <div class="input-wrapper">

                    <span class="input-icon input-icon-start" aria-hidden="true">
                        @include('svg.lock')
                    </span>

                    <input
                            class="form-input"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            placeholder="Confirm new password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                    >

                    <button
                            class="toggle-password"
                            type="button"
                            aria-label="Show password"
                            data-target="password_confirmation"
                    >

                        <span class="input-icon input-icon-end" aria-hidden="true">
                            @include('svg.eye')
                        </span>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button
                    class="submit-button"
                    type="submit"
            >
                <span class="button-icon" aria-hidden="true">
                    @include('svg.lock')
                </span>

                RESET PASSWORD

                <span class="button-arrow">→</span>
            </button>

        </form>

        @if ($errors->any())
            <div class="error-alert" role="alert">
                <span class="error-icon" aria-hidden="true">
                    @include('svg.error')
                </span>
                <span class="error-text">
                    {{ $errors->first() }}
                </span>
            </div>
        @endif

        <div class="bottom-divider"></div>

        <a
                class="back-link"
                href="{{ config('app.frontend_login_url') }}"
        >
            <span class="arrow">←</span>
            [ BACK TO SIGN IN ]
        </a>

    </section>
</main>

@endsection
