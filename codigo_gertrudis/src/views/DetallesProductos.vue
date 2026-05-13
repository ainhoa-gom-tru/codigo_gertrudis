<script setup>
import { ApiUrl, estiloNatural } from '@/main';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
//obtenemos de la url el nombre de usuario para imprimir todos los detalles
const nombreProducto = route.params.nombre;
console.log(nombreProducto);

//creamos las siguientes variables
const producto = ref(null);
const valoraciones = ref([]);
const mediaValoracion = ref(0);
const comentario = ref(null);
const puntuacion = ref(0);
const exito = ref(false);
const error = ref(false);
const mostrarModal = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre en caso de que hubiera de usuario logueado
const nombreUsuario = ref('');

if(usuarioLogueado){
  nombreUsuario.value = usuarioLogueado.usuario;
}

//funcion para obtener todos los detalles de la entrada
function obtenerDetallesEntrada(){
    fetch(ApiUrl + '/productos', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        const product = data.find(p => p.nombre === nombreProducto);
        producto.value = product;
        obtenerTodasValoraciones();
        console.log('Entrada:', producto.value)
    })
    .catch(error => console.error('Error:', error));
}

obtenerDetallesEntrada();

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
        valoraciones.value = data.filter(v => v.producto_id == producto.value.id);
        calcularMediaValoracion();
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodasValoraciones();

//function para obtener la media de un producto
function calcularMediaValoracion(){
    if(valoraciones.value.length === 0){
        mediaValoracion.value = 0;
        return;
    }

    const suma = valoraciones.value.reduce((acc, v) => {
        return acc + v.puntuacion;
    }, 0);

    mediaValoracion.value = (suma / valoraciones.value.length).toFixed(1);
}

//function para añadir una valoración al producto
function anadirValoracion(){
    if(!usuarioLogueado){
        error.value = true;
        return;
    }

    fetch(ApiUrl + '/valoraciones', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            producto_id: producto.value.id,
            comentario: comentario.value,
            puntuacion: puntuacion.value,
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Valoración:', data);
        puntuacion.value = 0;
        comentario.value = null;
        mostrarModal.value = false;
        obtenerTodasValoraciones();
    })
    .catch(error => {
        console.error('Error:', error)
    });
}

//hacemos una funcion para cerrar el modal
function cerrarModal(){
    mostrarModal.value = false;
}

</script>

<template>
    <div id="body">
        <main>
            <RouterLink to="/tienda">
                <i class="bi bi-arrow-left"></i>
                Volver
            </RouterLink>
            <div class="producto">
                <img :src="`http://localhost:8001/productos/${producto.foto}`" alt="Foto del producto">
                <div class="info">
                    <h2>{{ producto.nombre }}</h2>
                    <div id="valoracion">
                        <p>{{ mediaValoracion }}</p>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <h6>{{ producto.precio.toString().replace('.', ',') }}€</h6>
                    <p>{{ producto.descripcion }}</p>
                </div>
                <button @click="añadirCarrito(producto)">
                    <i class="bi bi-bag-plus-fill"></i>
                    Añadir al carrito
                </button>
            </div>
            <h4>Todas las reseñas</h4>
            <div v-if="valoraciones.length > 0"></div>
            <p v-else>Aún no hay reseñas de este producto</p>
            <div v-if="valoraciones.length > 0" id="comentariosValoraciones">
            <div v-for="valoracion in valoraciones" class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">{{ valoracion.cliente_usuario }}</h5>
                    <div id="estrellasPuntuacion">
                        <span v-for="estrella in valoracion.puntuacion">
                            <i class="bi bi-star-fill"></i>
                        </span>
                    </div>
                        <p class="card-text">{{ valoracion.comentario }}</p>
                    </div>
                </div>
            </div>
            <button @click="mostrarModal = true">
                <i class="bi bi-pencil-square"></i>
                Añadir reseña
            </button>
        </main>
    </div>
    <div v-if="mostrarModal" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloNatural" class="modal-title fs-5" id="anadirValoración">Valorar producto</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-2">
                            <button type="button" v-for="estrella in 5" @click="puntuacion = estrella">
                                <i v-if="estrella <= puntuacion" class="bi bi-star-fill"></i>
                                <i v-else class="bi bi-star"></i>
                            </button>
                        </div>
                        <div class="mb-2">
                            <textarea v-model="comentario" name="comentario" id="comentario" placeholder="Escribe un comentario..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cancelar</button>
                    <button type="button" @click="anadirValoracion" :disabled="puntuacion === 0">Valorar</button>
                </div>     
            </div>
        </div>
    </div>
</template>

<style scoped>

    #body{
        margin: 4rem;
    }

    .mensaje{
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: linear-gradient(135deg, #1f9d74, #168a64);
        color: #d1ffe9;
        padding: 1.2rem 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        margin-bottom: 1.5rem;
    }

    .mensaje i{
        font-size: 1.5rem;
        color: #a7ffda;
        background: rgba(255,255,255,0.15);
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .success{
        margin: 0;
        font-weight: 500;
    }

    .mensaje button{
        position: absolute;
        right: 1rem;
        top: 1rem;
        background: transparent;
        border: none;
        color: #d1ffe9;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
    }

    main a{
        color: #fcbf00;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.2rem;
    }

    .producto{
        display: flex;
        align-items: flex-start;
        gap: 2rem;
        margin-top: 2rem;
    }

    .producto img{
        width: 30rem;
        height: 30rem;
        object-fit: cover;
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    #valoracion p{
        margin-top: 1rem;
    }

    .producto .info{
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .producto button, button{
        align-self: flex-start;
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.3s ease;
        color: black;
        white-space: nowrap;
    }

    .producto button:hover, button:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

    #valoracion{
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: #ff8800;
        font-weight: bold;
    }

    #valoracion i{
        color: #fcbf00;
    }

    #comentariosValoraciones .card{
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        border: none;
        padding: 1rem;
        background: white;
    }

    #comentariosValoraciones .card-title{
        color: #fcbf00;
        font-weight: bold;
    }

    #comentariosValoraciones .card-text{
        color: #fcbf00;
    }

    #estrellasPuntuacion{
        display: flex;
        gap: 0.2rem;
        margin-bottom: 0.5rem;
    }

    #estrellasPuntuacion i{
        color: #ff8800;
    }

    main h4{
        color: #fcbf00;
        margin-top: 2rem;
    }

    #comentariosValoraciones{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 2rem 0rem;
    }

    .modal-overlay{
        position: fixed;
        inset: 0;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-container{
        width: 90%;
        max-width: 25rem;
        border-radius: 1rem;
        background: white;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        padding: 1.5rem;
    }

    .modal-header{
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }

    .modal-body{
        padding: 1rem 0;
    }

    .modal-footer{
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        padding-top: 1rem;
    }

    .modal-body form{
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .modal-body .mb-2{
        display: flex;
        flex-direction: flex;
        gap: 0.3rem;
    }

    .modal-body i{
        font-size: 2rem;
    }

    .modal-body label{
        font-weight: 600;
        color: #fcbf00;
    }

    .modal-body textarea{
        width: 100%;
        min-height: 80px;
        resize: none;
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #fcbf00;
        font-family: inherit;
    }

    .modal-body textarea::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    .modal-body button{
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        color: #fcbf00;
        transition: all 0.2s ease;
    }

    .modal-body button{
        color: #ff8800;
        transform: translateY(-2px);
    }

    .modal-footer button{
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .modal-footer button:first-child{
        background-color: #ff8800;
        color: white;
    }

    .modal-footer button:first-child:hover{
        background-color: #fcbf00;
        color: black;
        transform: translateY(-0.125rem);
    }

    .modal-footer button:last-child{
        background-color: #fcbf00;
        color: black;
    }

    .modal-footer button:last-child:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

    .modal-footer button:disabled{
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-close{
        background-color: transparent;
    }

</style>