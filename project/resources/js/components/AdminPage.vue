<template>
  <AppLayout>
    <div class="flex justify-center items-center min-h-screen bg-gradient-to-br from-green-200 via-teal-200 to-blue-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      <div class="bg-white/90 dark:bg-gray-900/90 p-10 rounded-2xl shadow-2xl max-w-3xl w-full">
        <div class="flex mb-6">
          <h1 class="text-3xl font-bold text-teal-700 dark:text-teal-300">Admin Dashboard</h1>
          <button @click="resetChanges" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-auto" :disabled="!hasChanges">Reset</button>
          <button @click="confirmChanges" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 ml-2" :disabled="!hasChanges">Confirm Changes</button>
        </div>
        <div class="flex mb-6 border-b border-gray-300 dark:border-gray-700">
          <button
            class="px-4 py-2 font-semibold focus:outline-none"
            :class="{
              'border-b-4 border-teal-500 text-teal-700 dark:text-teal-300': activeTab === 'users',
              'text-gray-600 dark:text-gray-400': activeTab !== 'users'
            }"
            @click="activeTab = 'users'"
          >Users</button>
          <button
            class="px-4 py-2 font-semibold focus:outline-none ml-2"
            :class="{
              'border-b-4 border-teal-500 text-teal-700 dark:text-teal-300': activeTab === 'tests',
              'text-gray-600 dark:text-gray-400': activeTab !== 'tests'
            }"
            @click="activeTab = 'tests'"
          >Tests</button>
        </div>
        <div v-if="activeTab === 'users'">
          <div v-if="loading" class="text-center">
            <p class="text-gray-700 dark:text-gray-200">Loading users...</p>
          </div>
          <div v-else>
            <div class="flex items-center mb-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by username..."
                class="border border-gray-300 dark:border-gray-700 px-4 py-2 rounded w-full dark:bg-gray-800 dark:text-gray-100 bg-gray-100"
              />
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
        <div v-else-if="activeTab === 'tests'">
          <div v-if="testsLoading" class="text-center text-gray-700 dark:text-gray-200 py-10">
            Loading tests...
          </div>
          <div v-else>
            <div class="flex items-center mb-4">
              <input
                v-model="testSearchQuery"
                type="text"
                placeholder="Search by test name or creator..."
                class="border border-gray-300 dark:border-gray-700 px-4 py-2 rounded w-full dark:bg-gray-800 dark:text-gray-100 bg-gray-100"
              />
              <select
                v-model="testSortKey"
                class="border border-gray-300 dark:border-gray-700 px-4 py-2 rounded ml-4 dark:bg-gray-800 dark:text-gray-100 bg-gray-100"
              >
                <option value="name">Sort by Name</option>
                <option value="creator">Sort by Creator</option>
                <option value="created_at">Sort by Date</option>
              </select>
            </div>
            <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
              <thead>
                <tr class="bg-gray-200 dark:bg-gray-800">
                  <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Test Name</th>
                  <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Creator</th>
                  <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-gray-700 dark:text-gray-200">Created At</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="test in filteredAndSortedTests" :key="test.id" class="text-black dark:text-gray-100">
                  <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ test.name }}</td>
                  <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ test.creator_email || test.creator_username || 'Unknown' }}</td>
                  <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ formatDate(test.created_at) }}</td>
                </tr>
                <tr v-if="filteredAndSortedTests.length === 0">
                  <td colspan="3" class="text-center text-gray-500 dark:text-gray-300 py-4">No tests found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AdminPageJS from '../scripts/AdminPage.js';

export default {
  mixins: [AdminPageJS],
  data() {
    return {
      activeTab: 'users',
      allTests: [],
      testsLoading: false,
      testSearchQuery: '',
      testSortKey: 'name',
    };
  },
  computed: {
    filteredAndSortedTests() {
      let filtered = this.allTests;
      const q = this.testSearchQuery.trim().toLowerCase();
      if (q) {
        filtered = filtered.filter(
          t =>
            (t.name && t.name.toLowerCase().includes(q)) ||
            (t.creator_username && t.creator_username.toLowerCase().includes(q)) ||
            (t.creator && t.creator.toLowerCase().includes(q))
        );
      }
      let sorted = [...filtered];
      if (this.testSortKey === 'name') {
        sorted.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
      } else if (this.testSortKey === 'creator') {
        sorted.sort((a, b) =>
          ((a.creator_username || a.creator || '')).localeCompare(b.creator_username || b.creator || '')
        );
      } else if (this.testSortKey === 'created_at') {
        sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      }
      return sorted;
    },
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return d.toLocaleDateString() + ' ' + d.toLocaleTimeString();
    },
    async fetchAllTests() {
      this.testsLoading = true;
      try {
        let resp = await fetch('/api/tests/all-with-users');
        if (resp.ok) {
          let data = await resp.json();
          this.allTests = data.map(test => ({
            ...test,
            creator_id: test.creator_username || 'Unknown',
            created_at: test.created_at || test.createdAt || '',
          }));
        } else {
          this.allTests = [];
          console.error('Failed to fetch tests:', resp.status, resp.statusText);
        }
      } catch (e) {
        this.allTests = [];
        console.error('Error fetching tests:', e);
      }
      this.testsLoading = false;
    },
  },
  mounted() {
    this.fetchAllTests();
  },
  watch: {
    activeTab(newTab) {
      if (newTab === 'tests' && this.allTests.length === 0) {
        this.fetchAllTests();
      }
    },
  },
};
</script>

<style>
.bg-cyan-100 { background-color: #e0f7fa; }
.dark .bg-cyan-900 { background-color: #164e63 !important; }
.dark .bg-red-900 { background-color: #7f1d1d !important; }
</style>
