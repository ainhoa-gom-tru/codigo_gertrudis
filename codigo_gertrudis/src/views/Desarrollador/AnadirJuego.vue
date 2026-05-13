<script setup>
import { ApiUrl, bordeNatural, bordeRojo, bordeVerde, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const nombre = ref('');
const foto = ref(null);
const dimensionesFoto = ref(null);
const extensionFoto = ref(null);
const archivo = ref(null);
const extensionArchivo = ref(null);
const mostrarMensaje = ref(false);
const mensaje = ref('');

//expresión regular para validar el nombre del archivo
const expresionRegular = /^[A-ZÁÉÍÓÚÑÜ][a-záéíóúñü]+( [A-ZÁÉÍÓÚÑÜ]?[a-záéíóúñü]+)*$/;

//comprobamos que el nombre que nos escriben pasa la validación
const validarNombre = computed(() => {
    if (!expresionRegular.test(nombre.value) && nombre.value.length > 1){
        return false;
    } else if (nombre.value.length === 0){
        return null;
    } else {
        return true;
    }
})

function clickImagen(e){
    foto.value = e.target.files[0];
    console.log(foto.value);

    if (!foto.value){
        dimensionesFoto.value = null;
        extensionFoto.value = null;
        return;
    }

    //obtenemos la extesión del archivo y comprobamos que es webp
    const xtn = foto.value.type;
    if (xtn === 'image/webp'){
        extensionFoto.value = true;
    } else {
        extensionFoto.value = false;
    }

    //creamos un nuevo objeto imagen y leemos las dimensiones
    const img = new Image();
    img.onload = function () {
        if (img.width === 700 && img.height === 700){
            dimensionesFoto.value = true;
        } else {
            dimensionesFoto.value = false;
        }
    };

    img.src = URL.createObjectURL(foto.value);
}

function clickArchivo(e){
    archivo.value = e.target.files[0];

    if (!archivo.value){
        extensionArchivo.value = null;
        return;
    }

    //obtenemos la extensión del archivo y comprobamos que es html
    const xtn = archivo.value.type;

    if (xtn === 'text/html'){
        extensionArchivo.value = true;
    } else {
        extensionArchivo.value = false;
    }
}

//variable para declarar el maximo de bytes permitidos para subir la imagen y el archivo
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

//función para validar el archivo
const tamanoArchivo = computed(() => {

    //para que no salte el error si no hay archivo
    if(!archivo.value){
        return true
    }

    if (archivo.value.size > 200000){
        return false
    } else {
        return true
    }
});

//funcion para añadir un nuevo producto
function anadirJueo(){
    const juegoData = new FormData();
    juegoData.append('nombre', nombre.value);
    juegoData.append('foto', foto.value);
    juegoData.append('archivo', archivo.value);

    fetch(ApiUrl + '/juegos', {
        method: 'POST',
        credentials: 'include',
        body: juegoData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta:', data);
        //vaciamos el formulario
        nombre.value = '';
        foto.value = null;
        dimensionesFoto.value = null;
        extensionFoto.value = null;
        archivo.value = null;
        extensionArchivo.value = null;
    })
    .catch(error => console.error('Error:', error));
    mostrarMensaje.value = true;
    mensaje.value = '¡Has añadido un nuevo juego con éxito!';
}

</script>

<template>
    <div id="body">
        <main>
            <div v-if="mostrarMensaje" class="mensaje" :class="mensaje.includes('éxito') ? 'success' : 'error'">
                <button @click="cerrarMensaje">X</button>
                <i v-if="mensaje.includes('éxito')" class="bi bi-check2"></i>
                <i v-else class="bi bi-x-octagon-fill"></i>
                <p class="texto-mensaje">{{ mensaje }}</p>
            </div>
            <form @submit.prevent="anadirJueo">
                <div class="mb-2">
                    <label :style="validarNombre === null ? estiloNatural : !validarNombre ? estiloRojo : estiloVerde" for="nombre" class="form-label" aria-label="Nombre del juego">Nombre
                        <span v-if="validarNombre === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarNombre !== null && !validarNombre" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="validarNombre" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-pencil-fill"></i>
                        <input :style="validarNombre === null ? bordeNatural : !validarNombre ? bordeRojo : bordeVerde" v-model="nombre" type="text" class="form-control" id="nombre" aria-describedby="Nombre del juego" placeholder="Ej: Recuerda las parejas" required>
                    </div>
                    <p v-if="validarNombre !== null && !validarNombre" :style="estiloRojo">* Nombre no válido, recuerda que la primera debe ser mayúscula</p>
                </div>
                <div class="mb-2">
                    <label :style="foto == null ? estiloNatural : tamanoImagen ? estiloVerde : estiloRojo" for="foto" aria-label="Subir foto del producto">Sube una imagen de la miniatura del juego
                        <span v-if="foto == null || !tamanoImagen" :style="!tamanoImagen ? estiloRojo : estiloNatural">*</span>
                        <i v-else :style="estiloVerde" class="fa fa-check" aria-hidden="true"></i>
                    </label>
                    <input @change="clickImagen($event)" type="file" name="foto" id="fotoProducto" accept="image/webp" required>
                    <p v-if="foto && !tamanoImagen" :style="estiloRojo">* El tamaño de la imagen supera el máximo (2MB)</p>
                    <p v-if="foto && !extensionFoto" :style="estiloRojo">* La extensión no es válida, solo archivos .webp</p>
                    <p v-if="foto && !dimensionesFoto" :style="estiloRojo">* La imagen no es válida, solo tamaño de 700x700px</p>
                </div>
                <div class="mb-2">
                    <label :style="archivo == null ? estiloNatural : tamanoArchivo && extensionArchivo ? estiloVerde : estiloRojo" for="archivo" aria-label="Subir archivo del juego">Sube el archivo del juego
                        <span v-if="archivo == null || !tamanoArchivo || !extensionArchivo" :style="!tamanoArchivo || !extensionArchivo ? estiloRojo : estiloNatural">*</span>
                        <i v-else :style="estiloVerde" class="fa fa-check" aria-hidden="true"></i>
                    </label>
                    <input @change="clickArchivo($event)" type="file" name="archivo" id="archivoJuego" accept=".html"required>
                    <p v-if="archivo && !tamanoArchivo" :style="estiloRojo">* El tamaño del archivo supera el máximo (200KB)</p>
                    <p v-if="archivo && !extensionArchivo" :style="estiloRojo">* La extensión no es válida, solo archivos .html</p>
                </div>
                <button type="submit" class="btn btn-primary" aria-label="Añadir juego" id="enviar" :disabled="!validarNombre || !tamanoImagen || !dimensionesFoto || !extensionFoto || !tamanoArchivo || !extensionArchivo">Añadir</button>
            </form>
        </main>
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
        margin: auto;
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

    form{
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    label{
        font-weight: 600;
    }

    .input{
        position: relative;
        margin-top: 0.3rem;
    }

    .input i{
        position: absolute;
        top: 50%;
        left: 0.6rem;
        transform: translateY(-50%);
        color: #fcbf00;
        opacity: 0.7;
    }

    .input input{
        width: 100%;
        padding: 0.5rem 0.5rem 0.5rem 2rem;
        border-radius: 0.5rem;
        border: 1px solid #fcbf00;
    }

    input[type="file"]{
        margin-top: 0.5rem;
        padding: 0.4rem;
    }

    input::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    input:focus{
        outline: none;
        border-color: #ff8800;
        box-shadow: 0 0 0 2px rgba(255,136,0,0.2);
    }

    p{
        font-size: 0.85rem;
        margin-top: 0.2rem;
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

</style>