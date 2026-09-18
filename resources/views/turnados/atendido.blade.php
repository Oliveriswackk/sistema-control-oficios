<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnado atendido</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">

        <div class="card border-0 shadow-sm text-center" style="max-width: 480px; width: 100%;">
            <div class="card-body p-5">

                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                </div>

                @if($yaAtendido)
                    <h4 class="mb-3">Este turnado ya fue atendido</h4>

                    <p class="text-muted mb-0">
                        El oficio <strong>{{ $turnado->oficio->numero_oficio }}</strong>
                        ya había sido marcado como atendido.
                    </p>
                @else
                    <h4 class="mb-3">Turnado atendido</h4>

                    <p class="text-muted mb-0">
                        El oficio <strong>{{ $turnado->oficio->numero_oficio }}</strong>
                        ha sido marcado como atendido correctamente.
                    </p>
                @endif

            </div>
        </div>

    </div>

</body>
</html>