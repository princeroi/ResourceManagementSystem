{{-- resources/views/filament/pages/office-supply-request.blade.php --}}
<x-filament-panels::page>
<style>
/* ═══════════════════════════════════════════
   POS LAYOUT — light + dark mode, scoped to .pos-*
═══════════════════════════════════════════ */

/* ── CSS vars: Light ── */
.pos-wrap {
    --pos-bg:         #f3f4f6;
    --pos-base:       #ffffff;
    --pos-base-tint:  #fafafa;
    --pos-sunken:     #f9fafb;
    --pos-border:     #e5e7eb;
    --pos-border-md:  #d1d5db;
    --pos-header:     #1e3a5f;
    --pos-text:       #111827;
    --pos-text-2:     #374151;
    --pos-text-3:     #6b7280;
    --pos-text-4:     #9ca3af;
    --pos-accent:     #166df5;
    --pos-accent-dk:  #0e55d0;
    --pos-accent-bg:  #eff5ff;
    --pos-accent-sel: #eff5ff;
    --pos-sel-border: #166df5;
    --pos-warn:       #d97706;
    --pos-err:        #ef4444;
    --pos-overlay:    rgba(0,0,0,0.55);
    --pos-shadow:     0 6px 18px rgba(22,109,245,0.10);
}

/* ── CSS vars: Dark ── */
.dark .pos-wrap,
.dark {
    --pos-bg:         #0b0f1a;
    --pos-base:       #111827;
    --pos-base-tint:  #141c2e;
    --pos-sunken:     #0d1220;
    --pos-border:     rgba(255,255,255,0.08);
    --pos-border-md:  rgba(255,255,255,0.12);
    --pos-header:     #0d1e35;
    --pos-text:       #e8edf7;
    --pos-text-2:     #cbd5e1;
    --pos-text-3:     #94a3b8;
    --pos-text-4:     #4d6080;
    --pos-accent:     #4d8ef7;
    --pos-accent-dk:  #166df5;
    --pos-accent-bg:  rgba(22,109,245,0.14);
    --pos-accent-sel: rgba(22,109,245,0.12);
    --pos-sel-border: #4d8ef7;
    --pos-warn:       #f59e0b;
    --pos-err:        #f87171;
    --pos-overlay:    rgba(0,0,0,0.72);
    --pos-shadow:     0 6px 18px rgba(0,0,0,0.40);
}

/* ── Outer shell ── */
.pos-wrap {
    display: grid;
    grid-template-columns: 1fr 320px;
    height: calc(100vh - 120px);
    border: 1.5px solid var(--pos-border-md);
    border-radius: 10px;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--pos-bg);
}

/* ── LEFT: catalog ── */
.pos-left {
    display: flex;
    flex-direction: column;
    background: var(--pos-bg);
    overflow: hidden;
}

/* ── Category chip bar wrapper ── */
.pos-catbar-wrap {
    position: relative;
    background: var(--pos-header);
    flex-shrink: 0;
}

/* Scroll arrow buttons */
.pos-catarrow {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 40px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: opacity 0.2s;
    padding: 0;
}
.pos-catarrow.left  { left:  0; background: linear-gradient(90deg,  var(--pos-header) 60%, transparent); }
.pos-catarrow.right { right: 0; background: linear-gradient(270deg, var(--pos-header) 60%, transparent); }
.pos-catarrow svg   { width: 14px; height: 14px; flex-shrink: 0; }

/* Scrollable chip strip */
.pos-catbar {
    background: var(--pos-header);
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    overflow-x: auto;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.2) transparent;
}
.pos-catbar::-webkit-scrollbar        { height: 3px; }
.pos-catbar::-webkit-scrollbar-track  { background: transparent; }
.pos-catbar::-webkit-scrollbar-thumb  { background: rgba(255,255,255,0.2); border-radius: 999px; }
.pos-catbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }

/* Pill chip */
.pos-catbtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    color: rgba(255,255,255,0.55);
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    border: 1.5px solid rgba(255,255,255,0.14);
    background: rgba(255,255,255,0.06);
    font-family: 'Plus Jakarta Sans', sans-serif;
    letter-spacing: 0.01em;
    transition: all 0.15s;
}
.pos-catbtn::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.5;
    flex-shrink: 0;
}
.pos-catbtn.active {
    background: var(--pos-accent);
    border-color: var(--pos-accent);
    color: #fff;
    font-weight: 600;
}
.pos-catbtn.active::before {
    background: rgba(255,255,255,0.8);
    opacity: 1;
}
.pos-catbtn:hover:not(.active) {
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.28);
    color: rgba(255,255,255,0.88);
}

/* Toolbar / search */
.pos-toolbar {
    background: var(--pos-base);
    border-bottom: 1px solid var(--pos-border);
    padding: 8px 12px;
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.pos-search {
    flex: 1;
    padding: 7px 10px;
    border: 1px solid var(--pos-border-md);
    border-radius: 6px;
    font-size: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--pos-sunken);
    color: var(--pos-text);
    outline: none;
    transition: border-color 0.12s;
}
.pos-search::placeholder { color: var(--pos-text-4); }
.pos-search:focus {
    border-color: var(--pos-accent);
    background: var(--pos-base);
}

/* Section label */
.pos-seclabel {
    font-size: 10px;
    font-weight: 800;
    color: var(--pos-text-4);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 10px 12px 6px;
}

/* Item grid scroll area */
.pos-grid-wrap {
    flex: 1;
    overflow-y: auto;
    padding: 0 10px 10px;
}
.pos-grid-wrap::-webkit-scrollbar { width: 4px; }
.pos-grid-wrap::-webkit-scrollbar-thumb { background: var(--pos-border-md); border-radius: 4px; }

/* Item grid */
.pos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 8px;
}

/* ── Item card: now a div, not a button ── */
.pos-icard {
    background: var(--pos-base);
    border: 1.5px solid var(--pos-border);
    border-radius: 8px;
    padding: 12px 8px 10px;
    text-align: center;
    position: relative;
    font-family: 'Plus Jakarta Sans', sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: border-color 0.12s, background 0.12s;
}
.pos-icard:hover:not(.oos) {
    border-color: var(--pos-accent);
    background: var(--pos-accent-bg);
}
.pos-icard.sel {
    border: 2px solid var(--pos-sel-border);
    background: var(--pos-accent-sel);
}
.pos-icard.oos {
    opacity: 0.4;
    pointer-events: none;
}

/* Item icon box */
.pos-icon {
    width: 46px;
    height: 46px;
    border-radius: 8px;
    background: var(--pos-bg);
    margin: 0 auto 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.pos-icard.sel .pos-icon { background: rgba(22,109,245,0.12); }
.pos-icon svg { width: 22px; height: 22px; color: var(--pos-text-4); }

.pos-iname  { font-size: 11px; font-weight: 700; color: var(--pos-text);   line-height: 1.3; margin-bottom: 2px; }
.pos-ivar   { font-size: 10px; font-weight: 600; color: var(--pos-accent);  margin-bottom: 3px; }
.pos-istock { font-size: 10px; font-weight: 600; color: var(--pos-text-4); margin-bottom: 8px; }
.pos-istock.warn { color: var(--pos-warn); }
.pos-istock.none { color: var(--pos-err); }

/* ── Add / qty controls inline on card ── */
.pos-card-actions {
    width: 100%;
    margin-top: auto;
}

/* "Add" button — shown when item NOT in cart */
.pos-add-btn {
    width: 100%;
    padding: 6px 0;
    border-radius: 6px;
    border: 1.5px solid var(--pos-accent);
    background: transparent;
    color: var(--pos-accent);
    font-size: 11px;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    cursor: pointer;
    letter-spacing: 0.04em;
    transition: background 0.12s, color 0.12s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.pos-add-btn:hover {
    background: var(--pos-accent);
    color: #fff;
}
.pos-add-btn svg { width: 12px; height: 12px; }

/* Inline qty control — shown when item IS in cart */
.pos-card-qty {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background: var(--pos-accent);
    border-radius: 6px;
    overflow: hidden;
}
.pos-card-qbtn {
    width: 28px;
    height: 28px;
    border: none;
    background: transparent;
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.1s;
    flex-shrink: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.pos-card-qbtn:hover { background: rgba(0,0,0,0.18); }
.pos-card-qnum {
    font-size: 13px;
    font-weight: 800;
    color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    flex: 1;
    text-align: center;
}

/* Quantity badge on card */
.pos-badge {
    position: absolute;
    top: -7px;
    right: -7px;
    background: var(--pos-accent);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--pos-bg);
    padding: 0 4px;
}

/* Mobile FAB */
.pos-cart-fab {
    display: none;
}

/* ══════════════════════════════
   RIGHT: full-height order panel (desktop)
══════════════════════════════ */
.pos-panel {
    background: var(--pos-base);
    display: flex;
    flex-direction: column;
    border-left: 1.5px solid var(--pos-border);
    height: 100%;
}

/* Panel header */
.pos-phead {
    background: var(--pos-header);
    padding: 12px 16px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.pos-ptitle {
    font-size: 13px;
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}
.pos-pcashier {
    font-size: 10px;
    color: rgba(255,255,255,0.5);
    font-weight: 600;
    margin-top: 2px;
}
.pos-phead-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.pos-pclose {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    color: rgba(255,255,255,0.7);
    padding: 4px;
    line-height: 0;
    border-radius: 4px;
    transition: color 0.1s;
}
.pos-pclose:hover { color: #fff; }
.pos-ordno {
    font-size: 11px;
    font-weight: 700;
    color: #fbbf24;
    background: rgba(251,191,36,0.15);
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid rgba(251,191,36,0.4);
}

/* Docket store header */
.pos-docket-head {
    padding: 10px 16px 8px;
    border-bottom: 1px dashed var(--pos-border);
    background: var(--pos-base-tint);
    flex-shrink: 0;
    text-align: center;
}
.pos-store {
    font-size: 12px;
    font-weight: 800;
    color: var(--pos-text);
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.pos-txndate {
    font-size: 10px;
    color: var(--pos-text-4);
    font-weight: 600;
    margin-top: 2px;
    font-family: monospace;
}

/* Column headers */
.pos-colhead {
    display: flex;
    padding: 5px 16px;
    border-bottom: 1px solid var(--pos-border);
    background: var(--pos-sunken);
    flex-shrink: 0;
}
.pos-colhead span {
    font-size: 10px;
    font-weight: 700;
    color: var(--pos-text-4);
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.pos-colhead .ci { flex: 1; }
.pos-colhead .cq { width: 72px; text-align: center; }
.pos-colhead .cd { width: 22px; }

/* Cart list */
.pos-pitems {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}
.pos-pitems::-webkit-scrollbar { width: 3px; }
.pos-pitems::-webkit-scrollbar-thumb { background: var(--pos-border); border-radius: 4px; }

/* Empty state */
.pos-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    height: 100%;
    padding: 32px 16px;
}
.pos-empty svg { width: 36px; height: 36px; color: var(--pos-border-md); }
.pos-empty p { font-size: 12px; font-weight: 600; color: var(--pos-text-4); text-align: center; }

/* Cart row */
.pos-crow {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    gap: 6px;
    border-bottom: 1px solid var(--pos-border);
}
.pos-crow:last-child { border-bottom: none; }
.pos-crow:hover { background: var(--pos-sunken); }

.pos-cinfo { flex: 1; min-width: 0; }
.pos-cname { font-size: 12px; font-weight: 700; color: var(--pos-text);   white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pos-cvar  { font-size: 10px; color: var(--pos-text-3); font-weight: 600; margin-top: 1px; }

/* Qty controls */
.pos-qctrl {
    display: flex;
    align-items: center;
    gap: 4px;
    width: 72px;
    justify-content: center;
    flex-shrink: 0;
}
.pos-qbtn {
    width: 22px;
    height: 22px;
    border-radius: 5px;
    border: 1px solid var(--pos-border);
    background: var(--pos-sunken);
    color: var(--pos-text-2);
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
    line-height: 1;
    flex-shrink: 0;
    transition: all 0.1s;
}
.pos-qbtn:hover { background: var(--pos-accent); color: #fff; border-color: var(--pos-accent); }
.pos-qnum { width: 20px; text-align: center; font-size: 13px; font-weight: 800; color: var(--pos-text); }

/* Delete row button */
.pos-delbtn {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: none;
    background: none;
    color: var(--pos-border-md);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 0;
    transition: color 0.1s;
}
.pos-delbtn:hover { color: var(--pos-err); }

/* Totals block */
.pos-totals {
    border-top: 1px dashed var(--pos-border);
    padding: 10px 16px;
    flex-shrink: 0;
    background: var(--pos-base-tint);
}
.pos-trow { display: flex; justify-content: space-between; align-items: center; padding: 2px 0; }
.pos-tlbl { font-size: 11px; color: var(--pos-text-3); font-weight: 600; }
.pos-tval { font-size: 11px; color: var(--pos-text-2); font-weight: 700; font-family: monospace; }
.pos-trow.grand { border-top: 1px solid var(--pos-border); margin-top: 6px; padding-top: 8px; }
.pos-trow.grand .pos-tlbl { font-size: 14px; font-weight: 800; color: var(--pos-text); }
.pos-trow.grand .pos-tval { font-size: 15px; font-weight: 800; color: var(--pos-accent); font-family: monospace; }

/* Footer form */
.pos-pfoot {
    border-top: 1px solid var(--pos-border);
    padding: 11px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
    background: var(--pos-base);
}
.pos-flabel {
    font-size: 10px;
    font-weight: 700;
    color: var(--pos-text-3);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    display: block;
    margin-bottom: 4px;
}
.pos-finput {
    width: 100%;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Plus Jakarta Sans', sans-serif;
    border: 1.5px solid var(--pos-border);
    border-radius: 6px;
    padding: 8px 10px;
    background: var(--pos-sunken);
    color: var(--pos-text);
    outline: none;
    transition: border-color 0.12s;
    box-sizing: border-box;
}
.pos-finput:focus { border-color: var(--pos-accent); background: var(--pos-base); }
.pos-finput::placeholder { color: var(--pos-text-4); font-weight: 500; }
.pos-ftarea { resize: none; height: 46px; }
.pos-ferr { font-size: 10px; font-weight: 700; color: var(--pos-err); margin-top: 3px; }

/* Action buttons */
.pos-actions {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 6px;
    padding: 10px 16px 12px;
    border-top: 1px solid var(--pos-border);
    flex-shrink: 0;
    background: var(--pos-base);
}
.pos-sbtn {
    padding: 12px 8px;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    cursor: pointer;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    transition: all 0.1s;
    border: none;
}
.pos-sbtn.clear {
    background: var(--pos-sunken);
    color: var(--pos-text-3);
    border: 1.5px solid var(--pos-border);
}
.pos-sbtn.clear:hover { background: var(--pos-border); color: var(--pos-text-2); }
.pos-sbtn.submit { background: var(--pos-accent); color: #fff; }
.pos-sbtn.submit:hover { background: var(--pos-accent-dk); }
.pos-sbtn.submit:disabled { opacity: 0.5; cursor: not-allowed; }

/* ══════════════════════════════
   MODAL OVERLAY — FIXED + FULL COVERAGE
══════════════════════════════ */
.pos-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    /* Ensure it sits above Filament sidebar, headers, notifications — everything */
    z-index: 99999;
    background: var(--pos-overlay, rgba(0,0,0,0.55));
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    align-items: flex-end;
    justify-content: center;
    /* Prevent clicks on background leaking through */
    pointer-events: all;
}
.pos-modal-overlay.open {
    display: flex;
    animation: posOverlayIn 0.2s ease;
}
@keyframes posOverlayIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.pos-modal-sheet {
    background: var(--pos-base);
    width: 100%;
    max-width: 520px;
    border-radius: 16px 16px 0 0;
    display: flex;
    flex-direction: column;
    max-height: 92vh;
    overflow: hidden;
    box-shadow: 0 -8px 40px rgba(0,0,0,0.3);
    animation: posSlideUp 0.25s ease;
}
@keyframes posSlideUp {
    from { transform: translateY(40px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

/* Drag handle pill */
.pos-drag-handle {
    width: 36px;
    height: 4px;
    background: var(--pos-border-md);
    border-radius: 2px;
    margin: 10px auto 6px;
    flex-shrink: 0;
}

/* Always show close button inside modal sheet */
.pos-modal-sheet .pos-pclose {
    display: flex !important;
}

/* ══════════════════════════════
   RESPONSIVE BREAKPOINTS
══════════════════════════════ */
@media (max-width: 768px) {
    .pos-wrap {
        grid-template-columns: 1fr;
        height: calc(100vh - 80px);
        border-radius: 8px;
    }
    .pos-panel { display: none; }

    .pos-cart-fab {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--pos-accent);
        color: #fff;
        padding: 13px 18px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.04em;
        cursor: pointer;
        border: none;
        flex-shrink: 0;
        width: 100%;
        transition: background 0.1s;
    }
    .pos-cart-fab:hover  { background: var(--pos-accent-dk); }
    .pos-cart-fab:active { background: #0a44b0; }

    .pos-fab-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pos-fab-badge {
        background: #fff;
        color: var(--pos-accent);
        font-size: 11px;
        font-weight: 800;
        min-width: 22px;
        height: 22px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
    }
    .pos-fab-arrow { display: flex; align-items: center; }

    .pos-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media (max-width: 480px) {
    .pos-grid {
        grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
    }
    .pos-catbtn {
        padding: 11px 14px;
        font-size: 11px;
    }
}
</style>

<script>
/* ── Live clock ── */
function updatePosDate() {
    ['pos-live-date', 'pos-live-date-modal'].forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = new Date().toLocaleString('en-PH', {
            timeZone: 'Asia/Manila',
            month: 'short', day: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true,
        });
    });
}
updatePosDate();
setInterval(updatePosDate, 1000);

/* ── Category scroll arrows ── */
function initCatArrows() {
    const bar    = document.getElementById('pos-catbar');
    const arrowL = document.getElementById('pos-arrow-left');
    const arrowR = document.getElementById('pos-arrow-right');
    if (!bar || !arrowL || !arrowR) return;

    function update() {
        const atStart = bar.scrollLeft <= 4;
        const atEnd   = bar.scrollLeft + bar.clientWidth >= bar.scrollWidth - 4;
        arrowL.style.opacity       = atStart ? '0' : '1';
        arrowL.style.pointerEvents = atStart ? 'none' : 'auto';
        arrowR.style.opacity       = atEnd ? '0' : '1';
        arrowR.style.pointerEvents = atEnd ? 'none' : 'auto';
    }

    bar.addEventListener('scroll', update);
    window.addEventListener('resize', update);
    update();
}

document.addEventListener('DOMContentLoaded', initCatArrows);
document.addEventListener('livewire:navigated', initCatArrows);

/* ── Mobile modal ── */
function openPosModal() {
    const overlay = document.getElementById('pos-modal-overlay');
    if (overlay) {
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}
function closePosModal() {
    const overlay = document.getElementById('pos-modal-overlay');
    if (overlay) {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('pos-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePosModal();
        });
    }
});

document.addEventListener('livewire:dispatch', function (e) {
    if (e.detail && (e.detail.name === 'cart-submitted' || e.detail.name === 'notify')) {
        closePosModal();
    }
});
</script>

{{-- ══════════════════════════════════════════
     MOBILE BOTTOM-SHEET MODAL
     z-index: 99999 — sits above everything
══════════════════════════════════════════ --}}
<div id="pos-modal-overlay" class="pos-modal-overlay">
    <div class="pos-modal-sheet">

        <div class="pos-drag-handle"></div>

        <div class="pos-phead">
            <div>
                <p class="pos-ptitle">Order summary</p>
                <p class="pos-pcashier">Cashier: {{ auth()->user()->name ?? 'Staff' }}</p>
            </div>
            <div class="pos-phead-right">
                <span class="pos-ordno">{{ \App\Models\OfficeSupplyRequest::generateRequestNumber() }}</span>
                <button class="pos-pclose" onclick="closePosModal()" aria-label="Close">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="pos-docket-head">
            <p class="pos-store">Supply Station</p>
            <p class="pos-txndate" id="pos-live-date-modal"></p>
        </div>

        <div class="pos-colhead">
            <span class="ci">Item</span>
            <span class="cq">Qty</span>
            <span class="cd"></span>
        </div>

        <div class="pos-pitems">
            @if(empty($cart))
                <div class="pos-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <p>Tap items on the left<br>to add them here</p>
                </div>
            @else
                @foreach($cart as $key => $entry)
                    <div class="pos-crow">
                        <div class="pos-cinfo">
                            <p class="pos-cname">{{ $entry['name'] }}</p>
                            @if(!empty($entry['variant']))
                                <p class="pos-cvar">{{ $entry['variant'] }}</p>
                            @endif
                        </div>
                        <div class="pos-qctrl">
                            <button wire:click="changeQty('{{ $key }}', -1)" class="pos-qbtn">−</button>
                            <span class="pos-qnum">{{ $entry['qty'] }}</span>
                            <button wire:click="changeQty('{{ $key }}', 1)" class="pos-qbtn">+</button>
                        </div>
                        <button wire:click="removeItem('{{ $key }}')" class="pos-delbtn">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>

        @if(!empty($cart))
            @php $totalQtyModal = array_sum(array_column($cart, 'qty')); @endphp
            <div class="pos-totals">
                <div class="pos-trow">
                    <span class="pos-tlbl">Line items</span>
                    <span class="pos-tval">{{ count($cart) }}</span>
                </div>
                <div class="pos-trow">
                    <span class="pos-tlbl">Total qty</span>
                    <span class="pos-tval">{{ $totalQtyModal }}</span>
                </div>
                <div class="pos-trow grand">
                    <span class="pos-tlbl">TOTAL</span>
                    <span class="pos-tval">{{ $totalQtyModal }} pcs</span>
                </div>
            </div>
        @endif

        <div class="pos-pfoot">
            <div>
                <label class="pos-flabel">
                    Requested by <span style="color:#ef4444">*</span>
                </label>
                <input
                    wire:model="requestedBy"
                    type="text"
                    class="pos-finput"
                    placeholder="Full name">
                @error('requestedBy')
                    <p class="pos-ferr">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="pos-flabel">
                    Note <span style="color:#9ca3af;font-weight:500;text-transform:none;letter-spacing:0">(optional)</span>
                </label>
                <textarea
                    wire:model="note"
                    class="pos-finput pos-ftarea"
                    placeholder="Additional details..."></textarea>
            </div>
            @error('cart')
                <p class="pos-ferr">{{ $message }}</p>
            @enderror
        </div>

        <div class="pos-actions">
            <button wire:click="clearCart" class="pos-sbtn clear">Clear</button>
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="pos-sbtn submit">
                <span wire:loading.remove wire:target="submit">Submit Request</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════
     MAIN POS LAYOUT
══════════════════════════════════════════ --}}
<div class="pos-wrap">

    {{-- ───── LEFT: Catalog ───── --}}
    <div class="pos-left">

        <div class="pos-catbar-wrap">

            <button
                id="pos-arrow-left"
                class="pos-catarrow left"
                style="opacity:0;pointer-events:none;"
                onclick="document.getElementById('pos-catbar').scrollBy({left:-150,behavior:'smooth'})"
                tabindex="-1"
                aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24" stroke="#ffffff" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <div id="pos-catbar" class="pos-catbar">
                @foreach($this->categories as $category)
                    <button
                        wire:click="setCategory('{{ $category['id'] }}')"
                        class="pos-catbtn {{ $activeCategory == $category['id'] ? 'active' : '' }}">
                        {{ $category['office_supply_category_name'] }}
                    </button>
                @endforeach
            </div>

            <button
                id="pos-arrow-right"
                class="pos-catarrow right"
                style="opacity:1;"
                onclick="document.getElementById('pos-catbar').scrollBy({left:150,behavior:'smooth'})"
                tabindex="-1"
                aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24" stroke="#ffffff" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

        </div>

        <div class="pos-toolbar">
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                class="pos-search"
                placeholder="Search items...">
        </div>

        @foreach($this->categories as $category)
            @if($activeCategory == $category['id'])
                <p class="pos-seclabel">{{ $category['office_supply_category_name'] }}</p>

                <div class="pos-grid-wrap">
                    <div class="pos-grid">
                        @forelse($category['items'] as $item)
                            @foreach($item['variants'] as $variant)
                                @php
                                    $key        = $item['id'] . '_' . $variant['id'];
                                    $inCart     = isset($cart[$key]);
                                    $stock      = $variant['office_supply_quantity'];
                                    $stockClass = $stock === 0 ? 'none' : ($stock <= 5 ? 'warn' : '');
                                @endphp

                                {{-- Card is now a div — no accidental tap-to-add --}}
                                <div class="pos-icard {{ $inCart ? 'sel' : '' }} {{ $stock <= 0 ? 'oos' : '' }}">

                                    @if($inCart)
                                        <div class="pos-badge">{{ $cart[$key]['qty'] }}</div>
                                    @endif

                                    <div class="pos-icon">
                                        @if(!empty($item['office_supply_image']))
                                            <img
                                                src="{{ Storage::url($item['office_supply_image']) }}"
                                                alt="{{ $item['office_supply_name'] }}"
                                                style="width:46px;height:46px;object-fit:cover;">
                                        @else
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="22" height="22">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <p class="pos-iname">{{ $item['office_supply_name'] }}</p>
                                    <p class="pos-ivar">{{ $variant['office_supply_variant'] }}</p>
                                    <p class="pos-istock {{ $stockClass }}">
                                        {{ $stock <= 0 ? 'Out of stock' : 'Stock: ' . $stock }}
                                    </p>

                                    {{-- ── Action area: Add button OR inline qty controls ── --}}
                                    <div class="pos-card-actions">
                                        @if(!$inCart)
                                            {{-- ADD button --}}
                                            <button
                                                wire:click="addItem({{ $item['id'] }}, {{ $variant['id'] }}, '{{ addslashes($item['office_supply_name']) }}', '{{ addslashes($variant['office_supply_variant']) }}')"
                                                class="pos-add-btn"
                                                @disabled($stock <= 0)>
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                                </svg>
                                                Add
                                            </button>
                                        @else
                                            {{-- Inline qty stepper once item is in cart --}}
                                            <div class="pos-card-qty">
                                                <button
                                                    wire:click="changeQty('{{ $key }}', -1)"
                                                    class="pos-card-qbtn"
                                                    title="Decrease">−</button>
                                                <span class="pos-card-qnum">{{ $cart[$key]['qty'] }}</span>
                                                <button
                                                    wire:click="changeQty('{{ $key }}', 1)"
                                                    class="pos-card-qbtn"
                                                    title="Increase">+</button>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        @empty
                            <div style="grid-column:1/-1;text-align:center;padding:3rem;font-size:12px;font-weight:600;color:var(--pos-text-4)">
                                No items in this category.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        @endforeach

        @php $fabQty = !empty($cart) ? array_sum(array_column($cart, 'qty')) : 0; @endphp
        <button class="pos-cart-fab" onclick="openPosModal()">
            <span class="pos-fab-left">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                </svg>
                View Order Summary
                @if($fabQty > 0)
                    <span class="pos-fab-badge">{{ $fabQty }}</span>
                @endif
            </span>
            <span class="pos-fab-arrow">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                </svg>
            </span>
        </button>

    </div>

    {{-- ───── RIGHT: Full-height order panel (desktop only) ───── --}}
    <div class="pos-panel">

        <div class="pos-phead">
            <div>
                <p class="pos-ptitle">Order summary</p>
                <p class="pos-pcashier">Cashier: {{ auth()->user()->name ?? 'Staff' }}</p>
            </div>
            <div class="pos-phead-right">
                <span class="pos-ordno">{{ \App\Models\OfficeSupplyRequest::generateRequestNumber() }}</span>
            </div>
        </div>

        <div class="pos-docket-head">
            <p class="pos-store">Supply Station</p>
            <p class="pos-txndate" id="pos-live-date"></p>
        </div>

        <div class="pos-colhead">
            <span class="ci">Item</span>
            <span class="cq">Qty</span>
            <span class="cd"></span>
        </div>

        <div class="pos-pitems">
            @if(empty($cart))
                <div class="pos-empty">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" width="36" height="36">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <p>Click <strong>Add</strong> on any item<br>to add it here</p>
                </div>
            @else
                @foreach($cart as $key => $entry)
                    <div class="pos-crow">
                        <div class="pos-cinfo">
                            <p class="pos-cname">{{ $entry['name'] }}</p>
                            @if(!empty($entry['variant']))
                                <p class="pos-cvar">{{ $entry['variant'] }}</p>
                            @endif
                        </div>
                        <div class="pos-qctrl">
                            <button wire:click="changeQty('{{ $key }}', -1)" class="pos-qbtn">−</button>
                            <span class="pos-qnum">{{ $entry['qty'] }}</span>
                            <button wire:click="changeQty('{{ $key }}', 1)" class="pos-qbtn">+</button>
                        </div>
                        <button wire:click="removeItem('{{ $key }}')" class="pos-delbtn">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" width="12" height="12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>

        @if(!empty($cart))
            @php $totalQty = array_sum(array_column($cart, 'qty')); @endphp
            <div class="pos-totals">
                <div class="pos-trow">
                    <span class="pos-tlbl">Line items</span>
                    <span class="pos-tval">{{ count($cart) }}</span>
                </div>
                <div class="pos-trow">
                    <span class="pos-tlbl">Total qty</span>
                    <span class="pos-tval">{{ $totalQty }}</span>
                </div>
                <div class="pos-trow grand">
                    <span class="pos-tlbl">TOTAL</span>
                    <span class="pos-tval">{{ $totalQty }} pcs</span>
                </div>
            </div>
        @endif

        <div class="pos-pfoot">
            <div>
                <label class="pos-flabel">
                    Requested by <span style="color:#ef4444">*</span>
                </label>
                <input
                    wire:model="requestedBy"
                    type="text"
                    class="pos-finput"
                    placeholder="Full name">
                @error('requestedBy')
                    <p class="pos-ferr">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="pos-flabel">
                    Note <span style="color:#9ca3af;font-weight:500;text-transform:none;letter-spacing:0">(optional)</span>
                </label>
                <textarea
                    wire:model="note"
                    class="pos-finput pos-ftarea"
                    placeholder="Additional details..."></textarea>
            </div>
            @error('cart')
                <p class="pos-ferr">{{ $message }}</p>
            @enderror
        </div>

        <div class="pos-actions">
            <button wire:click="clearCart" class="pos-sbtn clear">Clear</button>
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                class="pos-sbtn submit">
                <span wire:loading.remove wire:target="submit">Submit Request</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </button>
        </div>

    </div>

</div>
</x-filament-panels::page>