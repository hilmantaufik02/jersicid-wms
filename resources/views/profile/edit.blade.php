<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2 border border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Edit Profil') }}
                </h2>
                <p class="text-sm text-slate-400 mt-1">Perbarui informasi profil Anda</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-900/30 border border-emerald-500/50 text-emerald-400 rounded-xl flex items-start gap-3 animate-pulse">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="p-4 bg-red-900/30 border border-red-500/50 rounded-xl">
                    <ul class="text-sm text-red-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- ==================== KOLOM KIRI: FOTO PROFIL ==================== -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Foto Profil
                    </h3>

                    <div class="flex flex-col items-center">
                        <!-- Avatar Display -->
                        <div class="relative mb-4">
                            <img 
                                src="{{ $user->profile_photo_url }}" 
                                alt="{{ $user->name }}"
                                class="w-32 h-32 rounded-full object-cover border-4 border-cyan-500/30 shadow-lg shadow-cyan-500/10"
                                id="profile-preview"
                            />
                            <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-cyan-500 rounded-full flex items-center justify-center border-4 border-slate-900">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Nama & Email -->
                        <p class="text-base font-semibold text-white mb-1">{{ $user->name }}</p>
                        <p class="text-sm text-slate-400 mb-6">{{ $user->email }}</p>

                        <!-- Upload Form -->
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="w-full space-y-3">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Foto Baru</label>
                                <input 
                                    type="file" 
                                    name="photo" 
                                    id="photo-input"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                    onchange="previewImage(this)"
                                    class="block w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-500/20 file:text-cyan-400 hover:file:bg-cyan-500/30 file:cursor-pointer file:transition-all border border-slate-700 rounded-lg bg-slate-800"
                                />
                                @error('photo')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB.</p>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-slate-900 font-bold rounded-xl shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Foto
                            </button>
                        </form>

                        <!-- Hapus Foto -->
                        @if($user->photo)
                            <form method="POST" action="{{ route('profile.photo.delete') }}" class="w-full mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus foto profil?')" class="w-full py-2.5 bg-red-600/20 hover:bg-red-600/30 text-red-400 text-sm font-semibold rounded-xl transition-all border border-red-500/30 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- ==================== KOLOM KANAN: INFORMASI PROFIL ==================== -->
                <div class="space-y-6">
                    
                    <!-- Form Informasi Profil -->
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Informasi Profil
                        </h3>
                        
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"
                                    placeholder="Masukkan nama lengkap"
                                />
                                @error('name')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"
                                    placeholder="nama@email.com"
                                />
                                @error('email')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Info Role & Workspace (Read-only) -->
                            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-800/50 rounded-xl border border-slate-700/50">
                                <div>
                                    <p class="text-xs text-slate-400 mb-1">Role</p>
                                    <p class="text-sm font-semibold text-cyan-400">{{ $user->role }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 mb-1">Workspace</p>
                                    <p class="text-sm font-semibold text-cyan-400">{{ $user->workspace_default }}</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-slate-900 font-bold rounded-xl shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>

                    <!-- Form Ubah Password -->
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                        <h3 class="text-lg font-semibold text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Ubah Password
                        </h3>
                        
                        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Password Saat Ini</label>
                                <input 
                                    type="password" 
                                    name="current_password"
                                    required
                                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"
                                    placeholder="Masukkan password saat ini"
                                />
                                @error('current_password')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Password Baru</label>
                                    <input 
                                        type="password" 
                                        name="password"
                                        required
                                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"
                                        placeholder="Minimal 12 karakter"
                                    />
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password</label>
                                    <input 
                                        type="password" 
                                        name="password_confirmation"
                                        required
                                        class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all"
                                        placeholder="Ulangi password baru"
                                    />
                                </div>
                            </div>

                            <!-- Password Criteria -->
                            <div class="p-3 bg-slate-800/50 rounded-lg border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-2 font-medium">Kriteria Password:</p>
                                <ul class="text-xs text-slate-500 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Minimal 12 karakter
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Huruf besar (A-Z)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Huruf kecil (a-z)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Angka (0-9)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                        Simbol (@$!%*?&#)
                                    </li>
                                </ul>
                            </div>

                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-400 hover:to-cyan-500 text-slate-900 font-bold rounded-xl shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk preview image sebelum upload --}}
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</x-app-layout>