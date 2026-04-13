<script setup>
import { RouterLink, routerViewLocationKey } from 'vue-router';

</script>

<template>
    <div id="fondo">
        <div id="formulario">
            <form>
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label" aria-label="Correo electrónico o usuario">Correo electrónico o usuario</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Ej: example@mail.com">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label" aria-label="Contraseña">Contraseña</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Ej: Hola1234@">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Recuérdame</label>
                </div>
                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
                <p>¿Aún no tienes una cuenta?</p>
                <RouterLink to="/registrarse">Regístrate</RouterLink>
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

    input{
        border: 1px solid #fcbf00;
    }

    input::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.6;
    }

    .btn-login{
        background-color: #fcbf00;
        color: white;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-login:hover{
        background-color: #ff8800;
        transform: translateY(-2px);
    }

    video{
        width: 50%;
        object-fit: cover;
    }

</style>