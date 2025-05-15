<template>
    <div v-if="isLoading" class="flex justify-center items-center h-screen w-auto">
        <p class="text-lg font-semibold">Loading...</p>
    </div>
    <div v-else class="flex h-screen w-screen">
        
        <Navigation class="w-64" />
        <main class="flex-1 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>

<script setup>
import Navigation from "./Navigation.vue";
</script>

