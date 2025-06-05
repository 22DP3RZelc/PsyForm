<template>
  <AppLayout>
    <div class="p-6 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <!-- Header -->
      <header class="flex items-center justify-between bg-white dark:bg-gray-900 p-4 shadow rounded-xl mb-6">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-teal-300">PsyForm</h1>
        <input
          placeholder="Search content in the platform"
          class="w-1/3 border rounded px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-700"
          type="text"
          v-model="searchQuery"
        />
      </header>

      <!-- Main Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Available Tests -->
          <div>
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-teal-300">Available tests</h3>
            <div v-if="filteredTests.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div v-for="test in filteredTests" :key="test.id" class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">In progress</div>
                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ test.name }}</div>
                <button
                  class="mt-2 bg-teal-500 hover:bg-teal-600 focus:bg-teal-700 active:bg-teal-700 transition-colors text-white px-4 py-2 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-2 w-full"
                  @click="goToTest(test.id)"
                >
                  Take Test
                </button>
              </div>
            </div>
          <div v-else class="col-span-1 sm:col-span-2 lg:col-span-4 text-center text-gray-600 dark:text-gray-300">
            No tests available.
          </div>
          </div>

          <!-- Learning History -->
          <div>
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-teal-300">Completed Tests</h3>
            <div v-if="completedTests.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-for="test in completedTests" :key="test.id" class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">Completed</div>
                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ test.name }}</div>
              </div>
            </div>
            <div v-else class="col-span-1 sm:col-span-2 lg:col-span-4 text-center text-gray-600 dark:text-gray-300">No completed tests yet.</div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          <!-- Activity Overview -->
          <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
            <h4 class="font-semibold mb-2 text-gray-900 dark:text-teal-300">Activity Overview</h4>
            <div v-if="user">
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded h-3 mb-2">
                <div
                  class="bg-blue-500 h-3 rounded"
                  :style="{
                    width: user.role === 'psychologist'
                      ? (testsCreatedTotal > 0 ? (100 * testsCreated / testsCreatedTotal) : 0) + '%'
                      : (testsCreatedTotal > 0 ? (100 * testsCompleted / testsCreatedTotal) : 0) + '%'
                  }"
                ></div>
              </div>
              <p class="text-xs text-gray-900 dark:text-gray-100 font-semibold mb-1">
                <span v-if="user.role === 'psychologist'">
                  Tests created: <span class="text-blue-600 dark:text-blue-400">{{ testsCreated }}</span>
                </span>
                <span v-else>
                  Tests completed: <span class="text-blue-600 dark:text-blue-400">{{ testsCompleted }}</span>
                </span>
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-300">
                {{ user.role === 'psychologist'
                  ? (testsCreated + ' created')
                  : (testsCompleted + ' completed') }}
              </p>
            </div>
            <div v-else>
              <p class="text-gray-500 dark:text-gray-300">Please log in to see your activity overview.</p>
            </div>
          </div>

          <!-- Profile -->
          <div class="bg-blue-600 text-white rounded shadow p-4">
            <div v-if="user" class="text-lg font-bold">
              <img class="w-12 h-12 rounded-full border-4 border-teal-400 shadow mb-4" :class="{'border-teal-400': !isDark, 'border-teal-600': isDark}" src="/images/defaultpfp.png" alt="Profile Image">
            </div>
            <p class="text-sm" v-if="user">{{ user.username }}</p>
            <p class="text-sm mb-4" v-if="user">{{ user.email }}</p>
            <div v-if="user" class="flex flex-wrap gap-2">
              <button onclick="location.href='/profile/edit'" class="bg-white text-blue-700 px-3 py-1 rounded">Edit Profile</button>
              <button onclick="location.href='/profile'" class="bg-white text-blue-700 px-3 py-1 rounded">My Profile</button>
            </div>
            <div v-else class=" mb-4"> 
              <h1>Not logged in</h1> 
            </div>
            <div v-if="!user" class="flex flex-wrap gap-2">
              <button onclick="location.href='/login'" class="bg-white text-blue-700 px-3 py-1 rounded">Login</button>
              <button onclick="location.href='/register'" class="bg-white text-blue-700 px-3 py-1 rounded">Register</button>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const tests = ref([]);
const completedTests = ref([]);
const user = ref(null);
const testsCreated = ref(0);
const testsCreatedTotal = ref(0);
const testsCompleted = ref(0);
const searchQuery = ref('');

const filteredTests = computed(() => {
  if (!searchQuery.value.trim()) return tests.value;
  return tests.value.filter(test =>
    test.name.toLowerCase().includes(searchQuery.value.trim().toLowerCase())
  );
});

function goToTest(testId) {
  window.location.href = `/tests/${testId}/complete`;
}

onMounted(async () => {
  try {
    const response = await fetch('/api/user-session');
    if (response.ok) {
      const data = await response.json();
      user.value = data.user;
    }
    // Use the group-by-creator API for activity overview
    const groupResp = await fetch('/api/tests/group-by-creator');
    let grouped = {};
    if (groupResp.ok) {
      grouped = await groupResp.json();
      // Calculate total tests created (all creators)
      testsCreatedTotal.value = Object.values(grouped).reduce((sum, arr) => sum + arr.length, 0);

      if (user.value && (user.value.role === 'admin' || user.value.role === 'psychologist')) {
        // For admin/psychologist: count tests where creator_id matches user.id
        const userId = user.value.id.toString();
        testsCreated.value = grouped[userId] ? grouped[userId].length : 0;
        testsCompleted.value = 0;
      } else if (user.value) {
        // For regular user: count completed tests (assume completedList API returns array of test objects)
        const completedListResp = await fetch(`/api/tests/completed-list?user_id=${user.value.id}`);
        if (completedListResp.ok) {
          completedTests.value = await completedListResp.json();
          testsCompleted.value = completedTests.value.length;
        } else {
          testsCompleted.value = 0;
        }
        testsCreated.value = 0;
      }
    }
    // Fetch all tests for available/completed display
    const testsResponse = await fetch('/api/tests');
    if (testsResponse.ok) {
      tests.value = await testsResponse.json();
    }
  } catch (e) {
    user.value = null;
    testsCreated.value = 0;
    testsCreatedTotal.value = 0;
    testsCompleted.value = 0;
  }
});
</script>
