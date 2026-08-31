<x-guest-layout>

<style>
    .transition-link:hover {
        color: #5e18b6 !important;
        text-decoration: none;
    }

    /* Azul institucional del sistema */
    .btn-primary,
    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active,
    .btn-primary:not(:disabled):not(.disabled):active {
        color: #fff !important;
        background-color: #4e73df !important;
        border-color: #4e73df !important;
    }

    html {
        scroll-behavior: smooth;
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f3f8;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .fade-text {
        opacity: 0;
        transform: translateY(6px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .fade-text.show {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<!-- Contenedor principal -->

<div class="container-fluid p-0 vh-100 overflow-y-auto"
     style="scroll-behavior: smooth;"
     id="mainContainer">

<!-- ================================================================= -->
<!-- SECCIÓN 1: HERO INSTITUCIONAL                                    -->
<!-- ================================================================= -->

<div id="hero-section"
     class="position-relative vh-100 d-flex flex-column align-items-center justify-content-center text-center px-4 overflow-hidden animate-initial"
     style="background-color: #f4f6f9;
            background-image: radial-gradient(#d1d8e0 1px, transparent 1px);
            background-size: 24px 24px;">

    <!-- Destello superior izquierdo -->
    <div class="position-absolute rounded-circle"
         style="width: 550px;
                height: 550px;
                background: rgba(78, 115, 223, 0.08);
                top: -100px;
                left: -100px;
                filter: blur(75px);
                pointer-events: none;">
    </div>

    <!-- Destello inferior derecho -->
    <div class="position-absolute rounded-circle"
         style="width: 500px;
                height: 500px;
                background: rgba(94, 24, 182, 0.04);
                bottom: -120px;
                right: -120px;
                filter: blur(85px);
                pointer-events: none;">
    </div>


    <!-- Contenido central -->
    <div style="max-width: 750px; z-index: 10;">

        <!-- Badge institucional -->
        <div class="mb-4">

            <span class="badge px-4 py-2 font-weight-normal shadow-sm d-inline-flex align-items-center"
                  style="background-color: #ffffff;
                         color: #4e73df;
                         border-radius: 2rem;
                         font-size: 0.85rem;
                         border: 1px solid rgba(78, 115, 223, 0.25);">

                <i class="fas fa-shield-alt mr-2 text-primary"></i>

                <span id="dynamicPhrase" class="fade-text show">
                    Plataforma oficial de gestión y control documental
                </span>

            </span>

        </div>


        <!-- Título principal -->
        <h1 class="font-weight-bold text-gray-900 mb-3"
            style="font-size: 2.75rem;
                   letter-spacing: -1px;
                   line-height: 1.2;">

            Sistema de Control de
            <span style="color: #4e73df;">Oficios</span>

        </h1>


        <!-- Subtítulo -->
        <p class="text-muted mb-5 px-sm-5"
           style="font-size: 1.1rem;
                  line-height: 1.6;">

            Sistema centralizado para la trazabilidad, seguimiento y administración eficiente de la correspondencia institucional.

        </p>


        <!-- Botón -->
        <div>

            <a href="#login-section"
               class="btn btn-primary px-5 py-3 font-weight-bold shadow-sm d-inline-flex align-items-center"
               style="border-radius: 2rem;
                      font-size: 0.95rem;
                      background-color: #4e73df;
                      border: none;
                      transition: all 0.3s ease;">

                <span>Iniciar Sesión</span>

                <i class="fas fa-arrow-down ml-2 fa-xs"></i>

            </a>

        </div>

    </div>


    <!-- Indicador de scroll -->
    <div class="position-absolute"
         style="bottom: 35px;
                left: 50%;
                transform: translateX(-50%);">

        <a href="#login-section"
           class="text-muted small text-decoration-none d-flex flex-column align-items-center"
           style="opacity: 0.65;
                  transition: opacity 0.2s;">

            <span style="font-size: 0.7rem;
                         letter-spacing: 1.5px;"
                  class="mb-1 font-weight-bold">

                DESLIZA PARA ACCEDER

            </span>

            <i class="fas fa-chevron-down fa-xs animate-bounce"></i>

        </a>

    </div>

</div>


<!-- ================================================================= -->
<!-- SECCIÓN 2: FORMULARIO DE ACCESO                                  -->
<!-- ================================================================= -->

<div id="login-section"
     class="position-relative vh-100 d-flex flex-column align-items-center justify-content-center px-3"
     style="background-color: #f4f6f9;
            background-image: radial-gradient(#d1d8e0 1px, transparent 1px);
            background-size: 24px 24px;">


    <!-- Destello superior izquierdo -->
    <div class="position-absolute rounded-circle"
         style="width: 500px;
                height: 500px;
                background: rgba(78, 115, 223, 0.07);
                top: -100px;
                left: -100px;
                filter: blur(70px);
                pointer-events: none;">
    </div>


    <!-- Destello inferior derecho -->
    <div class="position-absolute rounded-circle"
         style="width: 450px;
                height: 450px;
                background: rgba(94, 24, 182, 0.04);
                bottom: -120px;
                right: -120px;
                filter: blur(80px);
                pointer-events: none;">
    </div>


    <!-- Tarjeta de acceso -->
    <div class="container px-3"
         style="max-width: 480px; z-index: 10;">

        <div class="card border-0 shadow-lg"
             style="border-radius: 1rem;
                    background: #ffffff;
                    border-top: 4px solid #4e73df !important;">

            <div class="card-body p-4 p-sm-5">


                <!-- Logotipo y encabezado -->
                <div class="text-center mb-4 pb-2 border-bottom">

                    <div class="mb-3 d-inline-block">

                        <img src="{{ asset('images/logo-sesea.png') }}"
                             alt="SESEA Logo"
                             style="max-height: 52px; width: auto;"
                             class="img-fluid">

                    </div>


                    <h5 class="font-weight-bold text-gray-900 mb-1"
                        style="letter-spacing: -0.3px;">

                        Control de Oficios

                    </h5>


                    <p class="text-muted small mb-0">

                        Sistema de Consulta y Gestión

                    </p>

                </div>


                <!-- Session Status -->
                <x-auth-session-status
                    class="mb-4"
                    :status="session('status')" />


                <!-- ALERTA DE ERRORES -->
                @if ($errors->any())

                    <div class="alert alert-danger border-0 small py-2 px-3 mb-3 shadow-sm text-center"
                         style="border-radius: 0.5rem;
                                background-color: #f8d7da;
                                color: #721c24;">

                        <i class="fas fa-exclamation-circle mr-1"></i>

                        <span>
                            Por favor, verifique los campos ingresados en el formulario.
                        </span>

                    </div>

                @endif


                <form method="POST"
                      action="{{ route('login') }}"
                      class="user">

                    @csrf


                    <!-- Correo Electrónico -->
                    <div class="form-group mb-3">

                        <label class="small font-weight-bold text-gray-700 mb-1"
                               for="email">

                            Correo institucional

                        </label>


                        <div class="input-group">

                            <div class="input-group-prepend">

                                <div class="input-group-text bg-light border-right-0 text-muted pl-3"
                                     style="border-top-left-radius: 0.5rem;
                                            border-bottom-left-radius: 0.5rem;">

                                    <i class="fas fa-envelope fa-sm text-gray-400"></i>

                                </div>

                            </div>


                            <input id="email"
                                   type="email"
                                   class="form-control bg-light border-left-0 @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   {{ $errors->any() ? 'autofocus' : '' }}
                                   autocomplete="username"
                                   placeholder="nombre@seseachihuahua.gob.mx"
                                   style="height: 46px;
                                          font-size: 0.925rem;
                                          border-top-right-radius: 0.5rem;
                                          border-bottom-right-radius: 0.5rem;">

                        </div>


                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2 text-danger small pl-1" />

                    </div>


                    <!-- Contraseña -->
                    <div class="form-group mb-3">

                        <div class="d-flex justify-content-between align-items-center mb-1">

                            <label class="small font-weight-bold text-gray-700 m-0"
                                   for="password">

                                Contraseña

                            </label>


                            @if (Route::has('password.request'))

                                <a class="small font-weight-bold"
                                    href="{{ route('password.request') }}"
                                    style="color: #4e73df !important;">

                                        ¿Olvidaste tu clave?

                                </a>

                            @endif

                        </div>


                        <div class="input-group">

                            <div class="input-group-prepend">

                                <div class="input-group-text bg-light border-right-0 text-muted pl-3"
                                     style="border-top-left-radius: 0.5rem;
                                            border-bottom-left-radius: 0.5rem;">

                                    <i class="fas fa-lock fa-sm text-gray-400"></i>

                                </div>

                            </div>


                            <input id="password"
                                   type="password"
                                   class="form-control bg-light border-left-0 border-right-0 @error('password') is-invalid @enderror @error('email') is-invalid @enderror"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   style="height: 46px;
                                          font-size: 0.925rem;">


                            <!-- Ver / ocultar contraseña -->
                            <div class="input-group-append">

                                <button class="input-group-text bg-light border-left-0 text-muted px-3"
                                        type="button"
                                        id="togglePassword"
                                        style="border-top-right-radius: 0.5rem;
                                               border-bottom-right-radius: 0.5rem;
                                               cursor: pointer;">

                                    <i class="fas fa-eye fa-sm text-gray-400"
                                       id="toggleIcon"></i>

                                </button>

                            </div>

                        </div>


                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2 text-danger small pl-1" />

                    </div>


                    <!-- Recordar sesión -->
                    <div class="form-group mb-4">

                        <div class="custom-control custom-checkbox small">

                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="remember_me"
                                   name="remember">

                            <label class="custom-control-label text-muted"
                                   for="remember_me">

                                Mantener sesión iniciada

                            </label>

                        </div>

                    </div>


                    <!-- Botón de ingreso -->
                    <button type="submit" class="btn btn-primary btn-block font-weight-bold shadow-sm py-3" style="border-radius: 0.5rem; font-size: 0.95rem; letter-spacing: 0.3px;">
                            Ingresar al Sistema <i class="fas fa-arrow-right ml-2 fa-xs"></i>
                    </button>

                </form>

            </div>

        </div>


        <!-- Pie institucional -->
        <div class="text-center mt-4">

            <span class="text-muted font-weight-bold"
                  style="font-size: 0.7rem;
                         letter-spacing: 1px;">

                SECRETARÍA EJECUTIVA DEL SISTEMA ESTATAL ANTICORRUPCIÓN

            </span>

        </div>

    </div>

</div>

</div>

<!-- Scripts -->

<script>

    // 1. Ver / Ocultar Contraseña

    const togglePasswordBtn = document.getElementById('togglePassword');

    if (togglePasswordBtn) {

        togglePasswordBtn.addEventListener('click', function () {

            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {

                passwordInput.type = 'text';

                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');

            } else {

                passwordInput.type = 'password';

                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');

            }

        });

    }


    // 2. Frases dinámicas

    const phrases = [
        "Plataforma oficial de gestión y control documental",
        "Trazabilidad y transparencia en la correspondencia",
        "Sistema automatizado de oficios de la SESEA"
    ];

    let currentIndex = 0;

    const phraseElement = document.getElementById('dynamicPhrase');

    if (phraseElement) {

        setInterval(() => {

            phraseElement.classList.remove('show');

            setTimeout(() => {

                currentIndex = (currentIndex + 1) % phrases.length;

                phraseElement.textContent = phrases[currentIndex];

                phraseElement.classList.add('show');

            }, 500);

        }, 4000);

    }


    // 3. Auto-scroll cuando existen errores

    @if ($errors->any())

        window.addEventListener('DOMContentLoaded', () => {

            const loginSection = document.getElementById('login-section');

            if (loginSection) {

                loginSection.scrollIntoView({
                    behavior: 'smooth'
                });

            }

        });

    @endif

</script>

</x-guest-layout>
