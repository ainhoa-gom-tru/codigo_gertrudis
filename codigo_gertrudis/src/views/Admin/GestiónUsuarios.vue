<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';

//creamos las siguientes variables
const roles = ref(['admin', 'desarrollador', 'compi', 'cliente']);
const usuarios = ref([]);
const usuarioEliminar = ref(null);
const usuarioActualizar = ref(null);
const nuevoRol = ref(null);
const paginaInicial = ref(1);
const cantidadUsuarios = ref(4);
const mostrarMensaje = ref(false);
const mensaje = ref('');

//añadimos en la variable usuarios los primeros 5
const usuariosPaginados = computed(() =>{
    const principio = (paginaInicial.value - 1) * cantidadUsuarios.value;
    const final = principio + cantidadUsuarios.value;
    return usuarios.value.slice(principio, final);
})

//vamos a calcular el total de páginas
const totalDePaginas = computed(() => {
    return Math.ceil(usuarios.value.length / cantidadUsuarios.value);
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
function obtenerTodosUsuarios(){
    fetch(ApiUrl + '/usuarios', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Usuarios:', data)
        usuarios.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodosUsuarios();

//funcion para modificar el rol del usuario
function modificarDatos(usuario){
    usuarioActualizar.value = usuario.id;
    nuevoRol.value = usuario.rol;
}

//hacemos una funcion para actualizar los datos del usuario
function actualizarUsuario(){
    fetch(ApiUrl + '/usuarios', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: usuarioActualizar.value,
            rol: nuevoRol.value
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodosUsuarios();
        usuarioActualizar.value = null;
        nuevoRol.value = null;
        console.log('Respuesta:', data)
        mensaje.value = "El usuario ha sido actualizado con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

//hacemos una funcion para validar al usuario
function validarUsuario(id){
    fetch(ApiUrl + '/usuarios', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: id,
            validado: true
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodosUsuarios();
        console.log('Respuesta:', data)
        mensaje.value = "El usuario ha sido validado con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

//funcion para eliminar el usuario
function eliminarUsuario(){
    fetch(ApiUrl + '/usuarios', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: usuarioEliminar.value
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodosUsuarios();
        usuarioEliminar.value = null;
        console.log('Respuesta:', data)
        mensaje.value = "El usuario ha sido eliminado con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

//hacemos una funcion para cerrar el modal
function cerrarModal(){
    usuarioEliminar.value = null;
}

//hacemos una función para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

</script>
<template>
    <div id="body">
        <main>
            <div v-if="mostrarMensaje" class="mensaje">
                <button @click="cerrarMensaje">X</button>
                <i class="bi bi-check2"></i>
                <p class="success">{{ mensaje }}</p>
            </div>
            <h4>Gestión de usuarios</h4>
            <table class="table table-hover">
                <caption>Gestiona los usuarios eliminándolos o cambiándoles su rol.</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Validado</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="cuerpoTabla">
                    <tr v-for="usuario in usuariosPaginados" :key="usuario.id">
                        <th scope="row">{{ usuario.id }}</th>
                        <td>
                            <RouterLink :to="`/detalles/${usuario.usuario}`">{{ usuario.usuario }}</RouterLink>
                        </td>
                        <td>{{ usuario.email }}</td>
                        <td v-if="usuarioActualizar != usuario.id">{{ usuario.rol }}</td>
                        <td v-else>
                            <select v-model="nuevoRol" name="rol">
                                <option v-for="(rol, index) in roles" :key="index" :value="rol">{{ rol }}</option>
                            </select>
                        </td>
                        <td>
                            <button v-if="!usuario.validado" type="button" @click="validarUsuario(usuario.id)" class="btn">
                                <i class="bi bi-check2"></i>
                                Validar
                            </button>
                            <p v-else  :style="estiloVerde"><b>Validado</b></p>
                        </td>
                        <td>
                            <button v-if="usuarioActualizar != usuario.id" type="button" @click="modificarDatos(usuario)" class="btn">
                                <i class="bi bi-pencil-square"></i>
                                Modificar
                            </button>
                            <button v-else @click="actualizarUsuario" class="btn">
                                <i class="bi bi-floppy-fill"></i>
                                Guardar
                            </button>
                            <button type="button" @click="usuarioEliminar = usuario.id" class="btn-eliminar">
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
    <div v-if="usuarioEliminar !=null" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloRojo" class="modal-title fs-5" id="eliminarUsuario">Eliminar usuario</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que quieres eliminar este usuario?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cancelar</button>
                    <button v-on:click="eliminarUsuario" type="button" class="btn btn">Confirmar</button>
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

    .success {
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