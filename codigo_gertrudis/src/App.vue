<script setup>
import { computed, ref } from 'vue';
import Footer from './components/Footer.vue';
import Header from './components/Header.vue';
import HeaderAdmin from './components/HeaderAdmin.vue';
import NavBarAdmin from './components/NavBarAdmin.vue';
import NavBar from './components/NavBar.vue';

//obtenemos el usuario logueado del localstorage
const user = ref(JSON.parse(localStorage.getItem('user')));

//comprobamos que hay usuario logueado
const usuarioLogeado = computed(() => {
  if(user.value){
    return true;
  } else {
    return false;
  }
})

//comprobamos que el rol es administrador
const esAdmin = computed(() => {
  if(usuarioLogeado.value && user.value && user.value.rol === 'admin'){
    return true;
  }
  if(usuarioLogeado.value && user.value && user.value.rol !== 'admin'){
    return false;
  }
})

</script>

<template>
  <HeaderAdmin v-if="esAdmin && !$route.meta.hideHeaderAdmin"></HeaderAdmin>
  <Header v-else></Header>
  <div id="layout">
    <NavBarAdmin v-if="esAdmin && !$route.meta.hideNavBar"></NavBarAdmin>
    <NavBar></NavBar>
    <main id="contenido">
      <RouterView :key="$route.params.nombre"></RouterView>
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