<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';

//creamos las siguientes variables
const roles = ref(['admin', 'desarrollador', 'compi', 'cliente']);
const productos = ref([]);
const productoEliminar = ref(null);
const productoActualizar = ref(null);
const nuevoNombre = ref(null);
const nuevoPrecio = ref(null);
const nuevasUnidades = ref(null);
const paginaInicial = ref(1);
const cantidadProductos = ref(4);
const mostrarMensaje = ref(false);
const mensaje = ref('');
const tipoMensaje = ref('success');

//añadimos en la variable usuarios los primeros 5
const productosPaginados = computed(() =>{
    const principio = (paginaInicial.value - 1) * cantidadProductos.value;
    const final = principio + cantidadProductos.value;
    return productos.value.slice(principio, final);
})

//vamos a calcular el total de páginas
const totalDePaginas = computed(() => {
    return Math.ceil(productos.value.length / cantidadProductos.value);
})

//hacemos las funciones de ir a pagian siguiente e ir a la anteiro
function paginaAnterior(){
    if(paginaInicial.value > 1){
        paginaInicial.value--;
    }
}
function paginaSiguiente(){
    if(paginaInicial.value < totalDePaginas.value){
        paginaInicial.value++;
    }
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

//funcion para modificar los datos del producto
function modificarDatos(producto){
    productoActualizar.value = producto.id;
    nuevoNombre.value = producto.nombre;
    nuevoPrecio.value = producto.precio;
    nuevasUnidades.value = producto.unidades;
}

//hacemos una funcion para actualizar los datos del usuario
function actualizarProducto(){

    //convertimos a numero
    const precio = Number(nuevoPrecio.value);
    const unidades = Number(nuevasUnidades.value);

    // validamos que el precio no sea menor de 1 ni las unidades menor de 0 o mayor de 100
    if (nuevoPrecio.value === null || nuevasUnidades.value === null || precio < 1 || unidades < 0 || unidades > 100) {
        mensaje.value = "El precio y/o las unidades no son válidos";
        mostrarMensaje.value = true;
        tipoMensaje.value = "error";
        return;
    }

    fetch(ApiUrl + '/productos', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: productoActualizar.value,
            nombre: nuevoNombre.value,
            precio,
            unidades
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodosProductos();
        productoActualizar.value = null;
        nuevoNombre.value = null;
        nuevoPrecio.value = null;
        nuevasUnidades.value = null;
        mensaje.value = "¡El producto ha sido actualizado con éxito!";
        mostrarMensaje.value = true;
        tipoMensaje.value = "success";
        console.log('Respuesta:', data)
    })
    .catch(error => console.error('Error:', error));
}

//funcion para eliminar el usuario
function eliminarProducto(){
    fetch(ApiUrl + '/productos', {
        method: 'DELETE',
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
    <div id="body">
        <main>
            <div v-if="mostrarMensaje" class="mensaje" :class="tipoMensaje">
                <button @click="cerrarMensaje">X</button>
                <i class="bi bi-check2"></i>
                <p class="texto-mensaje">{{ mensaje }}</p>
            </div>
            <h4>Gestión de productos</h4>
            <table class="table table-hover">
                <caption>Gestiona los productos eliminándolos o cambiándoles su rol.</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Fecha de registro</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="cuerpoTabla">
                    <tr v-for="producto in productosPaginados" :key="producto.id">
                        <th scope="row">{{ producto.id }}</th>
                        <td v-if="productoActualizar != producto.id">{{ producto.nombre }}</td>
                        <td v-else>
                            <input type="text" v-model="nuevoNombre" class="form-control" id="nombre" aria-describedby="Nombre producto">
                        </td>
                        <td v-if="productoActualizar != producto.id">{{ producto.precio }}€</td>
                        <td v-else>
                            <input type="number" v-model="nuevoPrecio" class="form-control" id="precio" aria-describedby="Precio producto" min="1">
                        </td>
                        <td v-if="productoActualizar != producto.id">{{ producto.unidades }}</td>
                        <td v-else>
                            <input type="number" v-model="nuevasUnidades" class="form-control" id="unidades" aria-describedby="Unidades producto" min="0" max="100">
                        </td>
                        <td>{{ producto.fecha_registro }}</td>
                        <td>
                            <button v-if="productoActualizar != producto.id" type="button" @click="modificarDatos(producto)" class="btn">
                                <i class="bi bi-pencil-square"></i>
                                Modificar
                            </button>
                            <button v-else @click="actualizarProducto" class="btn">
                                <i class="bi bi-floppy-fill"></i>
                                Guardar
                            </button>
                            <button type="button" @click="productoEliminar = producto.id" class="btn-eliminar">
                                <i class="bi bi-trash3-fill"></i>
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot></tfoot>        
            </table>
            <nav aria-label="Navegación de páginas" class="pagination">
                <button @click="paginaAnterior" :disabled="paginaInicial === 1" class="page-link" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </button>
                    <ul v-for="pagina in totalDePaginas" :key="pagina">
                        <li :class="{activo: paginaInicial === pagina}">{{ pagina }}</li>
                    </ul>
                <button @click="paginaSiguiente" :disabled="paginaInicial === totalDePaginas" class="page-link" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </nav>
        </main>
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
                    <button v-on:click="eliminarProducto" type="button" class="btn btn">Confirmar</button>
                </div>     
            </div>
        </div>
    </div>
</template>

<style scoped>

    #body{
        display: flex;
        margin-top: 2%;
        gap: 2%;
    }

    main{
        width: 90%;
        margin-left: 5%;
        margin-top: 2%;
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

    main h4, table{
        color: #fcbf00;
    }

    caption{
        color: #ff8800;
        font-style: italic;
        opacity: 0.8;
    }

    table{
        width: 100%;
        border-collapse: collapse;
    }

    tr th{
        color: #ff8800;
    }

    td a{
        color: black;
        text-decoration: none;
    }

    .btn {
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.3s ease;
        cursor: pointer;
        color: black;
    }

    .btn:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
        color: white;
    }

    .btn-eliminar{
        background-color: #ff8800;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-top: 0.5rem;
        color: white;
        margin-left: 4%;
    }

    .btn-eliminar:hover{
        background-color: #fcbf00;
        transform: translateY(-0.125rem);
        color: black;
    }

    select{
        background-color: #fcbf00;
        border: none;
        padding: 0.4rem;
        border-radius: 0.4rem;
        font-weight: bold;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fcbf00;
        margin-top: 1rem;
    }

    .pagination button {
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        border: none;
        padding: 0.3rem 0.6rem;
        border-radius: 0.4rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .pagination button:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
        color: white;
    }

    .pagination button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .pagination ul{
        padding: 0;
        margin: 0;
    }

    .pagination li{
        list-style: none;
        display: inline-block;
        margin: 0 0.3rem;
        padding: 0.3rem 0.6rem;
        border-radius: 0.4rem;
    }

    .pagination .activo {
        color: #ff8800;
        font-weight: bold;
        font-size: 1.05rem;
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

</style>