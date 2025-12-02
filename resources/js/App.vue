<script setup>
import { useRoute } from 'vue-router';
import Header from './components/Header.vue';

const route = useRoute();
</script>

<template>
    <div id="app">
        <Header v-if="!route.meta.hideNavbar" />
        
        <router-view />
    </div>
</template>