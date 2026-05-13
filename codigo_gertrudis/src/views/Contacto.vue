<script setup>
import { estiloRojo, estiloVerde } from '@/main';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { onMounted, ref } from 'vue';

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable para almacenar el nombre y la imagen en caso de que hubiera de usuario logueado
const emailUsuario = ref('');

if(usuarioLogueado){
  emailUsuario.value = usuarioLogueado.email;
}

//creamos las siguientes variables
const mostrarModal = ref(false);
const sugerencia = ref('');
let map;
const latitud = ref(37.7703885890033);
const longitud = ref(-3.787812475560868);
const marker = ref(null);

onMounted(() => {
    map = L.map('map').setView([latitud.value, longitud.value], 30);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker.value = L.marker([latitud.value, longitud.value]).addTo(map);

    map.on('click', function(e) {
        latitud.value = e.latlng.lat;
        longitud.value = e.latlng.lng;

        if (marker.value) {
            map.removeLayer(marker.value);
        }

        marker.value = L.marker([latitud.value, longitud.value]).addTo(map);
    });
});

</script>

<template>
    <div id="sugerencia">
        <img src="/logo.png" alt="Logotipo">
        <form @submit.prevent="mostrarModal = true" v-if="usuarioLogueado">
            <div class="mb-2">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" aria-label="Tu correo electronico" :value="emailUsuario">
            </div>
            <div class="mb-2">
                <textarea v-model="sugerencia" name="comentario" id="comentario" placeholder="Déjanos un comentario..." aria-label="Escribe un comentario"></textarea>
            </div>
            <button type="submit" class="btn" @click="mostrarModal = true; sugerencia = ''" :disabled="!sugerencia">Enviar</button>
        </form>
        <div v-else id="mensaje">
            <p :style="estiloRojo">Debes de iniciar sesión para poder realizar una sugerencia</p>
        </div>
    </div>
    <div id="ubicacion">
        <h4>Ubicación</h4>
        <div>
            <div id="map"></div>
        </div>
    </div>
    <!-- añadimos un modal para indicar al usuario que se ha enviado su mensaje correctamente -->
    <div v-if="mostrarModal" class="modal-overlay"  tabindex="-1" role="dialog">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :style="estiloVerde">Sugerencia exitosa</h5>
                    <button type="button" class="btn-close" @click="mostrarModal = false" aria-label="Cerrar modal"></button>
                </div>
                <div class="modal-body">
                    <p>¡Has enviado tu sugerencia con éxito!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" aria-label="Botón para cerrar modal" @click="mostrarModal = false">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

    #sugerencia{
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        padding: 2rem 1rem;
        margin: 2rem 0rem;
    }

    img{
        display: block;
        width: 12rem;
        max-width: 40%;
        height: auto;
        margin-left: 14rem;
    }

    form, #mensaje{
        margin: 0 auto;
        padding: 2rem;
        border-radius: 1.5rem;
        box-shadow: 0 0.8rem 2rem rgba(0,0,0,0.12);
        width: 100%;
        max-width: 32rem;
    }

    .form-label{
        font-weight: 600;
        color: #ff8800;
        font-size: 1rem;
        display: block;
        margin-bottom: 0.3rem;
    }

    .form-control{
        border: 0.12rem solid #fcbf00;
        border-radius: 0.6rem;
        padding: 0.7rem;
        width: 100%;
        font-size: 1rem;
    }

    .form-control:focus{
        border-color: #ff8800;
    }

    textarea{
        width: 100%;
        height: 18vh;
        min-height: 7rem;
        border: 0.12rem solid #fcbf00;
        border-radius: 0.6rem;
        padding: 0.7rem;
        resize: none;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    textarea:focus{
        outline: none;
        border-color: #ff8800;
    }

    textarea::placeholder{
        font-style: italic;
        color: #fcbf00;
        opacity: 0.7;
    }

    .btn{
        width: 100%;
        margin-top: 1.2rem;
        background-color: #fcbf00;
        border: none;
        padding: 0.8rem;
        border-radius: 0.6rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        color: black;
    }

    .btn:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

    h4{
        text-align: center;
        margin-top: 2rem;
        color: #ff8800;
        font-size: 1.6rem;
        letter-spacing: 0.05rem;
    }

    #ubicacion h4{
        text-align: left;
        margin: 3rem 5rem;
    }

    #ubicacion div{
        width: 95%;
        height: 60vh;
        margin: -1rem 2rem;
        padding: 1.2rem;
        border-radius: 1.5rem;
    }

    #map {
        height: 55vh;
        width: 100%;
        border-radius: 1rem;
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

    @media (max-width: 768px) {

        img {
            display: none;
        }

        #sugerencia {
            padding: 1rem;
            margin: 1rem 0;
            gap: 1rem;
        }

        form, #mensaje {
            width: 100%;
            max-width: 100%;
            padding: 1.2rem;
            border-radius: 1rem;
        }

        .form-label {
            font-size: 0.95rem;
        }

        .form-control, textarea {
            font-size: 0.95rem;
        }

        textarea {
            height: 14vh;
            min-height: 6rem;
        }

        .btn {
            padding: 0.7rem;
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: none;
        }

        #ubicacion {
            margin-bottom: 4rem;
        }

        #ubicacion h4 {
            margin: 2rem;
            text-align: center;
            font-size: 1.4rem;
        }

        #ubicacion div {
            width: 100%;
            padding: 0.5rem;
            margin: 0;
        }

        #map {
            height: 55vh;
            width: 100%;
            border-radius: 0.8rem;
        }

        .modal-container {
            width: 92%;
            padding: 1rem;
        }

        .modal-body {
            font-size: 1rem;
        }

        .modal-footer {
            justify-content: center;
        }

    }
    
</style>