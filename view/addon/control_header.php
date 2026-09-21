<?php
$title_text = isset($page_title) ? $page_title : "DASHBOARD";
?>
<style>
    .global-header {
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;
        background-color: rgba(255, 255, 255, 1);
        z-index: 9999;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: background-color 0.4s ease, backdrop-filter 0.4s ease, border-bottom 0.4s ease;
        border-bottom: 2px solid #e3e6ea;
        gap: 10px;
        flex-wrap: wrap;
    }
    .global-header.scrolled {
        background-color: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 2px solid rgba(227, 230, 234, 0.5);
    }
    .h-left { display: flex; align-items: center; gap: 15px; flex-wrap: wrap;}
    .h-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap;}
    
    .h-logo img { width: clamp(120px, 12vw, 160px); height: auto; }
    
    .btn-title-dash {
        background: linear-gradient(90deg, #154c9e, #0d6efd);
        color: #fff;
        font-weight: 900;
        padding: 0 18px;
        border-radius: 8px;
        font-size: clamp(14px, 1.2vw, 18px);
        letter-spacing: 0.5px;
        white-space: nowrap;
        margin-right: 10px;
        height: 42px; 
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-nav-group { display: flex; gap: 8px; align-items: center; }
    .btn-nav-top {
        background: #0d6efd;
        border: none;
        color: white;
        width: 42px; height: 42px; 
        border-radius: 10px;
        cursor: pointer;
        display: flex; justify-content: center; align-items: center;
        transition: 0.2s ease;
    }
    .btn-nav-top:hover {
        background: #0a58ca;
    }
    .btn-nav-top:active {
        transform: scale(0.92);
    }

    .btn-nav-top img.svg-icon {
        width: 22px; height: 22px;
        filter: brightness(0) invert(1); 
    }
    
    .ctrl-box {
        border: 2px solid #1976d2;
        background: transparent;
        color: #0d47a1;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: clamp(14px, 1.2vw, 16px);
        text-align: center;
        display: flex;
        justify-content: center; align-items: center;
        height: 40px;
    }
    .ctrl-box input, .ctrl-box select {
        border: none;
        background: transparent; font-weight: 800; color: #0d47a1;
        outline: none; cursor: pointer; text-align: center; font-size: inherit;
    }
    
    @media (max-width: 900px) {
        .global-header { flex-direction: column; align-items: stretch; }
        .h-left, .h-right { justify-content: center; width: 100%; }
    }
</style>

<div class="global-header" id="global-header">
    <div class="h-left">
        <a href="../index.php" class="h-logo" title="Capstone - Clutch Disc Production Monitoring">
            <img src="../assets/img/logo_perusahaan.png" alt="Logo">
        </a>
        <div class="btn-title-dash"><?= $title_text ?></div>
        
        <div class="btn-nav-group">
            <button class="btn-nav-top" onclick="previousPage()" title="Previous Page">
                <img src="../assets/svgs/solid/circle-arrow-left.svg" class="svg-icon" alt="<" onerror="this.style.display='none'; this.parentNode.innerHTML='&#10094;';">
            </button>
            <button class="btn-nav-top" onclick="nextPage()" title="Next Page">
                <img src="../assets/svgs/solid/circle-arrow-right.svg" class="svg-icon" alt=">" onerror="this.style.display='none'; this.parentNode.innerHTML='&#10095;';">
            </button>
            
            <button class="btn-nav-top" id="btn-pause-global" onclick="toggleAutoSlide()" title="Resume Auto Refresh" style="background: #c62828;">
                <img src="../assets/svgs/solid/play.svg" id="icon-pause-global" class="svg-icon" alt="Play" onerror="this.style.display='none'; this.parentNode.innerHTML='▶';">
            </button>
            
            <button class="btn-nav-top" onclick="window.location.href='../index.php'" title="Home">
                <img src="../assets/svgs/solid/house.svg" class="svg-icon" alt="Home" onerror="this.style.display='none'; this.parentNode.innerHTML='🏠';">
            </button>
            <button class="btn-nav-top" onclick="window.location.href='../pmc_system/index.php'" title="PMC System (perlu login)" style="width:auto; padding:0 14px; font-size:13px; font-weight:800;">
                PMC
            </button>
        </div>
    </div>
    
   <div class="h-right">
        <div class="ctrl-box" id="live-time-top">--:--:--</div>
        
        <div class="ctrl-box">
            <input type="date" id="filter-date" value="<?= date('Y-m-d') ?>">
        </div>
        
        <?php if (isset($is_wip_tracking) && $is_wip_tracking === true): ?>
        <div class="ctrl-box" style="padding: 0 10px;">
            <span style="font-size: 12px; margin-right: 5px;">SEARCH P/No:</span>
            <input type="text" id="search-part" placeholder="Type Part Number..." style="width: 160px; font-weight: 600;">
        </div>
        <?php elseif (!isset($hide_shift) || $hide_shift !== true): ?>
        <div class="ctrl-box">
            <select id="filter-shift"></select>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    window.addEventListener('scroll', () => {
        if (window.scrollY > 15) { document.getElementById('global-header').classList.add('scrolled'); }
        else { document.getElementById('global-header').classList.remove('scrolled'); }
    });

    setInterval(() => {
        let el = document.getElementById('live-time-top');
        if(el) el.innerText = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }, 1000);

    const pages = ['preforming.php', 'hotpress.php', 'drilling.php'];
    const SLIDE_INTERVAL = 3 * 60 * 1000; 
    let slideTimer;
    let isGlobalPaused = true;

    function previousPage() {
        const currentHref = window.location.href;
        let currentIndex = pages.findIndex(page => currentHref.includes(page));
        if (currentIndex === -1) currentIndex = 0;
        let prevIndex = (currentIndex - 1 + pages.length) % pages.length;
        window.location.href = pages[prevIndex];
    }

    function nextPage() {
        const currentHref = window.location.href;
        let currentIndex = pages.findIndex(page => currentHref.includes(page));
        if (currentIndex === -1) currentIndex = 0;
        let nextIndex = (currentIndex + 1) % pages.length;
        window.location.href = pages[nextIndex];
    }

    function initAutoSlide() {
        const btn = document.getElementById('btn-pause-global');
        const icon = document.getElementById('icon-pause-global');
        const isPaused = localStorage.getItem('autoSlidePaused') === 'true';
        isGlobalPaused = isPaused; 

        if (isPaused) {
            if(icon) icon.src = '../assets/svgs/solid/play.svg';
            else btn.innerHTML = '▶';
            btn.style.background = '#c62828';
            btn.title = "Jalankan Auto-Slide";
            clearTimeout(slideTimer);
        } else {
            if(icon) icon.src = '../assets/svgs/solid/pause.svg';
            else btn.innerHTML = '&#10074;&#10074;';
            btn.style.background = '#0d6efd';
            btn.title = "Hentikan Auto-Slide";
            startTimer();
        }
    }

    function startTimer() {
        clearTimeout(slideTimer);
        slideTimer = setTimeout(() => {
            nextPage(); 
        }, SLIDE_INTERVAL);
    }

    function toggleAutoSlide() {
        const isPaused = localStorage.getItem('autoSlidePaused') === 'true';
        if (isPaused) {
            localStorage.setItem('autoSlidePaused', 'false');
        } else {
            localStorage.setItem('autoSlidePaused', 'true');
        }
        initAutoSlide();
    }

    window.addEventListener('load', initAutoSlide);
</script>