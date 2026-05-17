<?php
// 1. Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: /apps");
    exit;
}

require_once "config.php";
require_once "lib/auth_lib.php";
require_once "lang/LanguageLoader.php";
$lang = LanguageLoader::load();
$html_lang = LanguageLoader::getLanguageCode();
check_remember_me($link);

// Check again after auto-login
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: /apps");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo LanguageLoader::getLanguageCode() === 'en' ? 'en' : (LanguageLoader::getLanguageCode() === 'de' ? 'de' : (LanguageLoader::getLanguageCode() === 'fr' ? 'fr' : 'nl')); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Check theme immediately to prevent FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <?php include 'includes/gtm.php'; ?>

    <title><?php echo htmlspecialchars($lang['seo_index_title'] ?? 'Flashcards - Het Complete Studie Ecosysteem'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($lang['seo_index_description'] ?? 'Het alles-in-een platform voor je studie. Van slimme flashcards en cijfercalculators tot gamification en agenda sync.'); ?>">

    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
    <link rel="canonical" href="https://flashcard.page.gd/" />
    <link rel="manifest" href="/pwa/manifest-pwa.php">
    <meta name="theme-color" content="#020617">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="img/apple-icon-180.png">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://flashcard.page.gd/">
    <meta property="og:title" content="Flashcards - Het Complete Studie Ecosysteem">
    <meta property="og:description" content="Het alles-in-een platform voor je studie.">
    <meta property="og:image" content="https://flashcard.page.gd/img/screenshot-desktop.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600&display=swap" rel="stylesheet">
    <script src="/js/lucide.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Light mode defaults */
            --bg:         #f8fafc;
            --bg-2:       #ffffff;
            --bg-card:    rgba(255, 255, 255, 0.85);
            --accent:     #0ea5e9;
            --accent-2:   #0284c7;
            --accent-dim: rgba(14,165,233,0.12);
            --accent-glow:rgba(14,165,233,0.3);
            --green:      #10b981;
            --amber:      #f59e0b;
            --violet:     #6366f1;
            --border:     rgba(0,0,0,0.08);
            --border-2:   rgba(0,0,0,0.12);
            --text:       #334155;
            --muted:      #64748b;
            --heading:    #0f172a;
            --dot:        rgba(14,165,233,0.15);
            --nav-bg:     rgba(248, 250, 252, 0.88);
            --showcase-grad: linear-gradient(160deg, rgba(14,165,233,0.2) 0%, rgba(99,102,241,0.13) 45%, rgba(255,255,255,0.95) 100%);
            --screen-shadow: 0 -24px 70px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.04) inset;
            --s-topbar-bg: rgba(0,0,0,0.02);
            --s-row-border: rgba(0,0,0,0.05);
            --soon-bg: rgba(0,0,0,0.04);
            --btn-ghost-bg: rgba(0,0,0,0.05);
            --btn-ghost-hover: rgba(14,165,233,0.08);
        }

        html.dark {
            --bg:         #020617;
            --bg-2:       #080f20;
            --bg-card:    rgba(10, 20, 40, 0.8);
            --accent:     #0ea5e9;
            --accent-2:   #0284c7;
            --accent-dim: rgba(14,165,233,0.12);
            --accent-glow:rgba(14,165,233,0.4);
            --green:      #10b981;
            --amber:      #f59e0b;
            --violet:     #6366f1;
            --border:     rgba(255,255,255,0.07);
            --border-2:   rgba(255,255,255,0.12);
            --text:       #cbd5e1;
            --muted:      #475569;
            --heading:    #f1f5f9;
            --dot:        rgba(56,189,248,0.16);
            --nav-bg:     rgba(2, 6, 23, 0.88);
            --showcase-grad: linear-gradient(160deg, rgba(14,165,233,0.2) 0%, rgba(99,102,241,0.13) 45%, rgba(8,15,32,0.95) 100%);
            --screen-shadow: 0 -24px 70px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04) inset;
            --s-topbar-bg: rgba(255,255,255,0.02);
            --s-row-border: rgba(255,255,255,0.05);
            --soon-bg: rgba(255,255,255,0.04);
            --btn-ghost-bg: rgba(255,255,255,0.05);
            --btn-ghost-hover: rgba(14,165,233,0.08);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Dot grid */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: radial-gradient(var(--dot) 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(ellipse at 50% 40%, black 20%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at 50% 40%, black 20%, transparent 75%);
            pointer-events: none;
        }

        /* Orbs */
        .orb {
            position: fixed;
            border-radius: 9999px;
            filter: blur(130px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width:650px; height:650px; top:-18%; left:12%;  background:rgba(14,165,233,0.11); animation: drift 22s ease-in-out infinite; }
        .orb-2 { width:500px; height:500px; bottom:-12%; right:5%; background:rgba(99,102,241,0.09); animation: drift 28s ease-in-out infinite reverse 4s; }
        .orb-3 { width:350px; height:350px; top:40%;  left:-5%;  background:rgba(16,185,129,0.06);  animation: drift 32s ease-in-out infinite 8s; }

        @keyframes drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(35px,-25px) scale(1.04); }
            66%      { transform: translate(-20px,18px) scale(0.97); }
        }

        /* Layout */
        .wrap {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 2;
        }
        section { position: relative; z-index: 1; }

        /* Typography */
        h1, h2, h3, h4 {
            font-family: 'Outfit', sans-serif;
            color: var(--heading);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        h1 { font-size: clamp(2.5rem, 5.5vw, 4.2rem); font-weight: 800; letter-spacing: -0.03em; }
        h2 { font-size: clamp(1.9rem, 3.5vw, 2.8rem); font-weight: 700; }
        h3 { font-size: 1.05rem; font-weight: 700; }
        a  { text-decoration: none; color: inherit; }

        /* ── Buttons ────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: .9rem;
            padding: .7rem 1.5rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: transform .22s cubic-bezier(.16,1,.3,1), box-shadow .22s, background .22s;
        }
        .btn:active { transform: scale(.97) !important; }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 0 30px rgba(14,165,233,0.5);
        }
        .btn-primary:hover {
            background: var(--accent-2);
            box-shadow: 0 0 44px rgba(14,165,233,0.65);
            transform: translateY(-2px);
        }

        .btn-ghost {
            background: var(--btn-ghost-bg);
            color: var(--text);
            border: 1px solid var(--border-2);
            backdrop-filter: blur(8px);
        }
        .btn-ghost:hover {
            background: var(--btn-ghost-hover);
            border-color: rgba(14,165,233,0.35);
            color: var(--accent);
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border-2);
        }
        .btn-outline:hover {
            border-color: var(--accent);
            color: var(--accent);
            box-shadow: 0 0 18px rgba(14,165,233,0.18);
            transform: translateY(-2px);
        }

        /* ── Navbar ─────────────────────────────────────── */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--nav-bg);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            animation: navDown .7s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes navDown {
            from { transform: translateY(-100%); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.5rem;
            max-width: 1080px;
            margin: 0 auto;
        }

        .nav-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--heading);
            display: flex;
            align-items: center;
            gap: .55rem;
        }
        .logo-dot {
            width: 7px; height: 7px;
            border-radius: 9999px;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            animation: pulseDot 2.4s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.5; transform:scale(.7); }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.2rem;
            list-style: none;
        }
        .nav-links a {
            font-size: .875rem;
            font-weight: 500;
            color: var(--muted);
            transition: color .2s;
            position: relative;
        }
        .nav-links a::after {
            content:"";
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 2px;
            background: var(--accent);
            border-radius: 9999px;
            transition: width .3s cubic-bezier(.16,1,.3,1);
        }
        .nav-links a:hover { color: var(--heading); }
        .nav-links a:hover::after { width: 100%; }

        @media (max-width: 720px) {
            .nav-links { display: none; }
        }

        /* ── Section Label ──────────────────────────────── */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .3rem .8rem;
            border-radius: 9999px;
            border: 1px solid rgba(14,165,233,0.25);
            background: rgba(14,165,233,0.07);
            color: var(--accent);
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        /* ── Hero ───────────────────────────────────────── */
        .hero {
            padding: 7rem 1.5rem 5rem;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            margin-bottom: 1.6rem;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .35rem .95rem;
            border-radius: 9999px;
            border: 1px solid rgba(14,165,233,0.3);
            background: rgba(14,165,233,0.07);
            color: var(--accent);
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            animation: fadeUp .6s cubic-bezier(.16,1,.3,1) .05s both;
        }
        .badge-blink {
            width: 6px; height: 6px;
            border-radius: 9999px;
            background: var(--accent);
            box-shadow: 0 0 6px var(--accent);
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1}50%{opacity:.25} }

        .hero h1 {
            max-width: 760px;
            margin: 0 auto;
            animation: fadeUp .7s cubic-bezier(.16,1,.3,1) .14s both;
        }
        .hero h1 em { font-style:normal; color: var(--accent); }

        .hero-sub {
            max-width: 500px;
            margin: 1.25rem auto 2.5rem;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.72;
            animation: fadeUp .7s cubic-bezier(.16,1,.3,1) .22s both;
        }

        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeUp .7s cubic-bezier(.16,1,.3,1) .3s both;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(22px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Showcase ───────────────────────────────────── */
        .showcase {
            max-width: 980px;
            margin: 4.5rem auto 0;
            padding: 0 1.5rem;
            position: relative;
            z-index: 2;
            animation: fadeUp .85s cubic-bezier(.16,1,.3,1) .42s both;
        }

        .showcase-box {
            border-radius: 1.5rem;
            border: 1px solid var(--border-2);
            background: var(--showcase-grad);
            padding: 3rem 2rem 0;
            overflow: hidden;
            position: relative;
        }
        .showcase-box::before {
            content:"";
            position:absolute;
            inset:0;
            background: radial-gradient(ellipse at 50% -10%, rgba(14,165,233,0.22) 0%, transparent 60%);
            pointer-events:none;
        }

        .showcase-kicker {
            text-align: center;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .4rem;
            position: relative;
        }
        .showcase-name {
            text-align: center;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            color: var(--heading);
            margin-bottom: 2.5rem;
            position: relative;
        }

        /* Mock screens */
        .screens {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 1.25rem;
            position: relative;
        }

        .screen {
            border-radius: 1.1rem 1.1rem 0 0;
            border: 1px solid var(--border-2);
            border-bottom: none;
            background: var(--bg-2);
            overflow: hidden;
            box-shadow: var(--screen-shadow);
            transition: transform .45s cubic-bezier(.16,1,.3,1);
            flex-shrink: 0;
        }
        .screen:hover { transform: translateY(-10px); }
        .screen.main { width: 220px; height: 300px; }
        .screen.side { width: 168px; height: 234px; opacity: .82; }

        .s-topbar {
            height: 30px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 .7rem;
            gap: .35rem;
            background: var(--s-topbar-bg);
        }
        .s-dot { width: 7px; height: 7px; border-radius:9999px; }

        .s-body { padding: .9rem 1rem; }

        .s-micro { font-size:.64rem; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.25rem; }
        .s-num {
            font-family:'Outfit',sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--heading);
            line-height: 1;
            margin-bottom: .5rem;
        }
        .s-bar { height:5px; border-radius:9999px; background:var(--soon-bg); overflow:hidden; margin-bottom:.5rem; }
        .s-fill {
            height:100%;
            border-radius:9999px;
            animation: fillBar 1.5s cubic-bezier(.16,1,.3,1) .9s both;
        }
        @keyframes fillBar { from{width:0} }

        .s-pill {
            display:inline-flex;
            align-items:center;
            gap:.3rem;
            padding:.22rem .55rem;
            border-radius:9999px;
            font-size:.65rem;
            font-weight:600;
            margin-bottom:.55rem;
        }

        .s-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding: .35rem 0;
            border-bottom: 1px solid var(--s-row-border);
            font-size:.7rem;
            color:var(--muted);
        }
        .s-row:last-child { border-bottom:none; }
        .s-row strong { font-size:.74rem; color:var(--heading); }

        @media (max-width:680px) {
            .screen.side { display:none; }
            .screen.main { width:80%; max-width:260px; height:260px; }
        }

        /* ── Features ───────────────────────────────────── */
        .features { padding: 7rem 0; }

        .sec-head {
            text-align: center;
            margin-bottom: 3.5rem;
        }
        .sec-head h2 { margin: .8rem 0 .75rem; }
        .sec-head p  { color:var(--muted); font-size:.95rem; line-height:1.7; max-width:460px; margin:0 auto; }

        .feat-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 1.1rem;
        }
        @media(max-width:900px) { .feat-grid{grid-template-columns:repeat(2,1fr);} }
        @media(max-width:580px) { .feat-grid{grid-template-columns:1fr;} }

        .feat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 1.2rem;
            padding: 1.65rem;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            transition: transform .4s cubic-bezier(.16,1,.3,1), border-color .3s, box-shadow .3s;
            position: relative;
            overflow: hidden;
        }
        .feat-card::after {
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(130deg,transparent 40%,var(--s-topbar-bg) 50%,transparent 60%);
            background-size:250%;
            background-position:200% 0;
            transition:background-position .6s;
            border-radius:inherit;
        }
        .feat-card:hover::after { background-position:-50% 0; }
        .feat-card:hover {
            transform: translateY(-6px);
            border-color: rgba(14,165,233,0.3);
            box-shadow: 0 22px 55px -22px rgba(14,165,233,0.2);
        }

        .f-icon {
            width: 2.6rem;
            height: 2.6rem;
            border-radius: .8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            margin-bottom: 1rem;
            transition: transform .4s cubic-bezier(.16,1,.3,1);
        }
        .feat-card:hover .f-icon { transform: scale(1.12) rotate(6deg); }
        .feat-card h3 { font-size:.98rem; margin-bottom:.45rem; }
        .feat-card p  { font-size:.845rem; color:var(--muted); line-height:1.65; }

        /* ── CTA Mid ────────────────────────────────────── */
        .cta-mid { padding: 2rem 0 6rem; }
        .cta-box {
            background: var(--bg-card);
            border: 1px solid var(--border-2);
            border-radius: 1.4rem;
            padding: 4.5rem 2rem;
            text-align: center;
            backdrop-filter: blur(14px);
            position: relative;
            overflow: hidden;
        }
        .cta-box::before {
            content:"";
            position:absolute;
            inset:0;
            background:radial-gradient(ellipse at 50% -15%,rgba(14,165,233,0.2) 0%,transparent 65%);
            pointer-events:none;
        }
        .cta-box .chip { margin-bottom:1.3rem; }
        .cta-box h2 { margin-bottom:.9rem; position:relative; }
        .cta-box p  { color:var(--muted); max-width:460px; margin:0 auto 2.2rem; line-height:1.7; font-size:.95rem; position:relative; }

        /* ── Apps ───────────────────────────────────────── */
        .apps-section { padding: 4rem 0 7rem; }
        .apps-list {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin-top: 3rem;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        .app-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 1.2rem;
            padding: 1.8rem;
            backdrop-filter: blur(14px);
            transition: transform .4s cubic-bezier(.16,1,.3,1), box-shadow .3s, border-color .3s;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            text-align: left;
        }

        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--screen-shadow);
            border-color: rgba(14,165,233,0.3);
        }

        .app-icon {
            width: 3.2rem;
            height: 3.2rem;
            border-radius: .8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: var(--btn-ghost-bg);
            color: var(--accent);
        }

        .app-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--heading);
            margin: 0 0 .4rem 0;
        }

        .app-card p {
            font-size: .9rem;
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
        }

        /* ── FAQ ────────────────────────────────────────── */
        .faq { padding: 4rem 0 7rem; }
        .faq-head {
            text-align:center;
            margin-bottom:3rem;
        }
        .faq-head h2 { margin:.8rem 0 .7rem; }
        .faq-head p  { color:var(--muted); font-size:.9rem; }

        .faq-grid {
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:2rem 2.5rem;
        }
        @media(max-width:820px) { .faq-grid{grid-template-columns:1fr;} }
        .faq-item h4 { font-size:.9rem; font-weight:700; color:var(--heading); margin-bottom:.45rem; }
        .faq-item p  { font-size:.82rem; color:var(--muted); line-height:1.72; }

        /* ── Footer ─────────────────────────────────────── */
        .foot-wrap {
            position:relative;
            z-index:2;
            max-width:1080px;
            margin:0 auto;
            padding:2rem 1.5rem;
            border-top:1px solid var(--border);
            display:flex;
            align-items:center;
            justify-content:space-between;
            flex-wrap:wrap;
            gap:1rem;
        }
        .foot-copy { font-size:.78rem; color:var(--muted); }
        .foot-links { display:flex; gap:1.5rem; }
        .foot-links a { font-size:.78rem; color:var(--muted); transition:color .2s; }
        .foot-links a:hover { color:var(--heading); }

        /* ── Scroll reveal ──────────────────────────────── */
        .rev {
            opacity:0;
            transform:translateY(26px);
            transition:opacity .7s cubic-bezier(.16,1,.3,1), transform .7s cubic-bezier(.16,1,.3,1);
        }
        .rev.d1{transition-delay:.08s} .rev.d2{transition-delay:.16s} .rev.d3{transition-delay:.24s}
        .rev.d4{transition-delay:.32s} .rev.d5{transition-delay:.40s} .rev.d6{transition-delay:.48s}
        .rev.on { opacity:1; transform:translateY(0); }

        /* View Transition */
        ::view-transition-old(root),::view-transition-new(root){animation:none;mix-blend-mode:normal}
        ::view-transition-new(root){z-index:2147483646}

        @media (max-width: 768px) {
            #theme-toggle { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- ══════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════ -->
    <nav>
        <div class="nav-inner">
            <a href="/" class="nav-logo">
                <img src="img/favicon.svg" alt="Flashcard Logo" style="width:24px; height:24px; border-radius:6px;">
                Flashcard.page
            </a>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#apps">Apps</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
            <div style="display:flex;align-items:center;gap:.65rem;">
                <button id="theme-toggle" class="btn btn-ghost" style="padding:.5rem; border-radius:50%;" aria-label="Toggle theme">
                    <i data-lucide="moon" class="dark-icon" style="width:18px;height:18px;display:none;"></i>
                    <i data-lucide="sun" class="light-icon" style="width:18px;height:18px;"></i>
                </button>
                <a href="login" class="btn btn-ghost" style="padding:.5rem 1.1rem;font-size:.875rem;">Inloggen</a>
                <a href="register" class="btn btn-primary" style="padding:.55rem 1.25rem;font-size:.875rem;">
                    Start nu <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- ══════════════════════════════════════════
         HERO
    ══════════════════════════════════════════ -->
    <section class="hero">
        <div class="hero-badge">
            <div class="badge-blink"></div>
            Jouw alles-in-één studieplatform
        </div>

        <h1>
            Het studie-ecosysteem<br>dat je <em>altijd wilde</em>
        </h1>

        <p class="hero-sub">
            Breng overzicht in je studie met slimme flashcards, cijferberekeningen,
            agenda-sync en focus-tools — alles in één rustige interface.
        </p>

        <div class="hero-btns">
            <a href="register" class="btn btn-primary">
                Gratis starten <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
            </a>
            <a href="#features" class="btn btn-outline">Ontdek de apps</a>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         SHOWCASE (App screens)
    ══════════════════════════════════════════ -->
    <div class="showcase">
        <div class="showcase-box">
            <div class="showcase-kicker">Introducing</div>
            <div class="showcase-name">Flashcard.page</div>

            <div class="screens">
                <!-- Agenda screen -->
                <div class="screen side">
                    <div class="s-topbar">
                        <div class="s-dot" style="background:#ef4444;"></div>
                        <div class="s-dot" style="background:#f59e0b;"></div>
                        <div class="s-dot" style="background:#10b981;"></div>
                    </div>
                    <div class="s-body">
                        <div class="s-micro">Agenda</div>
                        <div class="s-num" style="font-size:1.05rem;">Vandaag</div>
                        <div class="s-row"><span>Wiskunde</span><strong style="color:#f59e0b;">14:00</strong></div>
                        <div class="s-row"><span>Biologie</span><strong style="color:#0ea5e9;">15:30</strong></div>
                        <div class="s-row"><span>Nederlands</span><strong style="color:#10b981;">✓ Klaar</strong></div>
                        <div class="s-row"><span>Pomodoro</span><strong>25:00</strong></div>
                    </div>
                </div>

                <!-- Main: Flashcards screen -->
                <div class="screen main">
                    <div class="s-topbar">
                        <div class="s-dot" style="background:#ef4444;"></div>
                        <div class="s-dot" style="background:#f59e0b;"></div>
                        <div class="s-dot" style="background:#10b981;"></div>
                        <span style="font-size:.62rem;color:var(--muted);margin-left:.4rem;letter-spacing:.05em;">Flashcards</span>
                    </div>
                    <div class="s-body">
                        <div class="s-micro"><?php echo htmlspecialchars($lang['dashboard_progress_today'] ?? 'Voortgang vandaag'); ?></div>
                        <div class="s-num">12 <?php echo htmlspecialchars($lang['flashcards_deck_label'] ?? 'sets'); ?></div>
                        <div class="s-bar">
                            <div class="s-fill" style="width:72%;background:linear-gradient(90deg,#0ea5e9,#38bdf8);"></div>
                        </div>
                        <div class="s-pill" style="background:rgba(14,165,233,0.12);color:#38bdf8;">
                            <i data-lucide="zap" style="width:10px;height:10px;"></i> <?php echo htmlspecialchars($lang['dashboard_smart_mode'] ?? 'Smart mode'); ?>
                        </div>
                        <div class="s-row"><span><?php echo htmlspecialchars($lang['dashboard_learned'] ?? 'Geleerd'); ?></span><strong>86 <?php echo htmlspecialchars($lang['flashcards_decks_label'] ?? 'kaarten'); ?></strong></div>
                        <div class="s-row"><span><?php echo htmlspecialchars($lang['dashboard_to_repeat'] ?? 'Te herhalen'); ?></span><strong style="color:#f59e0b;">14</strong></div>
                        <div class="s-row"><span><?php echo htmlspecialchars($lang['flashcards_stats_streak'] ?? 'Streak'); ?></span><strong style="color:#10b981;">12 d 🔥</strong></div>
                        <div class="s-row"><span><?php echo htmlspecialchars($lang['dashboard_sets_shared'] ?? 'Sets gedeeld'); ?></span><strong>3</strong></div>
                    </div>
                </div>

                <!-- Cijfers screen -->
                <div class="screen side">
                    <div class="s-topbar">
                        <div class="s-dot" style="background:#ef4444;"></div>
                        <div class="s-dot" style="background:#f59e0b;"></div>
                        <div class="s-dot" style="background:#10b981;"></div>
                    </div>
                    <div class="s-body">
                        <div class="s-micro">Cijfers</div>
                        <div class="s-num" style="font-size:1.05rem;">Doel: 7.8</div>
                        <div class="s-bar">
                            <div class="s-fill" style="width:58%;background:linear-gradient(90deg,#10b981,#34d399);"></div>
                        </div>
                        <div class="s-row"><span>Huidig gem.</span><strong>7.2</strong></div>
                        <div class="s-row"><span>Nodig</span><strong style="color:#f59e0b;">8.4</strong></div>
                        <div class="s-row"><span>Vakken</span><strong>6</strong></div>
                        <div class="s-row"><span>Punten</span><strong style="color:#0ea5e9;">240 ⭐</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         FEATURES
    ══════════════════════════════════════════ -->
    <section class="features" id="features">
        <div class="wrap">
            <div class="sec-head">
                <span class="chip rev">
                    <i data-lucide="layout-grid" style="width:11px;height:11px;"></i>
                    Begin te groeien met Flashcard.page
                </span>
                <h2 class="rev d1">Bouw een vliegende start<br>voor je studie</h2>
                <p class="rev d2">Alles-in-één platform dat flashcards, cijfers, planning en focus-tools combineert in één rustige interface.</p>
            </div>

            <div class="feat-grid">
                <div class="feat-card rev">
                    <div class="f-icon" style="background:rgba(14,165,233,0.1);border-color:rgba(14,165,233,0.2);color:var(--accent);">
                        <i data-lucide="layers" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Slimme Flashcards</h3>
                    <p>Traceert automatisch welke kaarten je mist en herhaalt ze slim — met smart mode en multiple-choice in dezelfde flow.</p>
                </div>
                <div class="feat-card rev d1">
                    <div class="f-icon" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2);color:#10b981;">
                        <i data-lucide="calculator" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Cijfercalculator</h3>
                    <p>Bereken direct wat je nodig hebt voor je doel. Persoonlijke drempels, heldere scenario's en direct bruikbaar advies.</p>
                </div>
                <div class="feat-card rev d2">
                    <div class="f-icon" style="background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.2);color:#f59e0b;">
                        <i data-lucide="calendar-days" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Agenda en Focus</h3>
                    <p>Plan huiswerk, koppel Magister of iCal en schakel door naar Pomodoro — alles in één doorlopende studieflow.</p>
                </div>
                <div class="feat-card rev d1">
                    <div class="f-icon" style="background:rgba(99,102,241,0.1);border-color:rgba(99,102,241,0.2);color:#6366f1;">
                        <i data-lucide="clock-3" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Pomodoro Timer</h3>
                    <p>Start focusblokken direct naast je planning. Geen extra tabs, geen contextwissels — gewoon leren.</p>
                </div>
                <div class="feat-card rev d2">
                    <div class="f-icon" style="background:rgba(14,165,233,0.1);border-color:rgba(14,165,233,0.2);color:var(--accent);">
                        <i data-lucide="notebook-pen" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Notities</h3>
                    <p>Schrijf notities gekoppeld aan je flashcard-sets of agenda-items. Altijd de juiste context, nooit context kwijt.</p>
                </div>
                <div class="feat-card rev d3">
                    <div class="f-icon" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.2);color:#10b981;">
                        <i data-lucide="share-2" style="width:17px;height:17px;"></i>
                    </div>
                    <h3>Samenwerken</h3>
                    <p>Deel sets en notities met klasgenoten. Samen studeren zonder dat de interface druk of onoverzichtelijk wordt.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         CTA MID
    ══════════════════════════════════════════ -->
    <section class="cta-mid">
        <div class="wrap">
            <div class="cta-box rev">
                <span class="chip" style="margin-bottom:1.3rem;display:inline-flex;">
                    <i data-lucide="rocket" style="width:11px;height:11px;"></i>
                    Klaar om te starten
                </span>
                <h2>Geef je studie een vliegende start</h2>
                <p>Flashcard.page helpt je slimmer leren, cijfers bijhouden en je agenda in de hand houden — klaar voor iedere toetsweek.</p>
                <a href="register" class="btn btn-primary">
                    Gratis starten <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         APPS
    ══════════════════════════════════════════ -->
    <section class="apps-section" id="apps">
        <div class="wrap">
            <div class="sec-head">
                <span class="chip rev">
                    <i data-lucide="layout-grid" style="width:11px;height:11px;"></i>
                    Onze Apps
                </span>
                <h2 class="rev d1">Alles wat je nodig hebt,<br>helemaal gratis.</h2>
            </div>

            <div class="apps-list">
                <!-- App 1: Flashcards -->
                <div class="app-card rev">
                    <div class="app-icon">
                        <i data-lucide="layers" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Flashcards</h3>
                        <p>Oefen met slimme sets. Ons algoritme traceert automatisch welke kaarten je mist en herhaalt ze op het juiste moment voor maximaal resultaat.</p>
                    </div>
                </div>

                <!-- App 2: Cijfers -->
                <div class="app-card rev d1">
                    <div class="app-icon" style="color:#10b981;">
                        <i data-lucide="calculator" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Cijfercalculator</h3>
                        <p>Krijg grip op je gemiddelde. Bereken exact wat je moet halen voor je volgende toets en bekijk persoonlijke scenario's om je doelen te bereiken.</p>
                    </div>
                </div>

                <!-- App 3: Agenda & Pomo -->
                <div class="app-card rev d2">
                    <div class="app-icon" style="color:#f59e0b;">
                        <i data-lucide="calendar-clock" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Agenda & Pomodoro</h3>
                        <p>Plan je huiswerk, koppel je rooster en start direct een focus timer. Geen extra tabbladen nodig, alles in een vloeiende workflow.</p>
                    </div>
                </div>

                <div class="app-card rev d3">
                    <div class="app-icon" style="color:#06b6d4;">
                        <i data-lucide="notebook-pen" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Notities</h3>
                        <p>Maak en beheer notities per vak, zodat theorie en flashcards samen in een duidelijke leerflow blijven.</p>
                    </div>
                </div>

                <div class="app-card rev d1">
                    <div class="app-icon" style="color:#8b5cf6;">
                        <i data-lucide="bot" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Assistant</h3>
                        <p>Gebruik AI-ondersteuning voor uitleg, hulp bij studeren en het structureren van leerstof binnen je workflow.</p>
                    </div>
                </div>

                <div class="app-card rev d2">
                    <div class="app-icon" style="color:#eab308;">
                        <i data-lucide="store" style="width:20px;height:20px;"></i>
                    </div>
                    <div>
                        <h3>Shop & Rewards</h3>
                        <p>Verdien punten met leren, koop items in de shop en volg je ranking als extra motivatie.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         FAQ
    ══════════════════════════════════════════ -->
    <section class="faq" id="faq">
        <div class="wrap">
            <div class="faq-head rev">
                <span class="chip" style="display:inline-flex;">
                    <i data-lucide="help-circle" style="width:11px;height:11px;"></i>
                    FAQ
                </span>
                <h2>Alles wat je wilt weten</h2>
                <p>Hier zijn de vragen die mensen het vaakst stellen.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item rev">
                    <h4>Wat zijn slimme flashcards?</h4>
                    <p>Flashcard.page traceert automatisch welke kaarten je het minst goed kent en legt daar extra nadruk op, zodat je leertijd optimaal wordt benut.</p>
                </div>
                <div class="faq-item rev d1">
                    <h4>Wat voor studiemogelijkheden zijn er?</h4>
                    <p>Je kunt leren met Slimme Modus, Multiple Choice en schriftelijke examens. Zo oefen je actief en herhaal je gericht wat nog lastig is.</p>
                </div>
                <div class="faq-item rev d2">
                    <h4>Kan ik mijn planning koppelen?</h4>
                    <p>Ja. In de agenda kun je taken plannen en iCal koppelen (zoals Magister of Google Agenda), zodat je studieplanning centraal blijft.</p>
                </div>
                <div class="faq-item rev d1">
                    <h4>Hoe werken cijfers en voortgang?</h4>
                    <p>Met de cijfer tools bereken je wat je nog moet halen. Daarnaast zie je per vak voortgang en grafieken van je flashcard kennis.</p>
                </div>
                <div class="faq-item rev d2">
                    <h4>Is er ook een focus timer?</h4>
                    <p>Ja, de ingebouwde Pomodoro timer helpt je met vaste focusblokken en pauzes, zodat je zonder contextwissel door kunt leren.</p>
                </div>
                <div class="faq-item rev d3">
                    <h4>Kan ik sets delen met anderen?</h4>
                    <p>Ja. Je kunt sets en notities delen via veilige links, zodat je makkelijk samenwerkt met klasgenoten.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════ -->
    <div class="foot-wrap">
        <span class="foot-copy">&copy; <?php echo date('Y'); ?> Flashcard.page.gd. Alle rechten voorbehouden.</span>
        <div class="foot-links">
            <a href="contact.php">Contact</a>
            <a href="legal/privacy.php">Privacy</a>
            <a href="readme.php">Documentatie</a>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Scroll reveal
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('on');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.rev').forEach(el => observer.observe(el));

        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.querySelector('.dark-icon');
        const lightIcon = document.querySelector('.light-icon');

        function updateThemeIcons(isDark) {
            if (isDark) {
                darkIcon.style.display = 'none';
                lightIcon.style.display = 'block';
            } else {
                darkIcon.style.display = 'block';
                lightIcon.style.display = 'none';
            }
        }

        // Initialize icons
        updateThemeIcons(document.documentElement.classList.contains('dark'));

        // Sunrise Animation Toggle
        function toggleThemeWithAnimation(event) {
            const isDark = !document.documentElement.classList.contains('dark');

            // Fallback for browsers without View Transition API
            if (!document.startViewTransition) {
                document.documentElement.classList.toggle('dark', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons(isDark);
                return;
            }

            const x = event.clientX;
            const y = event.clientY;
            const endRadius = Math.hypot(
                Math.max(x, innerWidth - x),
                Math.max(y, innerHeight - y)
            );

            const transition = document.startViewTransition(() => {
                document.documentElement.classList.toggle('dark', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeIcons(isDark);
            });

            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                document.documentElement.animate(
                    { clipPath: clipPath },
                    {
                        duration: 650,
                        easing: 'ease-out',
                        pseudoElement: '::view-transition-new(root)',
                    }
                );
            });
        }

        themeToggleBtn.addEventListener('click', (e) => {
            toggleThemeWithAnimation(e);
        });

        // Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
    <?php include "includes/cookie_banner.php"; ?>

    <!-- Global Haptics -->
    <script type="module" src="/js/haptics.js?v=<?php echo time(); ?>"></script>
</body>
</html>

