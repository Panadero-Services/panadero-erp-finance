<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const photoInput = ref(null);
const previewUrl = ref(null);

const submit = () => {
    const file = photoInput.value?.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('photo', file);

    router.post('/test-upload', formData, {
        forceFormData: true,
        onSuccess: () => {
            previewUrl.value = null;
            photoInput.value.value = null;
        },
    });
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    } else {
        previewUrl.value = null;
    }
};
</script>

<template>
    <div class="p-6 max-w-md mx-auto">
        <h2 class="text-xl font-bold mb-4">Test Photo Upload</h2>

        <div v-if="$page.props.flash?.success" class="text-green-600 mb-4">
            {{ $page.props.flash.success }}
        </div>

        <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
            <input ref="photoInput" type="file" @change="handleFileChange" />

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Upload
            </button>
        </form>

        <div v-if="previewUrl" class="mt-4">
            <p class="mb-2">Preview:</p>
            <img :src="previewUrl" class="max-w-xs rounded shadow">
            {{previewUrl}}
        </div>
    </div>
</template>
