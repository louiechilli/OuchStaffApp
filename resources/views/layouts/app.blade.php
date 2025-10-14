{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#111827">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    {{-- Font Awesome (icons) --}}

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/fontawesome.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/fontawesome.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/whiteboard-semibold.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/whiteboard-semibold.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/thumbprint-light.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/thumbprint-light.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/slab-press-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/slab-press-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/slab-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/slab-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-thin.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-thin.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-light.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-duotone-light.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-thin.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-thin.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-light.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/sharp-light.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/notdog-duo-solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/notdog-duo-solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/notdog-solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/notdog-solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-fill-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-fill-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-duo-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-duo-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/jelly-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/etch-solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/etch-solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-thin.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-thin.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-light.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/duotone-light.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/thin.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/thin.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/solid.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/solid.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/regular.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/light.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/light.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/brands.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/brands.css"
        >
    </noscript>

    <link
        defer
        media="print"
        onload="this.media='all'"
        rel="stylesheet"
        href="https://site-assets.fontawesome.com/releases/v7.0.1/css/chisel-regular.css"
    >
    <noscript>
        <link
            rel="stylesheet"
            href="https://site-assets.fontawesome.com/releases/v7.0.1/css/chisel-regular.css"
        >
    </noscript>


    {{-- Tailwind via CDN for now (swap to Vite in prod) --}}
    <script>
        window.tailwind = {
            theme: {
                container: { center: true, padding: '1rem' },
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media (min-width: 768px) { :root { font-size: 17px; } }
        @media (min-width: 1024px) { :root { font-size: 16px; } }
        * { font-family: "Inter", sans-serif; }
    </style>

    @stack('styles')
    @yield('head')
</head>
<body class="h-full bg-white text-slate-800 antialiased">
<div id="app" class="min-h-dvh">
    <main id="main">
        @yield('content')
    </main>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>window.Dropzone = window.Dropzone || {}; window.Dropzone.autoDiscover = false;</script>
<script>
(function() {
    let idleTimer;
    const idleDelay = 15000; // 15 seconds

    function resetIdleTimer() {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(sendIdleUpdate, idleDelay);
    }

    async function sendIdleUpdate() {
        try {
            await fetch('/update-activity', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (error) {
            console.error('Error updating activity:', error);
        }
    }

    // Reset timer on user interactions
    const activityEvents = ['mousedown', 'keydown', 'touchstart', 'scroll', 'mousemove'];
    activityEvents.forEach(event => {
        document.addEventListener(event, resetIdleTimer, true);
    });

    // Check lock status every 15 seconds
    async function checkLockStatus() {
        try {
            const response = await fetch('/check-lock-status', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (data.locked && window.location.pathname !== '/lock') {
                window.location.href = '/lock';
            }
        } catch (error) {
            console.error('Error checking lock status:', error);
        }
    }

    if (window.location.pathname !== '/lock') {
        setInterval(checkLockStatus, 15000);
    }

    window.addEventListener('beforeunload', () => clearTimeout(idleTimer));
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            checkLockStatus();
        }
    });

    // Start idle timer initially
    resetIdleTimer();
})();
</script>
@stack('scripts')
@yield('body-end')
</body>
</html>
