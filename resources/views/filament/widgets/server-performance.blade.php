<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-cpu-chip" class="fi-server-performance-widget">
        <x-slot name="heading">Status & Performa Server</x-slot>

        <div class="server-perf-container">
            <!-- Grid Indicator Performa -->
            <div class="perf-grid">
                <!-- Penyimpanan Card -->
                <div class="perf-card">
                    <div class="perf-card-header">
                        <div class="perf-card-title">
                            <span class="icon-indicator bg-blue-soft">
                                <svg class="w-5 h-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                </svg>
                            </span>
                            <div>
                                <h4 class="perf-label">Penyimpanan (Disk)</h4>
                                <p class="perf-sub">{{ $disk['used'] }} dari {{ $disk['total'] }} digunakan</p>
                            </div>
                        </div>
                        <span class="perf-percentage text-blue">{{ $disk['percentage'] }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill bg-blue-grad" style="width: {{ $disk['percentage'] }}%"></div>
                    </div>
                    <div class="perf-card-footer">
                        <span>Sisa Ruang: {{ $disk['free'] }}</span>
                    </div>
                </div>

                <!-- RAM Card -->
                <div class="perf-card">
                    <div class="perf-card-header">
                        <div class="perf-card-title">
                            <span class="icon-indicator bg-purple-soft">
                                <svg class="w-5 h-5 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </span>
                            <div>
                                <h4 class="perf-label">Memori (RAM)</h4>
                                <p class="perf-sub">{{ $ram['used'] }} dari {{ $ram['total'] }} aktif</p>
                            </div>
                        </div>
                        <span class="perf-percentage text-purple">{{ $ram['percentage'] }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill bg-purple-grad" style="width: {{ $ram['percentage'] }}%"></div>
                    </div>
                    <div class="perf-card-footer">
                        <span>Tersedia: {{ $ram['free'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Detail Spesifikasi Server -->
            <div class="sys-info-section">
                <h5 class="sys-info-title">Spesifikasi Lingkungan Server</h5>
                <div class="sys-info-grid">
                    <div class="sys-info-item">
                        <span class="sys-label">Sistem Operasi</span>
                        <span class="sys-value">{{ $system['os'] }}</span>
                    </div>
                    <div class="sys-info-item">
                        <span class="sys-label">Alamat IP Server</span>
                        <span class="sys-value">{{ $system['ip'] }}</span>
                    </div>
                    <div class="sys-info-item">
                        <span class="sys-label">Versi PHP</span>
                        <span class="sys-value">{{ $system['php'] }}</span>
                    </div>
                    <div class="sys-info-item">
                        <span class="sys-label">Versi Laravel</span>
                        <span class="sys-value">v{{ $system['laravel'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .server-perf-container {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                padding: 0.5rem 0;
            }
            .perf-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            @media (min-width: 640px) {
                .perf-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .perf-card {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 0.75rem;
                padding: 1.25rem;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .dark .perf-card {
                background: rgba(255, 255, 255, 0.02);
                border-color: rgba(255, 255, 255, 0.05);
            }
            .perf-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .perf-card-title {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .icon-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.5rem;
            }
            .bg-blue-soft {
                background: rgba(59, 130, 246, 0.15);
            }
            .bg-purple-soft {
                background: rgba(139, 92, 246, 0.15);
            }
            .text-blue {
                color: rgb(59, 130, 246);
                font-weight: 700;
            }
            .text-purple {
                color: rgb(139, 92, 246);
                font-weight: 700;
            }
            .perf-label {
                font-size: 0.95rem;
                font-weight: 600;
            }
            .perf-sub {
                font-size: 0.75rem;
                color: #888;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 140px;
            }
            .perf-percentage {
                font-size: 1.25rem;
            }
            .progress-bar-bg {
                width: 100%;
                height: 0.5rem;
                background: rgba(0, 0, 0, 0.1);
                border-radius: 9999px;
                overflow: hidden;
            }
            .progress-bar-fill {
                height: 100%;
                border-radius: 9999px;
                transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .bg-blue-grad {
                background: linear-gradient(90deg, #3b82f6, #60a5fa);
            }
            .bg-purple-grad {
                background: linear-gradient(90deg, #8b5cf6, #a78bfa);
            }
            .perf-card-footer {
                display: flex;
                justify-content: space-between;
                font-size: 0.75rem;
                color: #666;
            }
            .sys-info-section {
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                padding-top: 1.25rem;
            }
            .sys-info-title {
                font-size: 0.9rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
                color: #aaa;
            }
            .sys-info-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            @media (min-width: 640px) {
                .sys-info-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .sys-info-item {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0.75rem;
                background: rgba(0, 0, 0, 0.05);
                border-radius: 0.375rem;
                font-size: 0.8rem;
            }
            .sys-label {
                color: #888;
            }
            .sys-value {
                font-weight: 500;
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
