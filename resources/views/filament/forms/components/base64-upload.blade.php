<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{
        state: $wire.$entangle('{{ $getStatePath() }}'),
        handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Check file size (e.g., max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size too large. Max 2MB.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                this.state = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }">
        <!-- Preview -->
        <template x-if="state">
            <div class="mb-2 relative w-fit">
                <img :src="state" class="h-32 w-auto rounded-lg border border-gray-300 object-cover" />
                <button 
                    type="button" 
                    @click="state = null; $refs.fileInput.value = ''" 
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-sm"
                    title="Remove image"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </template>

        <!-- Input -->
        <input 
            x-ref="fileInput"
            type="file" 
            accept="image/*"
            @change="handleFile"
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
        />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG up to 2MB (Stored as Base64)</p>
    </div>
</x-dynamic-component>