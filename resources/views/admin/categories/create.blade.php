<x-admin-layout title="Tambah Kategori">
    <x-slot name="pageTitle">Tambah Kategori Baru</x-slot>
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('categories.store') }}" method="POST" class="glass-panel p-6">
            @csrf
            <div class="mb-4">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" required class="form-input" value="{{ old('name') }}">
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('categories.index') }}" class="btn-ghost px-4 py-2 rounded-lg">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>