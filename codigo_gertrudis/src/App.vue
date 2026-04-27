<script setup>
import { computed, ref } from 'vue';
import Footer from './components/Footer.vue';
import Header from './components/Header.vue';
import HeaderAdmin from './components/HeaderAdmin.vue';
import NavBar from './components/NavBar.vue';

//obtenemos el usuario logueado del localstorage
const user = ref(JSON.parse(localStorage.getItem('user')));

//comprobamos que el rol es administrador
const esAdmin = computed(() => {
  if(user.value.rol === 'admin'){
    return true
  } else {
    return false;
  }
})

</script>

<template>
  <Header v-if="!esAdmin && !$route.meta.hideHeader"></Header>
  <HeaderAdmin v-if="esAdmin && !$route.meta.hideHeaderAdmin"></HeaderAdmin>
  <div id="layout">
    <NavBar v-if="esAdmin && !$route.meta.hideNavBar"></NavBar>
    <main id="contenido">
      <RouterView></RouterView>
    </main>
  </div>
  <Footer v-if="!$route.meta.hideFooter"></Footer>
</template>

<style scoped>

  #layout {
    display: flex;
    min-height: auto;
    margin-bottom: 5%;
  }
  
  #contenido {
    flex: 1;
  }

</style>