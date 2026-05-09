<script setup>
import { ApiUrl, bordeNatural, bordeRojo, bordeVerde, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const titulo = ref('');
const texto = ref('');
const categorias = ref(['arte', 'viajes', 'moda', 'música']);
const nuevaCategoria = ref('');
const foto = ref(null);
const dimensiones = ref(null);
const extension = ref(null);
const mostrarMensaje = ref(false);
const mensaje = ref('');

//creamos una variable para almacenar una expresión regular que valide el título del blog
const expresionRegular = /^[A-ZÁÉÍÓÚÑ][a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]*(?:\s[a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]+)*$/;

//comprobamos que el título que nos escriben pasa la validación
const validarTítulo = computed(() => {
    if (!expresionRegular.test(titulo.value) && titulo.value.length > 1){
        return false;
    } else if (titulo.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//comprobamos que la categoría seleccionada es válida
const validarCategoria = computed(() => {
    if (!categorias.value.includes(nuevaCategoria.value) && nuevaCategoria.value.length > 0){
        return false;
    } else if (nuevaCategoria.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//creamos una expresión regular para el texto
const expresionRegularTexto = /^[\s\S]+$/;

//comprobamos que la descripcion que nos escriben pasa la validación
const validarTexto = computed(() => {
    if (!expresionRegularTexto.test(texto.value) && texto.value.length > 1){
        return false;
    } else if (texto.value.length === 0){
        return null;
    } else {
        return true;
    }
})

function clickImagen(e){
    foto.value = e.target.files[0];
    console.log(foto.value);

    if (!foto.value){
        dimensiones.value = null;
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

    //creamos un nuevo objeto imagen y leemos las dimensiones
    const img = new Image();
    img.onload = function () {
        if (img.width === 700 && img.height === 700){
            dimensiones.value = true;
        } else {
            dimensiones.value = false;
        }
    };

    img.src = URL.createObjectURL(foto.value);
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


//function para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

//funcion para añadir un nuevo producto
function anadirEntradaBlog(){
    const entradaData = new FormData();
    entradaData.append('titulo', titulo.value);
    entradaData.append('texto', texto.value);
    entradaData.append('categoria', nuevaCategoria.value);
    entradaData.append('foto', foto.value);

    fetch(ApiUrl + '/blog', {
        method: 'POST',
        credentials: 'include',
        body: entradaData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta:', data);
        //vaciamos el formulario
        titulo.value = '';
        texto.value = '';
        foto.value = null;
        dimensiones.value = null;
        extension.value = null;
    })
    .catch(error => console.error('Error:', error));
    mostrarMensaje.value = true;
    mensaje.value = '¡Has añadido una nueva entrada con éxito!';
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
            <form @submit.prevent="anadirEntradaBlog">
                <div class="mb-2">
                    <label :style="validarTítulo === null ? estiloNatural : !validarTítulo ? estiloRojo : estiloVerde" for="titulo" class="form-label" aria-label="Título del blog">Título
                        <span v-if="validarTítulo === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarTítulo !== null && !validarTítulo" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-pencil-fill"></i>
                        <input :style="validarTítulo === null ? bordeNatural : !validarTítulo ? bordeRojo : bordeVerde" v-model="titulo" type="text" class="form-control" id="titulo" aria-describedby="Título del blog" placeholder="Ej: Top 3 canciones de mayo 2026" required>
                    </div>
                    <p v-if="validarTítulo !== null && !validarTítulo" :style="estiloRojo">* Título no válido, recuerda que la primera debe ser mayúscula</p>
                </div>
                <div class="mb-2">
                    <label :style="validarCategoria === null ? estiloNatural : !validarCategoria ? estiloRojo : estiloVerde" for="categoria" class="form-label" aria-label="Categoria del blog">Categoria
                        <span v-if="validarCategoria === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarCategoria !== null && !validarCategoria" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <select v-model="nuevaCategoria" name="categoria">
                            <option disabled value="">Selecciona una categoría</option>
                            <option v-for="(categoria, index) in categorias" :key="index" :value="categoria">{{ categoria }}</option>
                        </select>
                    </div>
                    <p v-if="validarTítulo !== null && !validarTítulo" :style="estiloRojo">* Título no válido, recuerda que la primera debe ser mayúscula</p>
                </div>
                <div class="mb-2">
                    <label :style="validarTexto === null ? estiloNatural : !validarTexto ? estiloRojo : estiloVerde" for="texto" class="form-label" aria-label="Texto del blog">Texto
                        <span v-if="validarTexto === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarTexto !== null && !validarTexto" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-card-text"></i>
                        <textarea v-model="texto" name="texto" rows="1" placeholder="Ej: El top número uno de este mes es la canción de Sin pena" required></textarea>
                    </div>
                    <p v-if="validarTexto !== null && !validarTexto" :style="estiloRojo">* El texto no es válidos</p>
                </div>
                <div class="mb-2">
                    <label :style="foto == null ? estiloNatural : tamanoImagen ? estiloVerde : estiloRojo" for="foto" aria-label="Subir foto del producto">Sube una fotografía del producto
                        <span v-if="foto == null || !tamanoImagen" :style="!tamanoImagen ? estiloRojo : estiloNatural">*</span>
                        <i v-else :style="estiloVerde" class="fa fa-check" aria-hidden="true"></i>
                    </label>
                    <input @change="clickImagen($event)" type="file" name="foto" id="fotoProducto" accept="image/webp" required>
                    <p v-if="foto && !tamanoImagen" :style="estiloRojo">* El tamaño de la imagen supera el máximo (2MB)</p>
                    <p v-if="foto && !extension" :style="estiloRojo">* La extensión no es válida, solo archivos .webp</p>
                    <p v-if="foto && !dimensiones" :style="estiloRojo">* La imagen no es válida, solo tamaño de 700x700px</p>
                </div>
                <button type="submit" class="btn btn-primary" aria-label="Añadir entrada de blog" id="enviar" :disabled="!validarTexto || !validarTítulo || !validarCategoria || !tamanoImagen || !dimensiones || !extension">Añadir</button>
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

    .input input, textarea{
        width: 100%;
        padding: 0.5rem 0.5rem 0.5rem 2rem;
        border-radius: 0.5rem;
        border: 1px solid #fcbf00;
    }

    input::placeholder, textarea::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    input:focus, textarea:focus{
        outline: none;
        border-color: #ff8800;
        box-shadow: 0 0 0 2px rgba(255,136,0,0.2);
    }

    #fotoProducto{
        margin-top: 0.5rem;
        padding: 0.4rem;
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