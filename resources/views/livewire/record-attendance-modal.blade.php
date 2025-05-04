<div>
    <div class="p-4">
        <h2 class="text-lg font-bold mb-4">Input Absensi - {{ $employee->name }}</h2>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu Masuk</label>
            <input type="time" wire:model="clock_in" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('clock_in') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Waktu Pulang</label>
            <input type="time" wire:model="clock_out" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        @if (session()->has('message'))
            <div class="text-green-500 mb-4">{{ session('message') }}</div>
        @endif

        <div class="flex justify-end space-x-2 mt-6">
            <button @click="$dispatch('close-modal')" type="button" class="px-4 py-2 bg-gray-300 rounded-md">Batal</button>
            <button wire:click="submit" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan</button>
        </div>
    </div>
</div>