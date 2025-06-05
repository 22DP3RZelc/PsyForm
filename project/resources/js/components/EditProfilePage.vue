<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-lg w-full text-center">
        <h1 class="text-3xl font-extrabold text-teal-700 dark:text-teal-300 mb-4">Edit Profile</h1>
        <img class="w-32 h-32 mx-auto rounded-full border-4 border-teal-400 shadow mb-4"
             :class="{'border-teal-400': !isDark, 'border-teal-600': isDark}"
             :src="profileImage" alt="Profile Image">
        <form v-if="user" @submit.prevent="saveProfile" class="space-y-4 mt-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Username</label>
            <input v-model="editForm.username" type="text" required
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Email</label>
            <input v-model="editForm.email" type="email" required
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">New Password</label>
            <input v-model="editForm.password" type="password" placeholder="Leave blank to keep current"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Confirm Password</label>
            <input v-model="editForm.password_confirmation" type="password" placeholder="Leave blank to keep current"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <div class="flex justify-center gap-2">
            <button type="submit"
              class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-teal-700 dark:hover:bg-teal-800">Save</button>
            <button type="button" @click="cancelEdit"
              class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-gray-700 dark:hover:bg-gray-800">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const user = ref(null);
const profileImage = ref('/images/default-profile.png');
const editForm = ref({
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const isDark = computed(() => document.documentElement.classList.contains('dark'));

function cancelEdit() {
  if (user.value) {
    editForm.value.username = user.value.username;
    editForm.value.email = user.value.email;
    editForm.value.password = '';
    editForm.value.password_confirmation = '';
  }
  window.location.href = '/profile';
}

async function saveProfile() {
  if (editForm.value.password && editForm.value.password !== editForm.value.password_confirmation) {
    alert('Passwords do not match!');
    return;
  }
  try {
    const payload = {
      username: editForm.value.username,
      email: editForm.value.email,
    };
    if (editForm.value.password) {
      payload.password = editForm.value.password;
      payload.password_confirmation = editForm.value.password_confirmation;
    }
    const response = await fetch('/api/user/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(payload),
    });
    if (!response.ok) {
      const data = await response.json();
      alert(data.message || 'Failed to update profile.');
      return;
    }
    window.location.href = '/profile';
  } catch (error) {
    alert('An error occurred while updating profile.');
  }
}

onMounted(async () => {
  try {
    const response = await fetch('/api/user-session');
    if (response.ok) {
      const data = await response.json();
      user.value = data.user;
      editForm.value.username = data.user.username;
      editForm.value.email = data.user.email;
      // Optionally set profileImage.value if user has a custom image
    }
  } catch (e) {
    user.value = null;
  }
});
</script>
