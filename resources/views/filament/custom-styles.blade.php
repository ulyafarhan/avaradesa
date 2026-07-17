<style>
    /* ===== AvaraDesa Admin — Premium Filament Styles ===== */

    html { scroll-behavior: smooth; }

    /* ── Sidebar Glassmorphism ── */
    .fi-sidebar {
        background-color: rgba(255, 255, 255, 0.97) !important;
        backdrop-filter: blur(20px) saturate(1.8) !important;
        border-right: 1px solid rgba(226, 232, 240, 0.6) !important;
    }

    .dark .fi-sidebar {
        background-color: rgba(15, 23, 42, 0.97) !important;
        border-right: 1px solid rgba(51, 65, 85, 0.6) !important;
    }

    /* ── Logo ── */
    .fi-sidebar-header .fi-logo {
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
    }

    body:not(.fi-sidebar-open) .fi-sidebar-header .fi-logo {
        justify-content: center !important;
    }

    /* ── Sidebar Nav Items ── */
    .fi-sidebar-item-button {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-radius: 0.625rem !important;
        margin: 2px 0.625rem !important;
        padding: 0.5rem 0.75rem !important;
    }

    .fi-sidebar-item-button:hover {
        background-color: rgba(20, 184, 166, 0.06) !important;
    }

    .dark .fi-sidebar-item-button:hover {
        background-color: rgba(20, 184, 166, 0.1) !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-button,
    .fi-sidebar-item-button[aria-current="page"] {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.12), rgba(20, 184, 166, 0.04)) !important;
        border-left: 3px solid #0D9488 !important;
        font-weight: 600 !important;
    }

    .dark .fi-sidebar-item-active .fi-sidebar-item-button,
    .dark .fi-sidebar-item-button[aria-current="page"] {
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.15), rgba(13, 148, 136, 0.05)) !important;
        border-left: 3px solid #2DD4BF !important;
    }

    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: #0D9488 !important;
    }

    .dark .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: #2DD4BF !important;
    }

    /* Force show resource icons (not bullet dots) inside nav groups and enforce correct sizing */
    .fi-sidebar-item-icon {
        display: inline-flex !important;
        width: 1.25rem !important;
        height: 1.25rem !important;
    }

    .fi-sidebar-group-icon {
        width: 1.25rem !important;
        height: 1.25rem !important;
    }

    .fi-sidebar-item-grouped-border {
        display: none !important;
    }

    /* ── Topbar Glassmorphism ── */
    .fi-topbar {
        background-color: rgba(255, 255, 255, 0.92) !important;
        backdrop-filter: blur(16px) saturate(1.5) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
    }

    .dark .fi-topbar {
        background-color: rgba(15, 23, 42, 0.92) !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.6) !important;
    }

    /* ── Stats Widget Cards ── */
    .fi-wi-stats-overview-stat {
        position: relative !important;
        overflow: hidden !important;
        background: #ffffff !important;
        border-radius: 1rem !important;
        border: 1px solid rgba(226, 232, 240, 0.7) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.2s ease !important;
    }

    .fi-wi-stats-overview-stat:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
        border-color: rgba(13, 148, 136, 0.25) !important;
    }

    .dark .fi-wi-stats-overview-stat {
        background: #1e293b !important;
        border: 1px solid rgba(51, 65, 85, 0.5) !important;
    }

    .dark .fi-wi-stats-overview-stat:hover {
        border-color: rgba(45, 212, 191, 0.3) !important;
    }

    /* ── Compact & Single-Row Table Headers (Filters on Left, New Button on Right) ── */
    .fi-ta-header-ctn {
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .dark .fi-ta-header-ctn {
        border-bottom: 1px solid rgba(51, 65, 85, 0.8) !important;
    }

    @media (min-width: 640px) {
        .fi-ta-header-ctn {
            display: flex !important;
            flex-direction: row-reverse !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0.625rem 1rem !important;
            gap: 1rem !important;
        }

        .fi-ta-header {
            padding: 0 !important;
            border-bottom: none !important;
            background: transparent !important;
        }

        .fi-ta-header-toolbar {
            padding: 0 !important;
            border-top: none !important;
            border-bottom: none !important;
            background: transparent !important;
            flex-grow: 0 !important;
            width: auto !important;
            justify-content: flex-start !important;
        }

        /* Align table toolbar contents (e.g. filters trigger) to the left side */
        .fi-ta-header-toolbar > div {
            margin-left: 0 !important;
            margin-right: auto !important;
            justify-content: flex-start !important;
            flex-grow: 0 !important;
            width: auto !important;
        }

        /* Reset Filament's default auto margins that push controls to the right */
        .fi-ta-header-toolbar > div > div {
            margin-inline-start: 0 !important;
            margin-left: 0 !important;
            margin-right: auto !important;
        }
    }

    /* ── Table Styling ── */
    .fi-ta-header-cell,
    .fi-ta-actions-header-cell,
    .fi-ta-table th {
        background-color: rgba(248, 250, 252, 0.9) !important;
        text-transform: uppercase !important;
        font-size: 0.7rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.06em !important;
        color: #64748b !important;
        border-bottom: 2px solid rgba(226, 232, 240, 0.8) !important;
    }

    .dark .fi-ta-header-cell,
    .dark .fi-ta-actions-header-cell,
    .dark .fi-ta-table th {
        background-color: rgba(15, 23, 42, 0.9) !important;
        color: #94a3b8 !important;
        border-bottom: 2px solid rgba(51, 65, 85, 0.8) !important;
    }

    .fi-ta-row {
        transition: background-color 0.15s ease !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(248, 250, 252, 0.6) !important;
    }

    .dark .fi-ta-row:hover {
        background-color: rgba(30, 41, 59, 0.5) !important;
    }

    .fi-ta-content {
        overflow-x: auto !important;
    }

    /* ── Form Inputs Focus Ring ── */
    .fi-input:focus,
    .fi-fo-component input:focus,
    .fi-fo-component select:focus,
    .fi-fo-component textarea:focus {
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.08) !important;
        transition: all 0.15s ease !important;
    }

    /* ── Section Cards ── */
    .fi-section {
        border-radius: 1rem !important;
        overflow: hidden !important;
    }

    .fi-section-header {
        padding: 1rem 1.25rem !important;
    }

    /* ── Buttons ── */
    .fi-btn {
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-radius: 0.5rem !important;
    }

    .fi-btn:active {
        transform: scale(0.98) !important;
    }

    /* ── Modal & Slide-over ── */
    .fi-modal-window {
        border-radius: 1.25rem !important;
    }

    /* ── Badge polish ── */
    .fi-badge {
        font-weight: 600 !important;
        letter-spacing: 0.02em !important;
    }

    /* ── Responsive: Mobile improvements ── */
    @media (max-width: 768px) {
        .fi-page-header {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .fi-ta-table {
            font-size: 0.8rem !important;
        }

        .fi-wi-stats-overview-stat {
            border-radius: 0.75rem !important;
        }
    }

    /* ── Scrollbar: subtle, only on sidebar ── */
    .fi-sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 4px;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }

    .fi-sidebar-nav {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
    }

    /* ── Page transition ── */
    .fi-page {
        animation: fadeInPage 0.2s ease-out;
    }

    @keyframes fadeInPage {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
