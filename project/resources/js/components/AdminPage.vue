<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-3xl w-full">
        <div class="flex mb-6">
          <h1 class="text-3xl font-bold text-teal-700 dark:text-teal-300">Admin Dashboard</h1>
          <button @click="resetChanges" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-auto" :disabled="!hasChanges">Reset</button>
          <button @click="confirmChanges" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 ml-2" :disabled="!hasChanges">Confirm Changes</button>
        </div>

        <div v-if="loading" class="text-center">
          <p class="text-gray-700 dark:text-gray-200">Loading users...</p>
        </div>
        <div v-else>
          <!-- Search and Sort Controls -->
          <div class="flex items-center mb-4">
            <!-- Search Bar -->
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by username..."
              class="border border-gray-300 dark:border-gray-700 px-4 py-2 rounded w-full dark:bg-gray-800 dark:text-gray-100 bg-gray-100"
            />
            <!-- Sort Dropdown -->
            <select
              v-model="sortKey"
              @change="sortUsers"
              class="border border-gray-300 dark:border-gray-700 px-4 py-2 rounded ml-4 dark:bg-gray-800 dark:text-gray-100 bg-gray-100"
            >
              <option value="username">Sort by Username</option>
              <option value="email">Sort by Email</option>
              <option value="role">Sort by Role</option>
            </select>
          </div>

          <!-- User Table -->
          <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
            <thead>
              <tr class="bg-gray-200 dark:bg-gray-800">
                <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Username</th>
                <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Email</th>
                <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Role</th>
                <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(user, index) in filteredAndSortedUsers"
                :key="user.id"
                :class="{
                  'bg-red-100 dark:bg-red-900': user.markedForDeletion,
                  'bg-cyan-200 dark:bg-cyan-900': user.roleChanged && !user.markedForDeletion,
                  'text-black dark:text-gray-100': true
                }"
              >
                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ user.username }}</td>
                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ user.email }}</td>
                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                  <select
                    v-model="user.role"
                    @change="updateRole(user.id, $event.target.value)"
                    class="border border-gray-300 dark:border-gray-700 px-2 py-1 rounded dark:bg-gray-800 dark:text-gray-100"
                    :disabled="user.markedForDeletion"
                  >
                  <option value="admin">Admin</option>
                    <option value="user">User</option>
                    <option value="psychologist">Psychologist</option>
                  </select>
                </td>
                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                  <button @click="toggleDelete(index)" class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-800">
                    {{ user.markedForDeletion ? 'Undo' : 'Delete' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AdminPageJS from '../scripts/AdminPage.js';

export default {
  mixins: [AdminPageJS],
  };
</script>

<style>
/* Add a cyan background color for rows with changed roles */
.bg-cyan-100 {
  background-color: #e0f7fa;
}
.dark .bg-cyan-900 {
  background-color: #164e63 !important;
}
.dark .bg-red-900 {
  background-color: #7f1d1d !important;
}
</style>
