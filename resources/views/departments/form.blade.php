{{-- Partial form departemen: menerima $department (null untuk buat baru) --}}
<div class="space-y-6">
    <div>
        <label for="name" class="block font-medium text-sm text-gray-700">Nama Departemen <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text" required maxlength="50"
               value="{{ old('name', $department->name ?? '') }}"
               placeholder="Contoh: Keuangan"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" />
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi</label>
        <input id="description" name="description" type="text" maxlength="255"
               value="{{ old('description', $department->description ?? '') }}"
               placeholder="Penjelasan singkat departemen (opsional)"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror" />
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
