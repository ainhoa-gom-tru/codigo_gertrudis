<script setup>
import { RouterLink, useRoute } from 'vue-router';
import companeras from '@/compis/compis.json';
import { computed, ref } from 'vue';
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';

//Creamos las siguientes variables
const productos = ref([]);
const valoraciones = ref([]);
const route = useRoute();
const exito = ref(false);
const error = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');

if(usuarioLogueado){
  nombreUsuario.value = usuarioLogueado.usuario;
}

//obtenemos de la url el nombre de la compañera para imprimir todos los detalles
const nombreCompi = route.params.nombre;
console.log(nombreCompi);

//para encontrar la informacion de la compañera en el jsons
const companeraActual = computed(() =>
  companeras.find(c => c.nombre.toLowerCase() === nombreCompi.toLowerCase())
);

//funcion para obtener todos los productos
function obtenerProductos() {
  fetch(ApiUrl + '/productos')
    .then(res => res.json())
    .then(data => {
        console.log(data);
        productos.value = data.filter(p => p.nombre.toLowerCase().includes(nombreCompi.toLowerCase())).slice(0, 4);
    })
    .catch(err => console.error(err));
}

obtenerProductos();

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
    <div id="presentacionCompanera">
        <img class="fondoImagen" :src="`/compis/${companeraActual.fondo}`">
        <div class="imagenCompanera">
            <img id="fondo" src="/fondoImagenCompi.png">
            <div class="foto-y-firma">
                <img id="foto" :src="`/compis/${companeraActual.foto}`" alt="Foto compañera de piso">
                <img class="firma" :src="`/compis/${companeraActual.firma}`" alt="Firma">
            </div>
        </div>
    </div>
    <div v-if="companeraActual && companeraActual.galeria" class="galeria-container">
        <div v-for="(fotoGaleria, index) in companeraActual.galeria" :key="index" class="galeria-card">
            <img :src="`/compis/${fotoGaleria.fotografia}`" />
            <span class="tooltip">{{ fotoGaleria.tooltip }}</span>
        </div>
    </div>
    <div id="descripcion" :style="{ backgroundColor: companeraActual.color }">
        <p>{{ companeraActual.descripcion }}</p>
    </div>
    <div id="todosProductos">
        <h4>Productos relacionados</h4>
        <div class="productos-container">
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

    #presentacionCompanera {
        position: relative;
        overflow: hidden;
        margin-bottom: 3rem;
        width: 100%;
    }

    .fondoImagen {
        width: 100vw;
        height: 90vh;
        object-fit: cover;
        display: block;
        z-index: 0;
    }

    .imagenCompanera{
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #fondo{
        width: 25rem;
        z-index: 2;
        margin-left: 6rem;
    }

    .foto-y-firma{
        position: absolute;
        top: 1.5rem;
        left: 8rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 3;
    }


    .firma{
        width: 20rem;
        margin-left: 16rem;
    }

    #foto{
        width: 20rem;
        position: static;
        top: 1.5rem;
        left: 8rem;
        z-index: 3;
    }

    .galeria-container {
        display: flex;
        gap: 1.5rem;
        padding: 2rem;
        justify-items: center;
    }

    .galeria-card {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        width: 100%;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .galeria-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tooltip {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,1);
        color: white;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        opacity: 0;
    }

    .galeria-card:hover .tooltip {
        opacity: 1;
    }

    #descripcion{
        padding: 2rem;
        margin: 3rem 0rem;
        color: white;
        text-align: center;
    }

    #descripcion p{
        width: 80%;
        margin: 0 auto;
    }

    #todosProductos{
        margin: 0rem 6rem;
    }

    #todosProductos h4{
        color: #fcbf00;
        margin: 1rem 0rem;
    }

    .productos-container{
        display: flex;
        flex-wrap: wrap;
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

    a{
        text-decoration: none;
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

        #presentacionCompanera {
            height: 70vh;
        }

        .fondoImagen {
            height: 70vh;
            object-fit: cover;
        }

        #fondo {
            width: 14rem;
            margin-left: 0;
        }

        .imagenCompanera {
            transform: translateY(-50%);
            align-items: center;
            width: 100%;
        }

        .foto-y-firma {
            position: absolute;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }

        #foto {
            width: 10rem;
        }

        .firma {
            width: 8rem;
            margin-left: 0;
        }

        .galeria-container {
            flex-direction: column;
            padding: 1rem;
            gap: 1rem;
        }

        .galeria-card {
            width: 100%;
        }

        #descripcion p {
            width: 95%;
        }

        #todosProductos {
            margin: 0 1rem;
        }

        .productos-container {
            justify-content: center;
        }

        .card {
            width: 100% !important;
            margin: 0.5rem 0;
        }
    }

</style>