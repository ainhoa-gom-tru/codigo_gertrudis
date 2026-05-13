<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import router from '@/router';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

//crea las siguientes variables
const entradas = ref([]);
const entradaEliminar = ref(null);
const mensaje = ref('');
const mostrarMensaje = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el id en caso de que hubiera de usuario logueado
const idUsuario = ref('');
const rolUsuario = ref('');

if(usuarioLogueado){
  idUsuario.value = usuarioLogueado.id;
  rolUsuario.value = usuarioLogueado.rol;
}

//funcion para obtener todos los usuarios
function obtenerTodasEntradas(){
    fetch(ApiUrl + '/blog', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Entradas:', data)
        entradas.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodasEntradas();

//funciton apra eliminar la entrada
function eliminarEntrada(){
    fetch(ApiUrl + '/blog', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
            id: entradaEliminar.value
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta:', data)
        obtenerTodasEntradas();
        entradaEliminar.value = null;
        console.log('Respuesta:', data)
        mensaje.value = "La entrada ha sido eliminada con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

//function para cerrar el modal
function cerrarModal(){
    entradaEliminar.value = null;
}

//funcion para redirigir al usuario a la página de añadir entrada de blog
function redirigir(){
    router.push("/anadir-entrada-blog")
}

</script>

<template>
    <div class="hero">
        <img src="/fondo_blog.png">
        <h2>Blog</h2>
    </div>
    <button v-if="rolUsuario === 'compi'" @click="redirigir" class="add">
        <i class="bi bi-file-earmark-plus-fill"></i>
        Añadir entrada de blog
    </button>
    <div id="todasEntradas">
        <div v-if="mostrarMensaje" class="mensaje" :class="mensaje.includes('éxito') ? 'success' : 'error'">
            <button @click="cerrarMensaje">X</button>
            <i v-if="mensaje.includes('éxito')" class="bi bi-check2"></i>
            <i v-else class="bi bi-x-octagon-fill"></i>
            <p class="texto-mensaje">{{ mensaje }}</p>
        </div>
        <div v-for="entrada in entradas" class="card" style="width: 18rem;">
            <div>
                <img :src="`http://localhost:8001/blog/${entrada.foto}`" class="card-img-top" alt="Foto de la entrada del blog">
                <button v-if="entrada.usuario_id === idUsuario && rolUsuario === 'compi'" class="btn-eliminar" @click="entradaEliminar = entrada.id">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ entrada.titulo }}</h5>
                <p class="card-text">{{ entrada.texto }}</p>
                <button>
                    <RouterLink :to="`/detalles-entrada/${entrada.titulo}`">
                        <i class="bi bi-plus-lg"></i>
                        Leer más
                    </RouterLink>
                </button>
            </div>
        </div>
    </div>
    <div v-if="entradaEliminar !=null" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloRojo" class="modal-title fs-5" id="eliminarUsuario">Eliminar entrada</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que quieres eliminar esta entrada?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cancelar</button>
                    <button v-on:click="eliminarEntrada" type="button" class="anadir">Confirmar</button>
                </div>     
            </div>
        </div>
    </div>
</template>

<style scoped>

    .hero{
        position: relative;
    }

    .hero img{
        width: 100%;
        height: 80vh;
        object-fit: cover;
        opacity: 0.3;
    }

    .hero h2{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fcbf00;
        font-size: 3rem;
        font-weight: bold;
        color: black;
        padding: 3rem;
        border-radius: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    #todasEntradas{
        display: flex;
        flex-wrap: wrap;
        margin-top: 4rem;
        margin-left: 2%;
        margin-bottom: 2%;
    }

    .add{
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem;
        width: 15rem;
        cursor: pointer;
        transition: all 0.3s ease;
        color: black;
        margin: 4rem 0em 0rem 3rem;
    }

    .add:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
        color: white;
    }

    .card{
        margin: 1%;
        padding: 1rem;
        border-radius: 2rem;
        color: black;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: none;
    }

    .card > div{
        position: relative;
    }

    .card img{
        border-radius: 1.5rem;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-text{
        margin: 0.3rem 0;
        line-height: 1.4rem;
        max-height: 4.5rem;
        overflow: hidden;
        position: relative;
        padding-right: 1.2rem;
    }

    .card-body button a{
        text-decoration: none;
    }

    .card-title{
        color: #ff8800;
        font-weight: bold;
    }

    .card-text{
        margin: 0.3rem 0;
    }

    .card button{
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card button a{
        color: black;
    }

    .card button a:hover{
        color: white;
    }

    .card button:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-container {
        width: 90%;
        max-width: 25rem;
        border-radius: 0.875rem;
        background: white;
        box-shadow: 0 0.625rem 1.875rem rgba(0,0,0,0.3);
        padding: 1.5rem;
    }

    .modal-header {
        border-bottom: 0.0625rem solid #eee;
        padding-bottom: 1rem;
    }

    .modal-body {
        padding: 1rem 0;
        font-size: 1rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        padding-top: 1rem;
    }

    .btn-cerrar {
        background-color: #ff8800;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-cerrar:hover{
        background-color: #fcbf00;
        color: black;
        transform: translateY(-0.125rem);
    }

    .anadir{
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .anadir:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

    .btn-eliminar{
        position: absolute;
        top: 0.8rem;
        right: 0.8rem;
        width: 1rem;
        padding: 0.5rem 0.7rem;
        border-radius: 50%;
        color: white;
        background: none;
        border: none;
        z-index: 10;
        cursor: pointer;
    }

    .btn-eliminar:hover{
        cursor: pointer;
    }

    @media (max-width: 768px) {

        .hero img {
            height: 50vh;
        }

        .hero h2 {
            font-size: 1.8rem;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            text-align: center;
        }

        button {
            width: 90%;
            margin: 1rem auto;
            display: block;
        }

        #todasEntradas {
            margin: 2rem 1rem;
            justify-content: center;
        }

        .card {
            width: 100% !important;
            margin: 0.8rem 0;
        }

        .card img {
            height: 160px;
        }

        .card-text {
            line-height: 1.4rem;
            max-height: 4.5rem;
            overflow: hidden;
            position: relative;
            padding-right: 1.2rem;
        }

        .card button {
            width: 100%;
        }

        .modal-container {
            width: 95%;
            padding: 1rem;
        }

        .modal-footer {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-cerrar, .anadir {
            width: 100%;
        }
    }

</style>