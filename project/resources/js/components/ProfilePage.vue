<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-lg w-full text-center">
        <h1 class="text-3xl font-extrabold text-teal-700 dark:text-teal-300 mb-4">Your Profile</h1>
        <img class="w-32 h-32 mx-auto rounded-full border-4 border-teal-400 shadow mb-4" :class="{'border-teal-400': !isDark, 'border-teal-600': isDark}" :src="profileImage" alt="Profile Image">
        <div v-if="user" class="mt-4">
          <template v-if="!editing">
            <p class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Welcome, {{ user.username }}!</p>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Email: {{ user.email }}</p>
            <button @click="editing = true"
              class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-teal-700 dark:hover:bg-teal-800 mr-2">Edit Profile</button>
            <button @click="logout"
              class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-red-700 dark:hover:bg-red-800">Logout</button>
          </template>
          <template v-else>
            <form @submit.prevent="saveProfile" class="space-y-4">
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
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script scoped>
import Logout from '../scripts/Logout.js';

export default {
  mixins: [Logout],
  props: {
    user: {
      type: Object,
      required: false,
      default: null,
    },
    profileImage: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      editing: false,
      editForm: {
        username: this.user ? this.user.username : '',
        email: this.user ? this.user.email : '',
        password: '',
        password_confirmation: '',
      },
    };
  },
  watch: {
    user(newUser) {
      if (newUser) {
        this.editForm.username = newUser.username;
        this.editForm.email = newUser.email;
      }
    }
  },
  computed: {
    isDark() {
      return document.documentElement.classList.contains('dark');
    }
  },
  methods: {
    async saveProfile() {
      if (this.editForm.password && this.editForm.password !== this.editForm.password_confirmation) {
        alert('Passwords do not match!');
        return;
      }
      try {
        const payload = {
          username: this.editForm.username,
          email: this.editForm.email,
        };
        if (this.editForm.password) {
          payload.password = this.editForm.password;
          payload.password_confirmation = this.editForm.password_confirmation;
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
        const updated = await response.json();
        this.$emit('update:user', updated.user || payload);
        this.editing = false;
        // Optionally reload page or fetch user again
        window.location.reload();
      } catch (error) {
        alert('An error occurred while updating profile.');
      }
    },
    cancelEdit() {
      this.editing = false;
      this.editForm.username = this.user.username;
      this.editForm.email = this.user.email;
      this.editForm.password = '';
      this.editForm.password_confirmation = '';
    }
  }
};
</script>
