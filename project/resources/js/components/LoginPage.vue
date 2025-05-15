<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-8 rounded-2xl shadow-2xl w-full max-w-lg">
        <h1 class="text-3xl font-extrabold text-teal-700 dark:text-teal-300 text-center mb-6">Sign In</h1>
        <form @submit.prevent="login" class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Email</label>
            <input type="email" id="email" v-model="form.email" required placeholder="Enter your email"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Password</label>
            <input type="password" id="password" v-model="form.password" required placeholder="Enter your password"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 dark:bg-gray-800 dark:text-gray-100 transition" />
          </div>
          <button type="submit"
            class="w-full py-3 bg-teal-500 text-white font-bold rounded-lg shadow-md hover:bg-teal-600 transition dark:bg-teal-700 dark:hover:bg-teal-800">Login</button>
        </form>
        <p class="text-center mt-6 text-sm text-gray-600 dark:text-gray-300">
          Don't have an account?
          <a href="/register" class="text-teal-600 dark:text-teal-300 hover:underline font-semibold">Register here</a>
        </p>
      </div>
    </div>
  </AppLayout>
</template>
<script>
import loginScript from '../scripts/Login.js';
export default {
  mixins: [loginScript],
};
</script>

