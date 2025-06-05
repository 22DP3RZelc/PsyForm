<script setup>
import { ref, watch, onMounted } from 'vue';
import Navigation from '../scripts/Navigation.js';
import Logout from '../scripts/Logout.js';
import { LogOut } from 'lucide-vue-next';
import { KeyRound } from 'lucide-vue-next';
import { UserRound } from 'lucide-vue-next';
import { UserRoundPlus } from 'lucide-vue-next';
import { ShieldUser } from 'lucide-vue-next';
import { TextSearch } from 'lucide-vue-next';
import { ChevronDown } from 'lucide-vue-next';
import { House } from 'lucide-vue-next';

const user = ref(null);
const navButtons = ref([]);

async function fetchUserSession() {
  try {
    const response = await fetch('/api/user-session');
    if (response.ok) {
      const data = await response.json();
      return data.user;
    }
  } catch (error) {
    console.error('Error fetching user session:', error);
  }
  return null;
}

function updateNavButtons() {
  navButtons.value = [
    { label: 'About', icon: TextSearch, link: '/' },
    { label: 'Home', icon: House, link: '/home' },
    
  ];

  if (user.value) {
    navButtons.value.push(
      { label: 'Create Test', icon: ShieldUser, link: '/tests/create' },
      { label: 'Profile', icon: UserRound, link: '/profile' },
      
    );
    if (user.value.role === 'admin') {
      navButtons.value.push({ label: 'Admin', icon: ShieldUser, link: '/admin' });
    };
    if (user.value) {
      navButtons.value.push(
        { label: 'Logout', icon: LogOut, action: logout },
      );
    }
  } else {
    navButtons.value.push(
      { label: 'Login', icon: KeyRound, link: '/login' },
      { label: 'Registration', icon: UserRoundPlus, link: '/register' }
    );
  }
}

async function logout() {
  try {
    await fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken() } });
    user.value = null;
    updateNavButtons();
    window.location.href = '/login'; // Redirect to login page after logout
  } catch (error) {
    console.error('Error logging out:', error);
  }
}

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

onMounted(async () => {
  user.value = await fetchUserSession();
  updateNavButtons();
  Navigation();
});

watch(user, updateNavButtons);
</script>

<template>
  <aside id="sidebar-multi-level-sidebar" class="top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
        <li v-for="button in navButtons" :key="button.label">
          <template v-if="button.dropdown">
            <button
              :data-dropdown-key="button.label"
              class="dropdown-btn flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            >
              <component :is="button.icon" />
              <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">{{ button.label }}</span>
              <ChevronDown />
            </button>
            <div
              class="dropdown-menu hidden bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg"
              :data-dropdown-key="button.label"
            >
              <ul class="space-y-2">
                <li v-for="child in button.children" :key="child.label">
                  <a
                    v-if="child.link"
                    :href="child.link"
                    class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-200 dark:text-white dark:hover:bg-gray-600"
                  >
                    <component :is="child.icon" />
                    <span class="ms-3">{{ child.label }}</span>
                  </a>
                  <button
                    v-else
                    @click="child.action"
                    :class="['flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-200 dark:text-white', child.class]"
                  >
                    <component :is="child.icon" />
                    <span class="ms-3">{{ child.label }}</span>
                  </button>
                </li>
              </ul>
            </div>
          </template>
          <a
            v-else-if="button.link"
            :href="button.link"
            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
          >
            <component :is="button.icon" />
            <span class="ms-3">{{ button.label }}</span>
          </a>
          <button
            v-else
            @click="button.action"
            class="flex items-center w-full p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
          >
            <component :is="button.icon" />
            <span class="ms-3">{{ button.label }}</span>
          </button>
        </li>
      </ul>
    </div>
  </aside>
</template>