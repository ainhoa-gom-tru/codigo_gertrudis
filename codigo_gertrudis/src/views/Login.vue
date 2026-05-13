<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
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
const mostrarModal = ref(false);

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
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosInicioSesion)
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            console.log("ROL RAW:", `"${data.rol}"`);
            console.log('Ha iniciado sesión:', data);
            localStorage.setItem('user', JSON.stringify(data));
            usuarioEmail.value = "";
            contrasena.value = "";
            recordar.value = false;
            //obtenemos el rol del usuario que está haciendo login y si está validado o no
            const rolPermitido = data.rol === "admin" || data.rol === "desarrollador";
            const estaValidado = data.validado == 1;
            //redigirigimos en función del rol y la validez
            if (rolPermitido && estaValidado) {
                if (data.rol === "admin") {
                    router.push("/gestion-usuarios");
                } else {
                    router.push("/gestion-juegos");
                }
            } else {
                //mostrarmos el modal en caso de que no esté validado
                if (estaValidado){
                    router.push("/");
                } else {
                    mostrarModal.value = true;
                }
            }
        } else {
            console.log('Error al iniciar sesión:', data.error);
            hayErrores.value = true;
        }
    })
    .catch(error => console.error('Error:', error));

}

//funcion parar cerrar el mensaje de error
function cerrarModal(){
    hayErrores.value = false;
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
                <div v-if="hayErrores" class="mensaje">
                    <button @click="cerrarMensaje">X</button>
                    <i class="bi bi-x-octagon-fill"></i>
                    <p>Hay campos vacíos y/o incorrectos</p>
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
    <div v-if="mostrarModal" class="modal-overlay"  tabindex="-1" role="dialog">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :style="estiloRojo">Cuenta no validada</h5>
                    <button type="button" class="btn-close" @click="mostrarModal = false" aria-label="Cerrar modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tu cuenta será validad en las próximas 24 horas</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" aria-label="Botón para iniciar sesión" @click="router.push('/')">Cerrar</button>
                </div>
            </div>
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

    .mensaje {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 1.2rem 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        margin-bottom: 1.5rem;
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

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-container {
        background: white;
        width: 400px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: aparecer 0.2s ease-out;
        padding: 2%;
    }

    .modal-content {
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        padding-bottom: 5%;
        border-bottom: 1px solid #eee;
    }

    .modal-body {
        padding: 5% 0%;
        font-size: 110%;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding-top: 5%;
        border-top: 1px solid #eee;
    }

    .btn-cerrar {
        margin-top: 2%;
        background-color: #ff8800;
        color: black;
        font-weight: bold;
        transition: all 0.3s ease;
        margin-right: 5%;
    }

    .btn-cerrar:hover{
        background-color: #fcbf00;
        transform: translateY(-2px);
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