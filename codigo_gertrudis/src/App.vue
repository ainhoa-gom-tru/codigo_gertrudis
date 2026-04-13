<script setup>
import Footer from './components/Footer.vue';
import Header from './components/Header.vue';
import HomeView from './views/HomeView.vue';

</script>

<template>
  <Header v-if="!$route.meta.hideHeader"></Header>
  <RouterView></RouterView>
  <Footer v-if="!$route.meta.hideFooter"></Footer>
</template>

<style scoped>
</style>