{{-- Partial form tiket: menerima $ticket (null untuk buat baru), $departments, $categories,
     dan opsional $showStaffFields (status untuk staf), $showEstimateField (estimasi pengerjaan) --}}
@php
    $catMap = $categories->groupBy('department_id')
        ->map(fn ($cs) => $cs->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values())
        ->toArray();
    $initDept = old('department_id', $ticket->department_id ?? '');
    $initCat = old('category_id', $ticket->category_id ?? '');
@endphp

<div x-data="{
        dept: @js($initDept === '' ? null : (int) $initDept),
        cat: @js($initCat === '' ? null : (int) $initCat),
        cats: @js($catMap)
     }"
     class="space-y-8">

    {{-- Bagian: Detail masalah --}}
    <section>
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </span>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Detail Masalah</h3>
                <p class="text-xs text-gray-500">Jelaskan kendala yang Anda alami selengkap mungkin.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div>
                <label for="title" class="block font-medium text-sm text-gray-700">Judul Masalah <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required maxlength="150"
                       value="{{ old('title', $ticket->title ?? '') }}"
                       placeholder="Contoh: Laptop tidak bisa menyala"
                       class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror" />
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi Masalah <span class="text-red-500">*</span></label>
                <textarea id="description" name="description" rows="6" required maxlength="5000"
                          placeholder="Jelaskan masalah Anda selengkap mungkin: kapan terjadi, pesan error yang muncul, langkah yang sudah dicoba, dll."
                          class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $ticket->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <div class="border-t border-gray-100"></div>

    {{-- Bagian: Klasifikasi tiket --}}
    <section>
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
            </span>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Tujuan & Prioritas</h3>
                <p class="text-xs text-gray-500">Tentukan departemen tujuan dan tingkat urgensi tiket.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="department_id" class="block font-medium text-sm text-gray-700">Ditujukan ke Departemen <span class="text-red-500">*</span></label>
                <select id="department_id" name="department_id" x-model="dept" @change="cat = null" required
                        class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department_id') border-red-500 @enderror">
                    <option value="">— Pilih departemen —</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category_id" class="block font-medium text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" x-model="cat" required
                        class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                    <option value="" x-show="!dept">— Pilih departemen dulu —</option>
                    <template x-for="c in (cats[dept] || [])" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="priority" class="block font-medium text-sm text-gray-700">Prioritas <span class="text-red-500">*</span></label>
                <select id="priority" name="priority" required
                        class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('priority') border-red-500 @enderror">
                    @foreach (\App\Models\Ticket::PRIORITY_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ old('priority', $ticket->priority ?? 'sedang') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            @if (isset($showEstimateField) && $showEstimateField)
                <div class="sm:col-span-2">
                    <p class="font-medium text-sm text-gray-700">Estimasi Pengerjaan <span class="font-normal text-gray-400">(ditentukan oleh admin)</span></p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="estimated_duration" value="" class="peer sr-only"
                                   {{ old('estimated_duration', $ticket->estimated_duration ?? '') === '' ? 'checked' : '' }} />
                            <span class="inline-block rounded-full border border-gray-200 bg-white px-3.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">Tidak ada</span>
                        </label>
                        @foreach (\App\Models\Ticket::ESTIMATE_LABELS as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="estimated_duration" value="{{ $value }}" class="peer sr-only"
                                       {{ old('estimated_duration', $ticket->estimated_duration ?? '') === $value ? 'checked' : '' }} />
                                <span class="inline-block rounded-full border border-gray-200 bg-white px-3.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Perkiraan lama pengerjaan — membantu memprioritaskan penanganan.</p>
                    @error('estimated_duration') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>
    </section>

    @if (isset($showStaffFields) && $showStaffFields)
        <div class="border-t border-gray-100"></div>

        {{-- Bagian: Status penanganan (staf) --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Status Penanganan</h3>
                    <p class="text-xs text-gray-500">Hanya admin/staf yang dapat mengubah status.</p>
                </div>
            </div>

            <div class="sm:max-w-xs">
                <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                <select id="status" name="status"
                        class="mt-1.5 w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (\App\Models\Ticket::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $ticket->status ?? 'baru') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </section>
    @endif
</div>
