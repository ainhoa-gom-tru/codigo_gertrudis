<script setup>
import router from '@/router';
import { ref } from 'vue';


//recuperamos el usuario logueaod del local storage
const usuarioLogueado = ref(JSON.parse(localStorage.getItem('user') || 'null'));

//creamos una variable para almacenar el nombre y la imagen en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');
const fotoUsuario = ref('');
const rolUsuario = ref('');
const usuarioValidado = ref(null);

if (usuarioLogueado.value) {
  nombreUsuario.value = usuarioLogueado.value.usuario;
  fotoUsuario.value = usuarioLogueado.value.avatar;
  rolUsuario.value = usuarioLogueado.value.rol;
  usuarioValidado.value = usuarioLogueado.value.validado;
}

//funcion para cerrar sesion
function cerrarSesion(){
    localStorage.removeItem('user');
    localStorage.clear();
    usuarioLogueado.value = null;
    nombreUsuario.value = '';
    fotoUsuario.value = '';
    rolUsuario.value = '';
    usuarioValidado.value = null;
    router.push("/");
}

</script>

<template>
    <header class="header black-bg">
        <div>
            <button class="navbar">
                <i class="bi bi-list"></i>
            </button>
            <h2>Código Gertrudis</h2>
        </div>
        <div class="top-menu">
            <ul class="nav pull-right top-menu">
                <button class="btn" @click="cerrarSesion">
                    <i class="bi bi-door-closed-fill"></i>
                    Cerrar sesión
                </button>
            </ul>
        </div>
    </header>
</template>

<style scoped>

    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        background-color: #ffffff;
        top: 0;
    }

    header > div:first-child {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .navbar {
        background-color: #fcbf00;
        border: none;
        border-radius: 0.6rem;
        padding: 0.5rem 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .navbar i {
        color: black;
        font-size: 1.2rem;
    }

    h2 {
        color: #fcbf00;
        text-decoration: none;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 0.02rem;
        transition: color 0.25s ease;
        margin-top: 3%;
    }

    .top-menu {
        display: flex;
        align-items: center;
    }

    .top-menu ul {
        margin: 0;
        padding: 0;
    }

    .btn {
        background-color: #fcbf00;
        color: black;
        font-weight: 700;
        border: none;
        border-radius: 0.6rem;
        padding: 0.5rem 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .btn i {
        font-size: 1.1rem;
        color: black;
    }

    .btn:hover {
        background-color: #ff8800;
        transform: translateY(-0.1rem);
        color: white;
    }

    .btn:hover i {
        color: white;
    }

</style>