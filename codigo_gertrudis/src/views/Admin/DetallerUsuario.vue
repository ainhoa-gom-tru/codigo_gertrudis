<script setup>
import { ApiUrl } from '@/main';
import { ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

const route = useRoute();
//obtenemos de la url el nombre de usuario para imprimir todos los detalles
const nombreUsuario = route.params.usuario;
console.log(nombreUsuario);

//creamos las siguientes variables
const usuario = ref(null);

//funcion para obtener todos los datos del usuario
function obtenerDatosUsuario() {
    fetch(ApiUrl + '/usuarios', {
        method: 'GET',
    })
        .then(response => response.json())
        .then(data => {
            const user = data.find(sr => sr.usuario === nombreUsuario);
            usuario.value = user;
            console.log('Usuario:', usuario)
        })
        .catch(error => console.error('Error:', error));
}

obtenerDatosUsuario()

</script>

<template>
    <div id="body">
        <main>
            <RouterLink to="/gestion-usuarios">
                <i class="bi bi-arrow-left"></i>
                Volver
            </RouterLink>
            <h2>Datos personales</h2>
            <img :src="`/avatar/${usuario.avatar}`" class="img-circle" alt="Avatar del usuario">
            <table class="table table-hover">
                <caption>No puedes ver la contraseña ni modificar datos.</caption>
                <thead></thead>
                <tbody id="cuerpoTabla">
                    <tr>
                        <th scope="row">ID</th>
                        <td>{{ usuario.id }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Nombre</th>
                        <td>{{ usuario.nombre }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Apellidos</th>
                        <td>{{ usuario.apellidos }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Usuario</th>
                        <td>{{ usuario.usuario }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Email</th>
                        <td>{{ usuario.email }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Contraseña</th>
                        <td>{{ '*'.repeat(usuario.contrasena.length) }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Rol</th>
                        <td>{{ usuario.rol }}</td>
                    </tr>
                    <tr>
                        <th scope="row">Avatar</th>
                        <td>
                            <img :src="`/avatar/${usuario.avatar}`" class="img-avatar" alt="Avatar del usuario">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Cuenta validada</th>
                        <td v-if="usuario.validado">Sí</td>
                        <td v-else>No</td>
                    </tr>
                </tbody>
                <tfoot></tfoot>
            </table>
        </main>
    </div>
</template>

<style scoped>

    #body {
        display: flex;
        justify-content: center;
        padding: 2rem;
    }

    main {
        width: 100%;
        border-radius: 1.5rem;
        padding: 2rem;
    }

    main a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        color: #fcbf00;
        font-weight: 600;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    main a:hover {
        color: #ff8800;
        transform: translateX(-3px);
    }

    h2 {
        text-align: center;
        margin-bottom: 1.5rem;
        color: #fcbf00;
        font-weight: 700;
    }

    .img-circle {
        display: block;
        margin: 0 auto 1.5rem auto;
        width: 9rem;
        height: 9rem;
        object-fit: cover;
        cursor: pointer;
    }

    table {
        width: 100%;
    }

    th {
        width: 35%;
        padding: 0.8rem;
        text-align: left;
        font-weight: 600;
    }

    td {
        padding: 0.8rem;
    }

    .img-avatar {
        width: 4rem;
        height: 4rem;
        border-radius: 0.5rem;
        object-fit: cover;
    }

</style>