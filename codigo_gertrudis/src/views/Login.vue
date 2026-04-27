<script setup>
import { ApiUrl } from '@/main';
import router from '@/router';
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';

//declaramos las sigueintes variables
const usuarioEmail = ref('');
const recordar = ref(false);
const contrasena = ref('');
const mostrarContrasena = ref(false);
const hayErrores = ref(false);
const mensajeError = ref(false);

//funcion para mostrar la contraseña
function mostrarLaContrasena() {
    mostrarContrasena.value = !mostrarContrasena.value;
}

//expresión regular para validar el email
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
//expresión regular para validar el nombre de usuario
const usuarioRegex = /^[a-z0-9_]{3,16}$/;

//validamos que sea o usuario o email
const validarEmailUsuario = computed(() => {
    return emailRegex.test(usuarioEmail.value) || usuarioRegex.test(usuarioEmail.value);
});

//función para iniciar la sesión del usuario
function iniciarSesion(){
    //comprobamos qeu la contraseña y el correo electrónico o usuario no nos llega vacío
    if(usuarioEmail.value === '' || contrasena.value === '' || !validarEmailUsuario.value){
        hayErrores.value = true;
        mensajeError.value = true;

        //tras 5 segundos, dejamos de mostrar el mensaje de error
        setTimeout(() => {
            mensajeError.value = false;
            hayErrores.value = false;
        }, 4000);

        usuarioEmail.value = '';
        contrasena.value = '';

        return;
    }

    const datosInicioSesion = {
        contrasena: contrasena.value
    };

    //si pasa la validación de email es porque es un email, sino es un usuario
    if (emailRegex.test(usuarioEmail.value)) {
        datosInicioSesion.email = usuarioEmail.value;
    } else {
        datosInicioSesion.usuario = usuarioEmail.value;
    }

    fetch(ApiUrl + '/usuarios/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosInicioSesion)
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            console.log('Ha iniciado sesión:', data);
            //si está marcado la casilla de recordar, lo almacenamos en localstorage
            if(recordar.value){
                localStorage.setItem('user', JSON.stringify(data));
            } else {
                localStorage.removeItem('user');
            }
        usuarioEmail.value = "";
        contrasena.value = "";
        recordar.value = false;
        router.push("/");
        } else {
            console.log('Error al iniciar sesión:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));

}

</script>

<template>
    <div id="fondo">
        <div id="formulario">
            <form @submit.prevent="iniciarSesion">
                <div id="mensaje-error" v-if="hayErrores">
                    <div></div>
                    <p v-if="mensajeError">* Hay campos vacíos y/o incorrectos</p>
                </div>
                <div class="mb-2">
                    <label for="usuarioEmail" class="form-label" aria-label="Correo electrónico o usuario">Correo electrónico o usuario *</label>
                    <div class="input">
                        <i class="bi bi-person-fill"></i>
                        <input v-model="usuarioEmail" type="text" class="form-control" id="usuarioEmail" placeholder="Ej: example@mail.com">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="contrasena" class="form-label" aria-label="Contraseña">Contraseña *</label>
                    <div class="input">
                        <i class="bi bi-lock-fill"></i>
                        <input v-model="contrasena" class="form-control" id="contrasena" placeholder="Ej: Hola1234@" :type="mostrarContrasena ? 'text' : 'password'">
                        <button type="button" class="mostrarContrasena" @click="mostrarLaContrasena">
                            <i v-if="!mostrarContrasena" class="bi bi-eye-slash-fill"></i>
                            <i v-else class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-2 form-check">
                    <input v-model="recordar" type="checkbox" class="form-check-input" id="recuerdame">
                    <label class="form-check-label" for="recuerdame">Recuérdame</label>
                </div>
                <button type="submit" class="btn btn-login" :disabled="!validarEmailUsuario || contrasena === ''">Iniciar Sesión</button>
                <div class="enlace-registrarse">
                    <p>¿Aún no tienes una cuenta?</p>
                    <RouterLink to="/registrarse">Regístrate</RouterLink>
                </div>
            </form>
            <video id="medio" autoplay loop>
                <source src="/loginVideo.webm" type="video/webm">
                <source src="/loginVideo.mp4" type="video/mp4">
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

    label{
        color: #ff8800;
    }

    #mensaje-error{
        display: flex;
        padding: 1%;
        border-radius: 10px;
        box-shadow: 5px 5px 5px 0px;
        height: 7%;
        margin-bottom: 3%;
        align-items: center;
    }

    #mensaje-error div{
        background-color: red;
        width: 1.5%;
        height: 80%;
        border-radius: 3px;
        margin-left: 1%;
        margin-right: 1%;
        margin-top: 0.5%;
    }

    #mensaje-error p{
        color: red;
        margin-top: 3%;
    }

    input{
        border: 2px solid #fcbf00;
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

    input::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    .mostrarContrasena{
        position: absolute;
        display: block;
        bottom: 15%;
        right: 5%;
        color: #fcbf00;
        background: none;
        border: none;
    }

    .btn-login{
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        transition: all 0.3s ease;
        margin-bottom: 3%;
        border: none;
    }

    .btn-login:hover{
        background-color: #ff8800;
        transform: translateY(-2px);
    }

    .enlace-registrarse{
        display: flex;
        align-items: center;
    }

    .enlace-registrarse p{
        margin: 0;
    }

    .enlace-registrarse a{
        margin-left: 2%;
    }

    video{
        width: 50%;
        object-fit: cover;
    }

    @media (max-width: 768px) {

        #formulario {
            flex-direction: column;
            height: auto;
            width: 95%;
            border-radius: 20px;
        }

        form {
            width: 100%;
            padding: 8%;
        }

        video {
            display: none;
        }

        .mostrarContrasena {
            right: 4%;
            bottom: 18%;
        }

    }

</style>