<script setup>
import { ApiUrl, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';

//creamos las siguientes variables
const juegos = ref([]);

//funcion para obtener todos los juegos
function obtenerTodasFotos(){
    fetch(ApiUrl + '/juegos', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Juegos:', data)
        juegos.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodasFotos();

</script>

<template>
    <main>
        <h4>Todos los juegos</h4>
        <div class="galeria">
            <div v-for="juego in juegos" :key="juego.nombre">
                <RouterLink :to="`/juego/${juego.nombre}`">
                    <div class="card-juego">
                        <img :src="`http://localhost:8001/miniaturas/${juego.foto}`">
                        <h6 class="titulo-juego">{{ juego.nombre }}</h6>
                    </div>
                </RouterLink>
            </div>
        </div>
    </main>
</template>

<style scoped>

    main{
        margin: 2rem;
    }

    h4{
        color: #fcbf00;
        font-weight: 700;
        margin: 0rem 1rem;
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
    }

    .card-juego {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
    }

    .titulo-juego {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: black;
        font-weight: bold;
        text-align: center;
        z-index: 2;
        padding: 0.4rem 0.8rem;
        border-radius: 0.4rem;
        background-color: #fcbf00;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .card-juego img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease, filter 0.3s ease;
    }

    .card-juego:hover .titulo-juego {
        opacity: 1;
    }

    @media (max-width: 768px) {

        main {
            margin: 4rem 1rem;
        }

        h4 {
            margin: 0 0.5rem;
            font-size: 1.2rem;
            text-align: center;
        }

        .galeria {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.8rem;
            padding: 0.5rem;
            margin-top: 1rem;
        }

        .card-juego {
            border-radius: 0.6rem;
        }

        .galeria div {
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
        }

        .galeria img,
        .card-juego img {
            height: 140px; 
        }

        .titulo-juego {
            opacity: 1;
            font-size: 0.85rem;
            padding: 0.3rem 0.6rem;
        }

        .galeria div:hover {
            transform: none;
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
            cursor: default;
        }

        .galeria div:hover img {
            transform: none;
        }
    }

</style>