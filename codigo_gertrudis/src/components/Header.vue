<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

//declaramos un array con lso nombres de cada compañeras
const companeras = ["Lucía", "Ainhoa", "Nazaret", "Alba"];

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = ref(JSON.parse(localStorage.getItem('user') || 'null'));

//creamos una variable para almacenar el nombre y la imagen en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');
const fotoUsuario = ref('');
const rolUsuario = ref('');

if (usuarioLogueado.value) {
  nombreUsuario.value = usuarioLogueado.value.usuario;
  fotoUsuario.value = usuarioLogueado.value.avatar;
  rolUsuario.value = usuarioLogueado.value.rol;
}

//funcion para cerrar sesion
function cerrarSesion(){
    localStorage.removeItem('user');
    localStorage.clear();
    usuarioLogueado.value = null;
    nombreUsuario.value = '';
    fotoUsuario.value = '';
    rolUsuario.value = '';
}

</script>

<template>
    <nav class="navbar navbar-expand-lg bg-body">
        <div class="container-fluid">
            <div id="logo">
                <RouterLink to="/" class="nav-link">
                    <img src="/logotipo.png" alt="Logotipo de Código Gertrudis">
                </RouterLink>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <RouterLink to="/conocenos" class="nav-link">Conócenos<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/tienda" class="nav-link">Tienda<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Compañeras</a>
                        <ul class="dropdown-menu">
                            <li v-for="(companera, index) in companeras" :key="companera">
                                <RouterLink :to="`/companera/${companera.toLocaleLowerCase()}`" class="dropdown-item">{{ companera }}</RouterLink>
                                <hr v-if="index !== companeras.length - 1" class="dropdown-divider">
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item" v-if="rolUsuario ==='compi'">
                        <RouterLink to="/galeria" class="nav-link">Galería<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/juegos" class="nav-link">Juegos<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/blog" class="nav-link">Blog<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/contacto" class="nav-link">Contacto<span class="sr-only"></span></RouterLink>
                    </li>
                </ul>
            </div>
            <div class="button">
                <RouterLink v-if="!usuarioLogueado" to="/login" class="nav-link">
                    <i class="bi bi-person-circle"></i>
                    <span>Iniciar sesión</span>
                </RouterLink>
                <div v-else>
                    <RouterLink to="/cambiar-avatar">
                        <img :src="`/avatar/${fotoUsuario}`" class="img-circle" alt="Avatar de tu usuario">
                    </RouterLink>
                    <RouterLink :to="`panel-usuario/${nombreUsuario}`">
                        <h5 class="centered">{{ nombreUsuario }}</h5>
                    </RouterLink>
                    <RouterLink to="/carrito" id="carrito" v-if="rolUsuario === 'cliente' || rolUsuario === 'compi'">
                        <i class="bi bi-cart-fill"></i>
                    </RouterLink>
                </div>
            </div>
            <div v-if="usuarioLogueado" class="cerrar-sesion" @click="cerrarSesion">
                <i class="bi bi-x-octagon-fill"></i>
                <button>Cerrar sesión</button>
            </div>
        </div>
    </nav>
</template>

<style scoped>

    .navbar{
        position: relative;
        margin: auto;
        justify-content: center;
        align-items: center;
    }

    #logo img{
        width: 12vw;
    }

    .collapse.navbar-collapse{
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    li .nav-link{
        color: #fcbf00;
    }

    .router-link-active {
        color: #ff8800;
    }

    .dropdown-menu {
        padding: 0;
        border: 1px solid #fcbf00;
    }

    .dropdown-toggle {
        color: #fcbf00;
    }

    .dropdown-item {
        color: #fcbf00;
        display: block;
        width: 100%;
        padding: 10px 16px;
    }


    .dropdown-item:hover {
        color: white;
        background-color: #ff8800;
        opacity: 0.8;
        font-size: 110%;
    }

    .dropdown-divider {
        margin: 0;
        border-top: 1px solid #fcbf00;
    }

    .button {
        border: none;
        background-color: #fcbf00;
        color: white;
        border-radius: 0px 0px 0px 30px;
        padding: 1%;
        width: 18%;
        height: 70%;
        font-size: 110%;
        font-weight: bold;
        position: absolute;
        top: 0;
        right: 0;
        margin-top: -1%;
        margin-right: -1%;
    }

    .button i{
        font-size: 160%;
        margin-right: 5%;
        margin-left: 8%;
    }

    .button a {
        display: flex;
        align-items: center;
        width: 100%;
        height: 100%;
        margin-top: 2%;
    }

    .button > div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        width: 100%;
        height: 100%;
        padding: 1.2rem 0.8rem;
        overflow: hidden;
    }

    .button > div img {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        object-fit: cover;
    }

    .button > div img:hover {
        transform: scale(1.1);
    }

    .button > div h5 {
        margin: 0;
        font-size: 1rem;
        color: white;
        font-weight: bold;
        text-align: center;
        overflow: hidden;
        margin-top: 0.3rem;
    }

    #carrito{
        color: white;
    }

    a {
        text-decoration: none;
    }

    .cerrar-sesion{
        border: none;
        background-color: #ff8800;
        color: white;
        border-radius: 0px 0px 0px 30px;
        padding: 1rem;
        width: 12.8rem;
        height: 2rem;
        font-size: 110%;
        font-weight: bold;
        position: absolute;
        top: 0;
        right: 0;
        margin-top: 3.7rem;
        margin-right: -1%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cerrar-sesion:hover{
        background-color: #fcbf00;
    }

    .cerrar-sesion button{
        background: none;
        border: none;
        color: white;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .cerrar-sesion button i{
        font-size: 0.2rem;
    }

</style>