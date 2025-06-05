<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-lg w-full text-center">
        <h1 class="text-3xl font-extrabold text-teal-700 dark:text-teal-300 mb-4">Your Profile</h1>
        <img class="w-32 h-32 mx-auto rounded-full border-4 border-teal-400 shadow mb-4" :class="{'border-teal-400': !isDark, 'border-teal-600': isDark}" :src="profileImage" alt="Profile Image">
        <div v-if="user" class="mt-4">
          <p class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Welcome, {{ user.username }}!</p>
          <p class="text-gray-600 dark:text-gray-300 mb-6">Email: {{ user.email }}</p>
          <a href="/profile/edit"
            class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-teal-700 dark:hover:bg-teal-800 mr-2 inline-block">Edit Profile</a>
          <button @click="logout"
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg shadow transition dark:bg-red-700 dark:hover:bg-red-800">Logout</button>
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
  computed: {
    isDark() {
      return document.documentElement.classList.contains('dark');
    }
  }
};
</script>
