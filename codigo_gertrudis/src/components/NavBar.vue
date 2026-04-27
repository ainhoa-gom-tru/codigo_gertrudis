<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre y la imagen en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');
const fotoUsuario = ref('');

if(usuarioLogueado){
  nombreUsuario.value = usuarioLogueado.usuario;
  fotoUsuario.value = usuarioLogueado.avatar;
}

</script>

<template>
    <aside>
        <div id="sidebar" class="nav-collapse ">
            <ul class="sidebar-menu" id="nav-accordion">
                <p class="centered">
                    <RouterLink to="/cambiar-avatar">
                        <img :src="`/avatar/${fotoUsuario}`" class="img-circle" alt="Avatar de tu usuario">
                    </RouterLink>
                </p>
                <RouterLink>
                    <h5 class="centered">{{ nombreUsuario }}</h5>
                </RouterLink>
                <RouterLink to="/gestion-usuarios" v-slot="{ isActive }">
                    <li :class="{ active: isActive }">
                        <i class="bi bi-people-fill"></i>
                        Gestión de usuarios
                    </li>
                </RouterLink>
                <RouterLink to="/nuevo-producto" v-slot="{ isActive }">
                    <li :class="{ active: isActive }">
                        <i class="bi bi-bag-plus-fill"></i>
                        Añadir de productos
                    </li>
                </RouterLink>
                <RouterLink to="/gestion-productos" v-slot="{ isActive }">
                    <li :class="{ active: isActive }">
                        <i class="bi bi-kanban-fill"></i>
                        Gestión de productos
                    </li>
                </RouterLink>
            </ul>
        </div>
    </aside>
</template>

<style scoped>

    aside{
        margin-top: 2%;
    }

    #sidebar {
        width: auto;
        height: auto;
        background: #fcbf00;
        border-radius: 0 2.5rem 2.5rem 0;
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }

    .centered {
        text-align: center;
        margin-bottom: 1rem;
    }

    .centered img {
        width: 5rem;
        height: 5rem;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .centered img:hover {
        transform: scale(1.05);
    }

    h5.centered {
        color: black;
        font-weight: 700;
        font-size: 1rem;
        margin-top: 0.5rem;
    }

    .sidebar-menu {
        padding: 0;
        margin: 0;
    }

    li {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem 1rem;
        margin: 0.3rem 0.5rem;
        border-radius: 0.8rem;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    li i {
        font-size: 1.1rem;
        color: black;
        transition: color 0.25s ease;
    }

    a {
        text-decoration: none;
        color: black;
        font-weight: 600;
        font-size: 0.95rem;
        width: 100%;
    }

    li:hover {
        background-color: #ff8800;
        transform: translateX(0.2rem);
    }

    li:hover i, li:hover a {
        color: white;
    }

    li.active {
        background-color: #ff8800;
        color: white;
        font-weight: bold;
    }

    li.active a, li.active i {
        color: white;
    }

    h5.centered {
        cursor: pointer;
    }

    h5.centered:hover {
        color: #ff8800;
    }

</style>