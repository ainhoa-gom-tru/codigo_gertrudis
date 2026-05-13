<script setup>
import { ApiUrl, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const fotos = ref([]);
const fotoEliminar = ref(null);
const activarModal = ref(false);
const foto = ref(null);
const extension = ref(null);
const mensaje = ref('');
const mostrarMensaje = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el id en caso de que hubiera de usuario logueado
const idUsuario = ref('');

if(usuarioLogueado){
  idUsuario.value = usuarioLogueado.id;
}

//funcion para obtener todas las fotos de la galeria
function obtenerTodasFotos(){
    fetch(ApiUrl + '/galeria', {
        method: 'GET',
        credentials: 'include',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Fotos:', data)
        fotos.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodasFotos();

function clickImagen(e){
    foto.value = e.target.files[0];
    console.log(foto.value);

    if (!foto.value){
        extension.value = null;
        return;
    }

    //obtenemos la extesión del archivo y comprobamos que es webp
    const xtn = foto.value.type;
    if (xtn === 'image/webp'){
        extension.value = true;
    } else {
        extension.value = false;
    }
}

//variable para declarar el maximo de bytes permitidos para subir la imagen
const maxSizeBytes = 2 * 1024 * 1024;

//función para validar la imagen
const tamanoImagen = computed(() => {
    //para que no salte el error si no hay foto
    if(!foto.value){
        return true
    }
    if (foto.value.size > maxSizeBytes){
        return false
    } else {
        return true
    }
});

//function para añadir la foto a la galeria
function anadirFoto(){
    
    const fotoGaleria = new FormData();
    fotoGaleria.append('foto', foto.value);

    fetch(ApiUrl + '/galeria', {
        method: 'POST',
        credentials: 'include',
        body: fotoGaleria
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        //comprobamos que no haya errores
        if (data.error) {
            mensaje.value = "Ha ocurrido un error al subir la imagen";
            mostrarMensaje.value = true;
            //vaciamos el formulario
            foto.value = null;
            extension.value = null;
            return;
        }

        console.log('Respuesta:', data);
        //vaciamos el formulario
        foto.value = null;
        extension.value = null;
        obtenerTodasFotos();
    })
    .catch(error => console.error('Error:', error));
    activarModal.value = false;
    mostrarMensaje.value = true;
    mensaje.value = '¡Has añadido tu imagen con éxito!';
}

//function para eliminar una foto
function eliminarFoto(){
    fetch(ApiUrl + '/galeria', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
            id: fotoEliminar.value
        })
    })
    .then(response => response.json())
    .then(data => {
        obtenerTodasFotos();
        fotoEliminar.value = null;
        console.log('Respuesta:', data)
        mensaje.value = "La fotografía ha sido eliminada con éxito";
        mostrarMensaje.value = true;
    })
    .catch(error => console.error('Error:', error));
}

//function para cerrar el modal
function cerrarModal(){
    activarModal.value = false;
    fotoEliminar.value = null;
}

//funcion para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

</script>

<template>
    <main>
        <div v-if="mostrarMensaje" class="mensaje" :class="mensaje.includes('éxito') ? 'success' : 'error'">
            <button @click="cerrarMensaje">X</button>
            <i v-if="mensaje.includes('éxito')" class="bi bi-check2"></i>
            <i v-else class="bi bi-x-octagon-fill"></i>
            <p class="texto-mensaje">{{ mensaje }}</p>
        </div>
        <button @click="activarModal = true" class="anadir">
            <i class="bi bi-image-fill"></i>
            Añadir fotografía
        </button>
        <div class="galeria">
            <div v-for="foto in fotos">
                <img :src="`http://localhost:8001/album/${foto.foto}`">
                <button v-if="foto.usuario_id === idUsuario" class="btn-eliminar" @click="fotoEliminar = foto.id">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
        <div v-if="activarModal" class="modal-overlay " tabindex="-1" role="dialog">
            <div class="modal-container" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="eliminarUsuario">Añadir foto</h1>
                        <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                    </div>
                    <form @submit.prevent="anadirFoto">
                        <div class="mb-2">
                            <label :style="foto == null ? estiloNatural : tamanoImagen ? estiloVerde : estiloRojo" for="foto" aria-label="Subir tu foto">Sube tu fotografía
                                <span v-if="foto == null || !tamanoImagen" :style="!tamanoImagen ? estiloRojo : estiloNatural">*</span>
                                <i v-else :style="estiloVerde" class="fa fa-check" aria-hidden="true"></i>
                            </label>
                            <input @change="clickImagen($event)" type="file" name="foto" id="fotoProducto" accept="image/webp" required>
                            <p v-if="foto && !tamanoImagen" :style="estiloRojo">* El tamaño de la imagen supera el máximo (2MB)</p>
                            <p v-if="foto && !extension" :style="estiloRojo">* La extensión no es válida, solo archivos .webp</p>
                        </div>
                        <button type="submit" class="btn btn-primary" aria-label="Añadir foto" id="enviar" :disabled="!tamanoImagen || !extension">Subir</button>
                    </form>  
                </div>
            </div>
        </div>
        <div v-if="fotoEliminar !=null" class="modal-overlay " tabindex="-1" role="dialog">
            <div class="modal-container" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 :style="estiloRojo" class="modal-title fs-5" id="eliminarUsuario">Eliminar fotografía</h1>
                        <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que quieres eliminar esta foto?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cancelar</button>
                        <button v-on:click="eliminarFoto" type="button" class="anadir">Confirmar</button>
                    </div>     
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>

    main{
        margin: 2rem;
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

    .anadir{
        width: auto;
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
        cursor: pointer;
        color: black;
        margin-top: 1rem;
        margin-left: auto;
        display: flex;
    }

    .anadir:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
        color: white;
    }

    .galeria {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
    padding: 1rem;
    margin-top: 1.5rem;
}

.galeria div {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    background: #f5f5f5;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.galeria div:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
    cursor: pointer;
}

.galeria img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease, filter 0.3s ease;
}

.galeria div:hover img {
    transform: scale(1.05);
    filter: brightness(0.9);
}

    #enviar{
        width: 100%;
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
        cursor: pointer;
        color: black;
        margin-top: 1rem;
    }

    #enviar:hover{
        background-color: #ff8800;
        transform: translateY(-0.125rem);
        color: white;
    }

    #enviar:disabled{
        opacity: 0.5;
        cursor: not-allowed;
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