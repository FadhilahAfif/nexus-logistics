<div x-data="{
    state: $wire.$entangle('{{ $getStatePath() }}'),
    isDragging: false,
    handleFile(event) {
        const file = event.target.files ? event.target.files[0] : null;
        if (!file) return;
        
        // Check file size (e.g., max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size too large. Max 2MB.');
            if(event.target.value) event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            this.state = e.target.result;
        };
        reader.readAsDataURL(file);
    },
    removeFile() {
        this.state = null;
        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    }
}">
    <!-- Preview State -->
    <template x-if="state">
        <div class="relative block w-full overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <img :src="state" class="h-full w-full object-cover max-h-64" />
            
            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                <button 
                    type="button" 
                    @click="removeFile()" 
                    class="rounded-full bg-white/10 p-2 text-white hover:bg-white/20 focus:outline-none"
                    title="Remove image"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Upload State (Dropzone) -->
    <template x-if="!state">
        <div 
            x-on:dragover.prevent="isDragging = true"
            x-on:dragleave.prevent="isDragging = false"
            x-on:drop.prevent="isDragging = false; handleFile({target: {files: $event.dataTransfer.files}})"
            :class="{ 'border-primary-500 ring-1 ring-primary-500': isDragging, 'border-gray-300 dark:border-gray-700': !isDragging }"
            class="relative flex h-24 w-full cursor-pointer flex-col items-center justify-center rounded-lg border bg-white dark:bg-gray-900 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
        >
            <input 
                x-ref="fileInput"
                type="file" 
                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                accept="image/*"
                @change="handleFile"
            />
            
            <div class="text-center pointer-events-none">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Drag & Drop your files or <span class="font-medium text-primary-600 dark:text-primary-400">Browse</span>
                </p>
            </div>
        </div>
    </template>
</div>