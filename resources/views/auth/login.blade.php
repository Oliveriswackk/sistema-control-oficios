<x-guest-layout>

    <style>
        .card-login {
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            padding: 32px 28px;
        }

        .title-login {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .subtitle-login {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: 6px;
            transition: all .2s ease;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78,115,223,.15);
        }

        .btn-login {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px;
            background: #4e73df;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .btn-login:hover {
            background: #3f63c7;
        }

        .forgot-link {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-link:hover {
            color: #4e73df;
        }
    </style>

    <div class="card-login">

        <div class="title-login">
            Control de Oficios
        </div>

        <div class="subtitle-login">
            Acceso institucional
        </div>

        <x-auth-session-status
            class="mb-4"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">

                <x-input-label
                    for="email"
                    :value="__('Correo electrónico')"
                />

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="form-control-custom"
                >

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-2"
                />

            </div>

            <div class="mb-4">

                <x-input-label
                    for="password"
                    :value="__('Contraseña')"
                />

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="form-control-custom"
                >

                <x-input-error
                    :messages="$errors->get('password')"
                    class="mt-2"
                />

            </div>

            <div class="mb-4">

                <label class="inline-flex items-center">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    <span style="margin-left:8px;">
                        Recordarme
                    </span>

                </label>

            </div>

            <button
                type="submit"
                class="btn-login"
            >
                Iniciar sesión
            </button>

            @if (Route::has('password.request'))

                <div style="text-align:center; margin-top:16px;">

                    <a
                        href="{{ route('password.request') }}"
                        class="forgot-link"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>

                </div>

            @endif

        </form>

    </div>

</x-guest-layout>