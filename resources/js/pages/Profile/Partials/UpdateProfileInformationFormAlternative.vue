<script setup>
import { ref } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

import ActionMessage from '@/components/ActionMessage.vue';
import FormSection from '@/components/FormUserSection.vue';
import InputError from '@/components/InputError.vue';

const props = defineProps({ user: Object });
const { props: pageProps } = usePage();

// Reactive state
const photoInput = ref(null);
const photoPreview = ref(null);
const formError = ref(null);
const responseMessage = ref('');
const responseClass = ref('');
const verificationLinkSent = ref(false);

// Styling
const baseButton = "mt-2.5 mx-1 rounded px-2 py-1 text-xs ring-1 ring-inset";
const buttonStyles = {
  default: `${baseButton} bg-white dark:bg-black min-w-16 text-gray-600 ring-gray-300 dark:text-gray-300 dark:ring-gray-600 hover:ring-gray-600 dark:hover:ring-indigo-400`,
  disabled: `${baseButton} text-gray-300 ring-gray-300 dark:text-gray-600 dark:ring-gray-600`,
};

// Form setup
const form = useForm({
  _method: 'PUT',
  name: props.user.name,
  email: props.user.email,
  photo: null,
});

function selectNewPhoto() {
  photoInput.value?.click();
}

function updatePhotoPreview() {
  const file = photoInput.value?.files?.[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = e => (photoPreview.value = e.target.result);
  reader.readAsDataURL(file);
}

function clearPhotoInput() {
  photoPreview.value = null;
  if (photoInput.value) photoInput.value.value = '';
}

function validatePhoto(file) {
  if (!file) return 'No file selected';
  if (file.size > 10 * 1024 * 1024) return 'File size must be under 10MB';

  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!allowedTypes.includes(file.type)) return 'Only JPEG, PNG, or GIF files are allowed';

  return null;
}

async function updateProfileInformation() {
  const file = photoInput.value?.files?.[0];
  const error = validatePhoto(file);

  if (error) {
    formError.value = error;
    return;
  }

  const formData = new FormData();
  formData.append('_method', 'post');
  formData.append('name', form.name);
  formData.append('email', form.email);
  formData.append('photo', file);

  try {
    const { data } = await axios.post('/photo-upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    responseMessage.value = `${data.message} (File: ${data.filename})`;
    responseClass.value = 'text-green-600';
    photoPreview.value = data.path;

    clearPhotoInput();
    await router.reload({ only: ['auth'] });
  } catch (error) {
    const errData = error.response?.data;
    formError.value = errData?.errors || 'Upload failed. Try again.';
    console.error('Upload error:', errData);
  }
}

function deletePhoto() {
  router.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null;
      clearPhotoInput();
    },
  });
}
</script>

<template>
  <FormSection @submitted="updateProfileInformation">

    <!-- Error message -->
    <div v-if="formError" class="text-red-500 mb-2 text-sm">
      {{ formError }}
    </div>

    <template #form>

      <!-- Profile Photo Section -->
      <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4 mt-4">

<div class="-mt-8 text-indigo-600 dark:text-indigo-300 text-lg">{{user.name}}</div> 
    
        <div class="mt-1 grid grid-cols-3 items-start gap-6 ">

          <!-- Photo Display -->
          <div class="col-span-2 space-y-1">
            <input
              id="photo"
              ref="photoInput"
              type="file"
              class="hidden"
              @change="updatePhotoPreview"
              accept="image/*"
            />
            <!-- Current Profile Photo -->
            <div v-show="!photoPreview" class="mt-1">
              <img
                :src="user.profile_photo_url"
                :alt="`Current profile photo for ${user.name}`"
                class="dark:border-gray-600 rounded-full h-20 w-20 object-cover border"
              />
            </div>

            <!-- New Photo Preview -->
            <div v-show="photoPreview" class="mt-1">
              <div
                class="w-20 h-20 rounded-full bg-cover bg-no-repeat bg-center border"
                :style="{ backgroundImage: `url('${photoPreview}')` }"
              ></div>
            </div>

            <!-- Error if any -->
            <InputError :message="form.errors.photo" class="text-sm text-red-500" />
          </div>

          <!-- Photo Buttons -->
          <div class="text-xs mt-1 ml-6 ">
            <button type="button" @click="selectNewPhoto" :class="buttonStyles.default">
              Select
            </button>

            <button
              v-if="user.profile_photo_path"
              type="button"
              @click="deletePhoto"
              :class="buttonStyles.default"
            >
              Remove
            </button>

            <button
              type="button"
              @click="updateProfileInformation"
              :class="buttonStyles.default"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    </template>

      
      <ActionMessage :on="form.recentlySuccessful" class="me-3">
        Saved
      </ActionMessage>


  </FormSection>
</template>

