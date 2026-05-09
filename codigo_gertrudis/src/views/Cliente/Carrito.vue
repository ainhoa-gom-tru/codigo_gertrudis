<script setup>
import { ApiUrl, estiloRojo } from '@/main';
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';

//crea las siguientes variables
const productos = ref([]);
const productoEliminar = ref(null);
const mostrarMensaje = ref(false);
const mensaje = ref('');
const tipoMensaje = ref('success');
const router = useRouter();

//funcion para obtener todos los productos
function obtenerTodosProductos(){
    fetch(ApiUrl + '/carrito', {
        method: 'GET',
        credentials: 'include',
    })
    .then(response => response.json())
    .then(data => {
        productos.value = data;
        console.log('Productos:', data)
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodosProductos();

//funcion para actualizar la cantidad del producto del carrito
function actualizarCantidad(producto){

    //nos aseguramos de que el numero de unidades sea mayor que uno y menor que el numero de stock
    if (producto.cantidad < 1 || producto.cantidad > producto.unidades) {
        mensaje.value = `La cantidad debe estar entre 1 y ${producto.unidades}`;
        tipoMensaje.value = 'error';
        mostrarMensaje.value = true;
        return;
    }

    fetch(ApiUrl + '/carrito', {
        method: 'PUT',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: producto.id,
            cantidad: producto.cantidad
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Actualizado:', data)
        obtenerTodosProductos();
    })
    .catch(err => console.error(err));
}

//calculamos el total de mi carrito
const totalCarrito = computed(() => {
    return productos.value.reduce((acc, producto) => {
        return acc + parseFloat(producto.total);
    }, 0).toFixed(2);
});

//funcion para eliminar el producto del carrito
function eliminarProducto(){
    fetch(ApiUrl + '/carrito', {
        method: 'DELETE',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: productoEliminar.value
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodosProductos();
        productoEliminar.value = null;
        console.log('Respuesta:', data)
        mensaje.value = "El producto ha sido eliminado con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

function irAPago() {
    router.push('/pasarela-pago');
}

//hacemos una funcion para cerrar el modal
function cerrarModal(){
    productoEliminar.value = null;
}

//hacemos una función para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

</script>

<template>
    <h4>Lista de productos</h4>
    <p v-if="productos.length === 0" class="carrito" :style="estiloRojo">Aún no tienes productos en el carrito</p>
    <div v-else class="carrito">
        <div v-if="mostrarMensaje" class="mensaje" :class="tipoMensaje">
            <button @click="cerrarMensaje">X</button>
            <i class="bi bi-check2"></i>
            <p class="texto-mensaje">{{ mensaje }}</p>
        </div>
        <div class="card" v-for="producto in productos">
            <h5 class="card-header">{{producto.nombre}}</h5>
            <div class="card-body">
                <img :src="`http://localhost:8001/productos/${producto.foto}`" class="card-img-top" alt="Foto del producto">
                <div>
                    <p class="card-text">{{ producto.descripcion }}</p>
                    <p>{{ producto.total }}€</p>
                </div>
                <div class="acciones">
                    <input type="number" v-model="producto.cantidad" @change="actualizarCantidad(producto)" min="1" :max="producto.unidades">
                    <button class="btn-eliminar" @click="productoEliminar = producto.id">
                        <i class="bi bi-trash3-fill"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        <p class="total"><b>Total:</b> {{ totalCarrito }}€</p>
        <button class="btn-pedido" @click="irAPago">
            <i class="bi bi-bag-check-fill"></i>
            Realizar pedido
        </button>
    </div>
    <div v-if="productoEliminar !=null" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloRojo" class="modal-title fs-5" id="eliminarProducto">Eliminar producto</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que quieres eliminar este producto?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cancelar</button>
                    <button v-on:click="eliminarProducto" type="button" class="btn-confirmar">Confirmar</button>
                </div>     
            </div>
        </div>
    </div>
</template>

<style scoped>

    h4 {
        color: #fcbf00;
        margin: 2rem 3rem;
    }

    .mensaje {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 1.2rem 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        margin-bottom: 1.5rem;
    }

    .mensaje.success {
        background: linear-gradient(135deg, #1f9d74, #168a64);
        color: #d1ffe9;
    }

    .mensaje.error {
        background: linear-gradient(135deg, #8b2c2c, #6e1f1f);
        color: #ffdede;
    }

    .mensaje i {
        font-size: 1.5rem;
        background: rgba(255,255,255,0.15);
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .texto-mensaje {
        margin: 0;
        font-weight: 500;
    }

    .mensaje button {
        position: absolute;
        right: 1rem;
        top: 1rem;
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
        color: inherit;
    }

    .carrito {
        margin: 0rem 3rem;
    }

    .card {
        margin-bottom: 1.5rem;
        border-radius: 1rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        padding: 0.8rem;
    }

    .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        padding: 1rem;
        width: 100%;
    }

    .card-body img {
        width: 9rem;
        height: 9rem;
        object-fit: cover;
        border-radius: 1rem;
        flex-shrink: 0;
    }

    .card-body > div:nth-of-type(1) {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.3rem;
    }

    .acciones {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-right: 2rem;
    }

    .acciones input{
        margin-bottom: 1rem;
    }

    .card-text {
        margin: 0;
        color: black;
    }

    .card-body p {
        margin: 0;
    }

    input {
        width: 4.5rem;
        padding: 0.2rem;
        border: 1px solid #fcbf00;
        border-radius: 0.4rem;
        outline: none;
    }

    input:focus {
        border-color: #ff8800;
    }

    .btn-eliminar {
        background-color: #ff8800;
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        cursor: pointer;
        transition: 0.2s ease;
        white-space: nowrap;
    }

    .btn-eliminar:hover {
        background-color: #fcbf00;
        color: black;
        transform: translateY(-2px);
    }

    .total {
        margin-top: 2rem;
        font-size: 1.2rem;
        text-align: right;
        color: black;
    }

    .btn-pedido {
        margin-top: 0.5rem;
        width: 100%;
        background-color: #fcbf00;
        color: #000;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.7rem;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .btn-pedido:hover {
        background-color: #ff8800;
        color: #fff;
        transform: translateY(-2px);
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

    .btn-confirmar {
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-confirmar:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

</style>