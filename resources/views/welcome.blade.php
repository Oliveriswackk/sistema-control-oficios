<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>SESEA - Sistema de Oficios</title>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Montserrat', sans-serif;}

    body {
        min-height: 100vh;
        background: #f5f7fb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', system-ui, sans-serif;
        overflow: hidden;
        color: #0f172a;
    }

    /* GRID CON PROFUNDIDAD REAL */
    .grid-bg {
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(78, 114, 223, 0.08) 1px, transparent 1px),
            linear-gradient(90deg,rgba(78, 114, 223, 0.08) 1px, transparent 1px);

        background-size: 36px 36px;

        mask-image: radial-gradient(circle at center, black 55%, transparent 100%);
        opacity: 0.7;

        pointer-events: none;
        z-index: 0;
    }

    .grid-bg::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 20%, rgba(78,115,223,0.08), transparent 40%);
    }

    /* CARD */
    .card {
        position: relative;
        z-index: 1;
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(226,232,240,0.9);
        border-radius: 18px;

        padding: 54px 56px 46px;
        width: 390px;

        text-align: center;

        box-shadow:
            0 10px 30px rgba(15,23,42,0.06),
            0 1px 2px rgba(15,23,42,0.05);

        backdrop-filter: blur(6px);
    }

    /* línea superior */
    .line-top {
        position: absolute;
        top: 0;
        left: 60px;
        right: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #4e73df, transparent);
        border-radius: 2px;
        opacity: 0.9;
    }

    /* badge */
    .acro {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.18em;
        color: #4e73df;
        background: #eef3ff;
        border: 1px solid #d6e3ff;
        border-radius: 8px;
        padding: 5px 14px;
        margin-bottom: 20px;
    }

    .title {
        font-size: 21px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 36px;
    }

    .sub {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 38px;
        line-height: 1.5;
    }

    /* BOTÓN */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        width: 100%;
        padding: 12px 0;

        background: #4e73df;
        color: #fff;

        font-size: 14px;
        font-weight: 600;

        border: none;
        border-radius: 10px;

        cursor: pointer;

        transition: all 0.18s ease;

        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    /* hover*/
    .btn:hover {
        background: #3f63c7;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(78,115,223,0.25);
    }

    .btn:active {
        transform: translateY(0px) scale(0.99);
    }

    .arrow {
        display: inline-flex;
        transition: transform 0.2s ease;
    }

    .btn:hover .arrow {
        transform: translateX(3px);
    }

    /* divider */
    .divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 26px;
        opacity: 0.9;
    }

    .divider span {
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .divider p {
        font-size: 10.5px;
        color: #94a3b8;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* punto status */
    .pulse-ring {
        position: absolute;
        width: 7px;
        height: 7px;
        right: -3px;
        top: -3px;
        border-radius: 50%;
        background: #22c55e;
        border: 2px solid #fff;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.9); }
    }
</style>

</head>
<body>

<div class="grid-bg"></div>

<div class="card">
    <div class="line-top"></div>
    <div class="acro">SESEA</div>
    <div class="title">Control de Oficios</div>

    <a href="{{ route('login') }}" class="btn">
            <span>Acceder al sistema</span>
        <span class="arrow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </span>
        <div class="pulse-ring"></div>
    </a>

    <div class="divider">
        <span></span>
        <p>Plataforma de gestión interna</p>
        <span></span>
    </div>
</div>

</body>
</html>