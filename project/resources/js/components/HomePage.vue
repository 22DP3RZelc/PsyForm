<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-2xl w-full text-center">
        <h1 class="text-4xl font-extrabold text-teal-700 dark:text-teal-300 mb-4">Home Page</h1>
        <p class="text-lg text-gray-700 dark:text-gray-200 mb-6">
          Welcome to your dashboard! Here you can manage your profile, explore features, and access admin tools if you have permission.
        </p>
        <div class="flex justify-center gap-4">
          <a href="/register" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold shadow transition dark:bg-teal-700 dark:hover:bg-teal-800">Register</a>
          <a href="/login" class="bg-white border border-teal-500 text-teal-700 px-6 py-2 rounded-lg font-semibold hover:bg-teal-50 transition dark:bg-gray-800 dark:border-teal-400 dark:text-teal-200 dark:hover:bg-gray-700">Login</a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
