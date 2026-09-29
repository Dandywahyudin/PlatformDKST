@php
    $isDirectorView = (auth()->user()?->isDirector() && !auth()->user()?->isAdmin()) || request()->routeIs('director.*');
@endphp

<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl border-r border-slate-800 lg:static lg:inset-auto">
    
    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-6 bg-slate-950 border-b border-slate-800">
        <a href="{{ $isDirectorView ? route('director.dashboard') : route('admin.dashboard') }}" class="flex items-center space-x-3 group">
            <img src="{{ asset('storage/IIP-Logo.png') }}" alt="Logo IIP DKST ITB" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform">
            <div>
                <span class="text-base font-bold text-white tracking-wide block leading-none">DKST ITB</span>
                <span class="text-[10px] text-blue-400 font-medium tracking-wider uppercase block mt-0.5">
                    {{ $isDirectorView ? 'Executive Portal' : 'Admin Portal' }}
                </span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6 custom-scrollbar">
        
        <!-- Main Navigation -->
        <div>
            <p class="px-3 text-[11px] font-semibold tracking-wider text-slate-500 uppercase mb-2">Utama</p>
            <nav class="space-y-1">
                @if($isDirectorView)
                    <a href="{{ route('director.dashboard') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('director.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard Direktur</span>
                    </a>
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 transition-colors">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            <span>Ke Dashboard Admin</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span>Dashboard Admin</span>
                    </a>
                    <a href="{{ route('director.dashboard') }}" 
                       class="flex items-center px-3 py-2 rounded-xl text-xs font-medium text-blue-400 hover:text-blue-300 hover:bg-blue-950/40 transition-colors">
                        <svg class="w-4 h-4 mr-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Dashboard Direktur</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Program & Monitoring Evaluasi -->
        <div>
            <p class="px-3 text-[11px] font-semibold tracking-wider text-slate-500 uppercase mb-2">Program & Monev</p>
            <nav class="space-y-1">
                <!-- Semua Program -->
                <a href="{{ route('admin.programs.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.programs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002 2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span>{{ $isDirectorView ? 'Semua Program' : 'Pengelolaan Program' }}</span>
                    </div>
                </a>

                @if(!$isDirectorView)
                    <!-- Persetujuan (Approval) -->
                    <a href="{{ route('admin.approvals.index') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.approvals.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Persetujuan (Approval)</span>
                        </div>
                    </a>
                @endif

                <!-- Monitoring & Evaluasi -->
                <a href="{{ route('admin.monev.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.monev.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>{{ $isDirectorView ? 'Monitoring & Evaluasi' : 'Monitoring & Evaluasi' }}</span>
                    </div>
                </a>

                <!-- Kinerja & Dampak -->
                <a href="{{ route('admin.impact.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.impact.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>Kinerja & Dampak</span>
                    </div>
                </a>

                <!-- Layanan & Konsultasi -->
                <a href="{{ route('admin.services.index') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.services.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                        <span>Layanan & Konsultasi</span>
                    </div>
                </a>

                @if(!$isDirectorView)
                    <!-- Task & Penugasan -->
                    <a href="{{ route('admin.tasks.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.tasks.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Task & Penugasan</span>
                    </a>

                    <!-- Dokumen & Arsip -->
                    <a href="{{ route('admin.documents.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <span>Dokumen & Arsip</span>
                    </a>
                @endif
            </nav>
        </div>

        @if($isDirectorView)
            <!-- Reports & Account Links for Director -->
            <div>
                <p class="px-3 text-[11px] font-semibold tracking-wider text-slate-500 uppercase mb-2">Laporan & Akun</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.impact.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.impact.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Laporan Eksekutif (Reports)</span>
                    </a>

                    <a href="{{ route('admin.notifications.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-400 hover:text-slate-100 hover:bg-slate-800/60">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span>Notifikasi (Notifications)</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-400 hover:text-slate-100 hover:bg-slate-800/60">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profil Saya (Profile)</span>
                    </a>
                </nav>
            </div>
        @else
            <!-- Administrasi & Akses for Admin -->
            <div>
                <p class="px-3 text-[11px] font-semibold tracking-wider text-slate-500 uppercase mb-2">Administrasi & Akses</p>
                <nav class="space-y-1">
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Pengguna</span>
                    </a>

                    <a href="{{ route('admin.roles.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.roles.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Role & Hak Akses</span>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Audit Log</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30 font-semibold' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Pengaturan Sistem</span>
                    </a>
                </nav>
            </div>
        @endif
    </div>

    <!-- User Profile Strip (Bottom) -->
    <div class="p-4 bg-slate-950 border-t border-slate-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                <p class="text-xs text-slate-400 truncate">
                    {{ $isDirectorView ? 'Direktur DKST' : (Auth::user()->position ?? 'Super Admin') }}
                </p>
            </div>
        </div>
    </div>
</aside>
