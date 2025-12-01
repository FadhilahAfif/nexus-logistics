<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
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
                :class="{ 'border-blue-400 bg-blue-100 dark:bg-blue-900/20': isDragging, 'border-blue-200 bg-blue-50 dark:border-gray-600 dark:bg-gray-800': !isDragging }"
                class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-8 transition-all duration-300 text-center"
            >
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 text-blue-600 dark:text-blue-500">
                        <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                    </svg>
                </div>

                <p class="text-gray-700 dark:text-gray-300 font-medium mb-2">Click the button below to upload your files.</p>
                
                <div class="flex items-center w-full max-w-[200px] my-4">
                    <div class="h-px bg-gray-300 dark:bg-gray-600 flex-1"></div>
                    <span class="px-3 text-gray-400 text-sm font-medium">OR</span>
                    <div class="h-px bg-gray-300 dark:bg-gray-600 flex-1"></div>
                </div>
                
                <label class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors">
                    Choose File
                    <input 
                        x-ref="fileInput" 
                        type="file" 
                        class="hidden" 
                        accept="image/*" 
                        @change="handleFile"
                    >
                </label>
                
                <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">PNG, JPG up to 2MB</p>
            </div>
        </template>
    </div>
</x-dynamic-component>