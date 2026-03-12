<div class="flex flex-col gap-6 h-full max-w-2xl mx-auto w-full">
    
    <!-- HEADER -->
    <div class="flex items-center justify-between shrink-0 mb-2">
        <div>
            <h1 class="font-bold text-2xl text-gray-900">Tambah Pelajaran</h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan entri data mata pelajaran baru ke dalam sistem.</p>
        </div>
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('manage-subject') }}" wire:navigate class="!text-gray-500 hover:!bg-gray-100">
            Kembali
        </flux:button>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6 sm:p-8">
        <form wire:submit="save" class="flex flex-col gap-6">
            
            <!-- Kode Mapel -->
            <flux:input 
                wire:model="subject_code" 
                label="Kode Pelajaran" 
                placeholder="Misal: MTK-01" 
                required 
            />

            <!-- Nama Pelajaran -->
            <flux:input 
                wire:model="subject_name" 
                label="Nama Pelajaran" 
                placeholder="Misal: Matematika Dasar" 
                required 
            />

            <!-- Kategori -->
            <flux:select wire:model="category" label="Kategori" placeholder="Pilih Kategori">
                <flux:option value="Wajib">Wajib</flux:option>
                <flux:option value="Muatan Lokal">Muatan Lokal</flux:option>
                <flux:option value="Ekstrakurikuler">Ekstrakurikuler</flux:option>
            </flux:select>

            <!-- Status Aktif -->
            <div class="pb-2">
                <flux:switch wire:model="is_active" label="Berstatus Aktif" description="Menentukan apakah pelajaran ini masih diajarkan saat ini." />
            </div>

            <!-- Form Actions -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-100">
                <flux:button variant="ghost" href="{{ route('manage-subject') }}" wire:navigate>
                    Batal
                </flux:button>
                <!-- Main Submit Button using Flux -->
                <flux:button type="submit" variant="primary" class="!bg-[#0F609B] hover:!bg-blue-800 !border-0 px-6">
                    Simpan Pelajaran
                </flux:button>
            </div>
            
        </form>
    </div>

</div>
