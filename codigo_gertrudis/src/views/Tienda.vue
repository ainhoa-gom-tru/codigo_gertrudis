<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

//crea las siguientes variables
const productos = ref([]);
const valoraciones = ref([]);
const exito = ref(false);
const error = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');

if(usuarioLogueado){
  nombreUsuario.value = usuarioLogueado.usuario;
}

//funcion para obtener todos los usuarios
function obtenerTodosProductos(){
    fetch(ApiUrl + '/productos', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Productos:', data)
        productos.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodosProductos();

//funcion para añadir los productos al carrito
function añadirCarrito(producto) {

    if(!usuarioLogueado){
        error.value = true;
        return;
    }

    fetch(ApiUrl + '/carrito', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            producto_id: producto.id,
            cantidad: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Añadido al carrito:', data);
        exito.value = true;
    })
    .catch(error => {
        console.error('Error:', error)
    });
}

//function para obtener todas las valoraciones
function obtenerTodasValoraciones(){
    fetch(ApiUrl + '/valoraciones', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Valoraciones:', data)
        valoraciones.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodasValoraciones();

//function para obtener la media de un producto
function calcularMediaValoracion(producto_id){
    const filtradas = valoraciones.value.filter(v => v.producto_id == producto_id);

    if (filtradas.length === 0) return 0;

    const suma = filtradas.reduce((acc, v) => acc + v.puntuacion, 0);

    return (suma / filtradas.length).toFixed(1);
}

//hacemos una funcion para cerrar el modal
function cerrarModal(){
    exito.value = false;
    error.value = false;
}

</script>

<template>
    <div class="hero">
        <img src="/fondo tienda.png">
        <h2>Productos</h2>
    </div>
    <div id="todosProductos">
        <div v-for="producto in productos" class="card" style="width: 18rem;">
            <div>
                <RouterLink :to="`/detalles-producto/${producto.nombre}`">
                    <img :src="`http://localhost:8001/productos/${producto.foto}`" class="card-img-top" alt="Foto del producto">
                </RouterLink>
                <div id="valoracion">
                    <p>{{ calcularMediaValoracion(producto.id) }}</p>
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
            <div class="card-body">
                <RouterLink :to="`/detalles-producto/${producto.nombre}`">
                    <h5 class="card-title">{{ producto.nombre }}</h5>
                </RouterLink>
                <p class="card-text">
                    {{ producto.precio.toString().replace('.', ',') }}€
                </p>
                <button @click="añadirCarrito(producto)">
                    <i class="bi bi-bag-plus-fill"></i>
                    Añadir al carrito
                </button>
            </div>
        </div>
    </div>
    <div v-if="exito" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloVerde" class="modal-title fs-5" id="eliminarUsuario">Producto añadido exitosamente</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Producto añadido a la cesta con éxito</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cerrar</button>
                </div>     
            </div>
        </div>
    </div>
    <div v-if="error" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloRojo" class="modal-title fs-5" id="eliminarUsuario">Error</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>No puedes añadir productos al carrito si no tienes la sesión iniciada</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cerrar</button>
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

    #todosProductos{
        display: flex;
        flex-wrap: wrap;
        margin-left: 2%;
        margin-bottom: 2%;
        margin-top: 4rem;
    }

    .card{
        margin: 1%;
        padding: 1rem;
        border-radius: 2rem;
        color: black;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: none;
    }

    .card img{
        border-radius: 1.5rem;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    #valoracion{
        display: flex;
        align-items: center;
        gap: 0.3rem;
        background-color: white;
        padding: 0rem 0.5rem;
        border-radius: 0.8rem;
        position: absolute;
        top: 7%;
        right: 9%;
        font-weight: bold;
        color: #fcbf00;
        height: 2.5rem;
    }

    #valoracion p{
        margin-top: 0.8rem;
    }

    #valoracion i{
        margin-top: -0.3rem;
    }

    .card-title{
        color: #ff8800;
        font-weight: bold;
    }

    a {
        text-decoration: none;
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

    .card button:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

    .card button i{
        margin-right: 0.2rem;
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

    @media (max-width: 768px) {

        .hero img {
            height: 40vh;
        }

        .hero h2 {
            font-size: 1.8rem;
            padding: 1.2rem;
            border-radius: 20px;
            width: 90%;
            text-align: center;
        }

        #todosProductos {
            margin: 2rem 1rem;
            justify-content: center;
        }

        .card {
            width: 100% !important;
            margin: 0.5rem 0;
        }

        .card img {
            height: 160px;
        }

        #valoracion {
            top: 6%;
            right: 6%;
            height: 2rem;
            font-size: 0.85rem;
            padding: 0 0.4rem;
        }

        #valoracion p {
            margin-top: 0.4rem;
        }

        .card-title {
            font-size: 1rem;
        }

        .card-text {
            font-size: 0.9rem;
        }

        .card button {
            font-size: 0.9rem;
            padding: 0.6rem;
        }
    }

</style>