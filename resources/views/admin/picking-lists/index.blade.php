<x-admin-layout title="Picking List">
    <x-slot name="pageTitle">Manajemen Picking List</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Picking List</h1>
            <p class="text-gray-400 text-sm mt-1">Daftar tugas pengambilan barang di gudang</p>
        </div>
        <a href="{{ route('picking-lists.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Picking List
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">ID Referensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Gudang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Staff</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Prioritas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse($pickingLists as $pl)
                <tr class="hover:bg-gray-700/50 transition">
                    <td class="px-6 py-4 text-sm text-white font-mono">{{ $pl->reference_type }}-{{ $pl->reference_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ $pl->warehouse->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-300">{{ $pl->assignedTo->name ?? 'Belum ditugaskan' }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                {{ $pl->priority === 'urgent' ? 'bg-red-900 text-red-200' : ($pl->priority === 'high' ? 'bg-orange-900 text-orange-200' : 'bg-blue-900 text-blue-200') }}">
                            {{ strtoupper($pl->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                {{ $pl->status === 'completed' ? 'bg-green-900 text-green-200' : ($pl->status === 'in_progress' ? 'bg-yellow-900 text-yellow-200' : 'bg-gray-700 text-gray-300') }}">
                            {{ ucfirst(str_replace('_', ' ', $pl->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="{{ route('picking-lists.mobile', $pl) }}" target="_blank" class="text-cyan-400 hover:text-cyan-300">
                            Buka Mobile
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada picking list.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>