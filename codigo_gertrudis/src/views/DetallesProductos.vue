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
//funcion para obtener todos los usuarios
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
            <div>
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
                <button @click="añadirCarrito(producto)">Añadir al carrito</button>
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
            <button @click="mostrarModal = true">Añadir reseña</button>
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

<style scoped></style>