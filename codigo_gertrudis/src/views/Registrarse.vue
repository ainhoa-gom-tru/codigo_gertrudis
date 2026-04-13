<script setup>
import { ApiUrl, bordeNatural, bordeRojo, bordeVerde, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const nombre = ref('');
const apellidos = ref('');
const nombreUsuario = ref('');
const email = ref('');
const contrasena = ref('');
const repetirContrasena = ref('');
const mensajeError = ref(false);

//creamos una expresión regular para validar el nombre y los apellidos
const regex = /^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]+( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]+)*$/;
//validamos nombre y apellidos
const validarNombre = computed(() =>{
    if(!regex.test(nombre.value) && nombre.value.length >= 1){
        return false;
    } else if(nombre.value.length === 0){
        return null;
    } else {
        return true;
    }
});
const validarApellidos = computed(() =>{
    if(!regex.test(apellidos.value) && apellidos.value.length >= 1){
        return false;
    } else if(apellidos.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//expresión regular para validar el nombre de usuario
const usuarioRegex = /^[a-z0-9_]{3,16}$/;
//validamos el nombre de usuario que nos introduce
const validarUsuario = computed(() => {
    if(!usuarioRegex.test(nombreUsuario.value) && nombreUsuario.value.length >= 1){
        return false;
    } else if(nombreUsuario.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//expresión regular para validar el email
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
//validamos el email que nos introduce el usuario
const validarEmail = computed(() => {
    if(!emailRegex.test(email.value) && email.value.length >= 1){
        return false;
    } else if(email.value.length === 0){
        return null;
    } else {
        return true;
    }
})

//creamos una expresión regular para validar que la contraseña sea segura
const contrasenaRegex = /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-_@#$%&]).{8,16}/;
//validamos que la contraseña sea segura
const validarContrasena = computed(() => {
    if(!contrasenaRegex.test(contrasena.value) && contrasena.value.length >= 1){
        return false;
    } else if(contrasena.value.length === 0){
        return null;
    } else {
        return true;
    }
})
//validamos que las contraseñas sean iguales
const validarContrasenas = computed(() => {
    if(contrasena.value !== repetirContrasena.value && repetirContrasena.value.length >= 1){
        return false;
    } else if(repetirContrasena.value.length === 0){
        return null;
    } else {
        return true;
    }
})

function registrarUsuario(){

    //comprobamos que pase las validaciones y que no hayan campos vacíos
    if(nombre.value === '' || apellidos.value === '' || nombreUsuario.value === '' || email.value === '' || 
            contrasena.value === '' || repetirContrasena.value === '' || !validarNombre.value || !validarApellidos.value || 
            !validarUsuario.value || !validarEmail.value || !validarContrasena.value || !validarContrasenas.value){
        mensajeError.value = true;
        return;
    }

    const userData = {
        nombre: nombre.value,
        apellidos: apellidos.value, 
        usuario: nombreUsuario.value, 
        email: email.value, 
        contrasena: contrasena.value
    };

    fetch(ApiUrl + '/usuarios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userData)
    })
    .then(response => response.json())
    .then(data => {console.log('Respuesta:', data),
        nombre.value = '';
        apellidos.value = '';
        nombreUsuario.value = '';
        email.value = '';
        contrasena.value = '';
        repetirContrasena.value = '';
    })
    .catch(error => console.error('Error:', error));
}

</script>

<template>
    <div id="fondo">
        <div id="formulario">
            <form @submit.prevent="registrarUsuario">
                <div id="mensaje-error" v-if="mensajeError">
                    <div></div>
                    <p>Hay campos vacíos o incorrectos</p>
                </div>
                <div class="mb-3">
                    <div id="nombreApellidos">
                        <div>
                            <label :style="validarNombre === null ? estiloNatural : !validarNombre ? estiloRojo : estiloVerde" for="nombre" class="form-label" aria-label="Nombre">Nombre
                                <span v-if="validarNombre === null" :style="estiloNatural">*</span>
                                <i v-else-if="validarNombre !== null && !validarNombre" :style="estiloRojo" class="bi bi-x"></i>
                                <i v-else :style="estiloVerde" class="bi bi-check"></i>
                            </label>
                            <div class="input">
                                <i class="bi bi-file-earmark-person"></i>
                                <input :style="validarNombre === null ? bordeNatural : !validarNombre ? bordeRojo : bordeVerde" v-model="nombre" type="text" class="form-control" id="nombre" placeholder="Ej: Ainhoa">
                            </div>
                        </div>
                        <div>
                            <label :style="validarApellidos === null ? estiloNatural : !validarApellidos ? estiloRojo : estiloVerde" for="apellidos" class="form-label" aria-label="Apellidos">Apellidos
                                <span v-if="validarApellidos === null" :style="estiloNatural">*</span>
                                <i v-else-if="validarApellidos !== null && !validarApellidos" :style="estiloRojo" class="bi bi-x"></i>
                                <i v-else :style="estiloVerde" class="bi bi-check"></i>
                            </label>
                            <div class="input">
                                <i class="bi bi-file-earmark-person"></i>
                                <input :style="validarApellidos === null ? bordeNatural : !validarApellidos ? bordeRojo : bordeVerde" v-model="apellidos" type="text" class="form-control" id="apellidos" placeholder="Ej: Gómez">
                            </div>
                        </div>
                    </div>
                    <p v-if="validarNombre!== null && !validarNombre" :style="estiloRojo">* Nombre no válido, recuerda que la primera es mayúscula</p>
                </div>
                <div class="mb-3">
                    <label :style="validarUsuario === null ? estiloNatural : !validarUsuario ? estiloRojo : estiloVerde" for="usuario" class="form-label" aria-label="Nombre de usuario">Nombre de usuario
                        <span v-if="validarUsuario === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarUsuario !== null && !validarUsuario" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-person-fill"></i>
                        <input :style="validarUsuario === null ? bordeNatural : !validarUsuario ? bordeRojo : bordeVerde" v-model="nombreUsuario" type="text" class="form-control" id="usuario" placeholder="Ej: ainhoagt">
                    </div>
                </div>
                <div class="mb-3">
                    <label :style="validarEmail === null ? estiloNatural : !validarEmail ? estiloRojo : estiloVerde" for="email" class="form-label" aria-label="Correo electrónico">Correo electrónico
                        <span v-if="validarEmail === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarEmail !== null && !validarEmail" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-envelope-fill"></i>
                        <input :style="validarEmail === null ? bordeNatural : !validarEmail ? bordeRojo : bordeVerde" v-model="email" type="email" class="form-control" id="email" placeholder="Ej: example@mail.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label :style="validarContrasena == null ? estiloNatural : !validarContrasena ? estiloRojo : estiloVerde" for="contrasena" class="form-label" aria-label="Contraseña">Contraseña
                        <span v-if="validarContrasena === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarContrasena !== null && !validarContrasena" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-lock-fill"></i>
                        <input v-model="contrasena" type="password" class="form-control" id="contrasena" placeholder="Ej: Hola1234@" :style="validarContrasena === null ? bordeNatural : !validarContrasena ? bordeRojo : bordeVerde">
                    </div>
                    <p v-if="!validarContrasena && validarContrasena !== null" :style="estiloRojo">* La contraseña no es segura</p>
                </div>
                <div class="mb-3">
                    <label :style="validarContrasenas === null ? estiloNatural : !validarContrasenas ? estiloRojo : estiloVerde" for="repetirContrasena" class="form-label" aria-label="Repetir contraseña">Repetir contraseña
                        <span v-if="validarContrasenas === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarContrasenas !== null && !validarContrasenas" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <div class="input">
                        <i class="bi bi-lock-fill"></i>
                        <input :style="validarContrasenas === null ? bordeNatural : !validarContrasenas ? bordeRojo : bordeVerde" v-model="repetirContrasena" type="password" class="form-control" id="repetirContrasena">
                    </div>
                    <p v-if="!validarContrasenas && validarContrasenas !== null" :style="estiloRojo">* Las contraseñas no coinciden</p>
                </div>
                <button type="submit" class="btn btn-registro">Registrarse</button>
            </form>
            <video id="medio" autoplay loop>
                <source src="/videoRegister.webm" type="video/webm">
                <source src="/videoRegister.mp4" type="video/mp4">
                Tu navegador no soporta el vídeo.
            </video>
        </div>
    </div>
</template>

<style scoped>

    #fondo{
        background: linear-gradient(90deg, #ff8800,#fcbf00);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #formulario {
        display: flex;
        width: 90%;
        height: 90vh;
        display: flex;
        border-radius: 40px;
        overflow: hidden;
    }

    form {
        width: 50%;
        background-color: white;
        padding: 5%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    #mensaje-error{
        display: flex;
        padding: 1%;
        border-radius: 10px;
        box-shadow: 5px 5px 5px 0px;
        height: 7%;
    }

    #mensaje-error div{
        background-color: red;
        width: 1.5%;
        height: 70%;
        border-radius: 3px;
        margin-left: 1%;
        margin-right: 1%;
        margin-top: 0.5%;
    }

    #mensaje-error p{
        color: red;
    }

    #nombreApellidos{
        display: flex;
        width: 100%;
    }

    #nombreApellidos .input i {
        position: absolute;
        display: block;
        bottom: 20%;
        left: 3%;
        color: #fcbf00;
        opacity: 0.6;
    }

    #nombreApellidos input{
        padding-left: 12%;
    }

    #nombre{
        width: 85%;
    }

    #apellidos{
        width: 124%;
    }

    .input{
        position: relative;
    }

    .input i{
        position: absolute;
        display: block;
        bottom: 18%;
        left: 2%;
        color: #fcbf00;
        opacity: 0.6;
    }

    .input input{
        padding-left: 6%;
    }

    input{
        border: 1px solid #fcbf00;
    }

    input::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    .btn-registro{
        background-color: #fcbf00;
        color: white;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-registro:hover{
        background-color: #ff8800;
        transform: translateY(-2px);
    }

    video{
        width: 50%;
        object-fit: cover;
    }

</style>