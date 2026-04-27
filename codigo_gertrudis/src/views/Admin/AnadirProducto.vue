<script setup>
import { ApiUrl, bordeNatural, bordeRojo, bordeVerde, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const nombreProducto = ref('');
const descripcionProducto = ref('');
const foto = ref(null);
const dimensiones = ref(null);
const extension = ref(null);
const precio = ref('');
const unidades = ref('');
const mostrarMensaje = ref(false);
const mensaje = ref('');

//creamos una variable para almacenar una expresión regular que valide el nombre del producto
const expresionRegular = /^[A-ZÁÉÍÓÚÑ][a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]*(?:\s[a-zA-Z0-9ÁÉÍÓÚáéíóúÑñ]+)*$/;

//comprobamos que el nombre que nos escriben pasa la validación
const validarNombre = computed(() => {
    if (!expresionRegular.test(nombreProducto.value) && nombreProducto.value.length > 1){
        return false;
    } else if (nombreProducto.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//creamos una expresión regular para la descripción
const expresionRegularDescripcion = /^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\n]+$/;

//comprobamos que la descripcion que nos escriben pasa la validación
const validarDescripcion = computed(() => {
    if (!expresionRegularDescripcion.test(descripcionProducto.value) && descripcionProducto.value.length > 1){
        return false;
    } else if (descripcionProducto.value.length === 0){
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

//creamos una expresión regular para validar le precio del producto
const expresionRegularPrecio = /^\d{1,8}(\.\d{1,2})?$/;

//comprobamos que el precio que nos escriben pasa la validación
const validarPrecio = computed(() => {
    if (!expresionRegularPrecio.test(precio.value) && precio.value.length > 1){
        return false;
    } else if (precio.value.length === 0){
        return null;
    } else if (parseFloat(precio.value) < 1){
        return false;
    } else {
        return true;
    }
});

//creamos una expresión regular para validar que las unidades
const expresionRegularUnidades = /^\d+$/;

//comprobamos que la descripcion que nos escriben pasa la validación
const validarUnidades = computed(() => {
    if (!expresionRegularUnidades.test(unidades.value) && unidades.value.length > 1){
        return false;
    } else if (unidades.value.length === 0){
        return null;
    } else if (unidades.value < 0 || unidades.value > 1000){
        return false;
    } else {
        return true;
    }
});

//function para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

//funcion para añadir un nuevo producto
function anadirProducto(){
    const productoData = new FormData();
    productoData.append('nombre', nombreProducto.value);
    productoData.append('descripcion', descripcionProducto.value);
    productoData.append('foto', foto.value);
    productoData.append('precio', precio.value);
    productoData.append('unidades', unidades.value);

    fetch(ApiUrl + '/productos', {
        method: 'POST',
        body: productoData
    })
    .then(response => response.json())
    .then(data => {

        //comprobamos que si el producto ya existe en la base de datos
        if (data.error) {
            mensaje.value = "El producto ya existe en la web";
            mostrarMensaje.value = true;
            //vaciamos el formulario
            nombreProducto.value = '';
            descripcionProducto.value = '';
            foto.value = null;
            dimensiones.value = null;
            extension.value = null;
            precio.value = '';
            unidades.value = '';
            return;
        }

        console.log('Respuesta:', data);
        //vaciamos el formulario
        nombreProducto.value = '';
        descripcionProducto.value = '';
        foto.value = null;
        dimensiones.value = null;
        extension.value = null;
        precio.value = '';
        unidades.value = '';
    })
    .catch(error => console.error('Error:', error));
    mostrarMensaje.value = true;
    mensaje.value = '¡Has añadido un nuevo producto con éxito!';
}

</script>

<template>
    <div id="body">
        <main>
            <div v-if="mostrarMensaje" class="mensaje" :class="mensaje.includes('éxito') ? 'success' : 'error'">
                <button @click="cerrarMensaje">X</button>
                <i v-if="mensaje.includes('éxito')" class="bi bi-check2"></i>
                <i v-else class="bi bi-x-lg"></i>
                <p class="texto-mensaje">{{ mensaje }}</p>
            </div>
            <form @submit.prevent="anadirProducto">
                <div class="mb-2">
                    <label :style="validarNombre === null ? estiloNatural : !validarNombre ? estiloRojo : estiloVerde" for="nombre" class="form-label" aria-label="Nombre del producto">Nombre
                        <span v-if="validarNombre === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarNombre !== null && !validarNombre" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-pencil-fill"></i>
                        <input :style="validarNombre === null ? bordeNatural : !validarNombre ? bordeRojo : bordeVerde" v-model="nombreProducto" type="text" class="form-control" id="nombre" aria-describedby="Nombre producto" placeholder="Ej: Pack completo revista edición n1" required>
                    </div>
                    <p v-if="validarNombre !== null && !validarNombre" :style="estiloRojo">* Nombre no válido, recuerda que la primera debe ser mayúscula</p>
                </div>
                <div class="mb-2">
                    <label :style="validarDescripcion === null ? estiloNatural : !validarDescripcion ? estiloRojo : estiloVerde" for="descripcion" class="form-label" aria-label="Descripcion del producto">Descripción
                        <span v-if="validarDescripcion === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarDescripcion !== null && !validarDescripcion" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-card-text"></i>
                        <textarea v-model="descripcionProducto" name="descripcion" rows="1" placeholder="Ej: Con este pack completo tendrá el juego de Gertrudis y la edición número 1" required></textarea>
                    </div>
                    <p v-if="validarDescripcion !== null && !validarDescripcion" :style="estiloRojo">* La descripción no es válida</p>
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
                <div class="mb-2">
                    <label :style="validarPrecio === null ? estiloNatural : !validarPrecio ? estiloRojo : estiloVerde" for="precio" class="form-label" aria-label="Precio del producto">Precio
                        <span v-if="validarPrecio === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarPrecio !== null && !validarPrecio" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-pencil-fill"></i>
                        <input :style="validarPrecio === null ? bordeNatural : !validarPrecio ? bordeRojo : bordeVerde" v-model="precio" type="text" class="form-control" id="precio" aria-describedby="Precio producto" placeholder="Ej: 10" required>
                    </div>
                    <p v-if="validarPrecio !== null && !validarPrecio" :style="estiloRojo">* El formato del precio no es válido</p>
                </div>
                <div class="mb-2">
                    <label :style="validarUnidades === null ? estiloNatural : !validarUnidades ? estiloRojo : estiloVerde" for="unidades" class="form-label" aria-label="Unidades del producto">Unidades
                        <span v-if="validarUnidades === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarUnidades !== null && !validarUnidades" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-pencil-fill"></i>
                        <input :style="validarUnidades === null ? bordeNatural : !validarUnidades ? bordeRojo : bordeVerde" v-model="unidades" type="text" class="form-control" id="unidades" aria-describedby="Unidades producto" placeholder="Ej: 54" required>
                    </div>
                    <p v-if="validarUnidades !== null && !validarUnidades" :style="estiloRojo">* El formato de unidades no es válido</p>
                </div>
                <button type="submit" class="btn btn-primary" aria-label="Añadir producto" id="enviar" :disabled="!validarNombre || !validarDescripcion || !validarPrecio || !validarUnidades || !tamanoImagen || !dimensiones || !extension">Enviar</button>
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