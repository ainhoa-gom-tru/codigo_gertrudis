<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import { ref } from 'vue';

//crea las siguientes variables
const productos = ref([]);
const valoraciones = ref([]);
const entradas = ref([]);
const error = ref(false);
const exito = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');
const validadoUsuario = ref(false);

if(usuarioLogueado){
  nombreUsuario.value = usuarioLogueado.usuario;
  validadoUsuario.value = usuarioLogueado.validado;
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
        error.value = true;
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

//función para cerrar el modal
function cerrarModal(){
    error.value = false;
    exito.value = false;
}

</script>

<template>

  <section id="primera">
    <div class="contenido">
      <button>
        <span><RouterLink to="/conocenos">Pincha para conocer la historia</RouterLink></span>
        <i class="bi bi-arrow-right"></i>
      </button>
    </div>
    <video id="medio" autoplay loop>
      <source src="/video_home.webm" type="video/webm">
      <source src="/video_home.mp4" type="video/mp4">
      Tu navegador no soporta el vídeo.
    </video>
  </section>
  <section id="segunda">
    <div id="todosProductos">
        <div v-for="producto in productos.slice(0, 4)" class="card" style="width: 18rem;">
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
  </section>
  <section id="tercera">
    <div v-for="entrada in entradas.slice(0, 1)">
        <div>
            <h4>{{ entrada.titulo }}</h4>
            <p>{{ entrada.texto }}</p>
            <button>
                <RouterLink :to="`/detalles-entrada/${entrada.titulo}`">
                    <i class="bi bi-plus-lg"></i>
                    Leer más
                </RouterLink>
            </button>
        </div>
        <img :src="`http://localhost:8001/blog/${entrada.foto}`" class="card-img-top" alt="Foto de la entrada del blog">
    </div>
  </section>

</template>

<style scoped>

  #primera{
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
  }

  #medio{
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.3;
  }

  .contenido{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
  }

  button{
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: #fcbf00;
    font-weight: bold;
    border: none;
    border-radius: 1rem;
    padding: 0.9rem;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    cursor: pointer;
    color: black;
  }

  button:hover{
    background-color: #ff8800;
    transform: translateY(-0.125rem);
    color: white;
  }

  a{
    text-decoration: none;
    color: inherit;
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

    .btn-close{ 
        background-color: transparent;
    }

    .btn-close:hover {
        transform: none;
        background-color: transparent;
    }

    #tercera{
        margin: 8rem 4rem 4rem 4rem;
    }

    #tercera div{
        display: flex;
    }

    #tercera div div{
        display: block;
    }

    #tercera div div h4{
        color: #fcbf00;
        font-size: 2rem;
        font-weight: bold;
        margin: 3rem 2rem 2rem 2rem;
    }

    #tercera div div p{
        margin: 2rem;
        line-height: 1.4rem;
        max-height: 4.5rem;
        overflow: hidden;
        position: relative;
        padding-right: 1.2rem;
    }

    #tercera div div button{
        margin: 0rem 2rem;
    }

    #tercera div img{
        width: 20rem;
        border-radius: 2rem;
        margin-right: 4rem;
    }

    @media (max-width: 768px) {

        #primera {
            height: 70vh;
        }

        .contenido {
            width: 90%;
            text-align: center;
        }

        button {
            padding: 0.7rem;
            font-size: 0.9rem;
        }

        #todosProductos {
            flex-direction: column;
            align-items: center;
            margin-left: 0;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            width: 90% !important;
            margin: 0.8rem 0;
        }

        .card img {
            height: 180px;
        }

        #valoracion {
            top: 5%;
            right: 6%;
            font-size: 0.85rem;
        }

        .modal-container {
            width: 92%;
            padding: 1rem;
        }

        .modal-body {
            font-size: 0.9rem;
        }

        .modal-footer {
            flex-direction: column;
        }

        .modal-footer button {
            width: 100%;
        }

        .btn-close {
            transform: none;
        }

        #tercera {
            margin: 4rem 1rem;
        }

        #tercera div {
            flex-direction: column;
            align-items: center;
        }

        #tercera div img {
            width: 100%;
            max-width: 18rem;
            margin: 1rem 0;
        }

        #tercera div div {
            text-align: center;
        }

        #tercera div div h4 {
            font-size: 1.4rem;
            margin: 1rem;
        }

        #tercera div div p {
            margin: 1rem;
            font-size: 0.9rem;
        }

        #tercera div div button {
            margin: 1rem auto;
        }
    }

</style>