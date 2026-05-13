<script setup>
import { ApiUrl } from '@/main';
import { ref } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

//obtener el id de la url
const route = useRoute();
const nombreJuego = route.params.nombre;
console.log(nombreJuego);

//creamos las siguientes variables
const juego = ref(null);
const juegos = ref([]);

//funcion para obtener todos los datos del usuario
function obtenerDatosJuego() {
    fetch(ApiUrl + '/juegos', {
        method: 'GET',
    })
        .then(response => response.json())
        .then(data => {
            juegos.value = data;
            const game = data.find(g => g.nombre === nombreJuego);
            juego.value = game;
            console.log('Juego:', juego)
        })
        .catch(error => console.error('Error:', error));
}

obtenerDatosJuego()

</script>

<template>
    <main>
        <aside>
            <RouterLink to="/juegos">
                <i class="bi bi-arrow-left"></i>
                Volver
            </RouterLink>
            <div class="mas-juegos">
                <div v-for="game in juegos.slice(0, 3)" :key="juego.nombre">
                    <RouterLink :to="`/juego/${game.nombre}`">
                        <div class="card-juego">
                            <img :src="`http://localhost:8001/miniaturas/${game.foto}`">
                            <h6 class="titulo-juego">{{ game.nombre }}</h6>
                        </div>
                    </RouterLink>
                </div>
            </div>
        </aside>
        <div class="game" v-if="juego">
            <iframe :src="`http://localhost:8001/juegos/${juego.archivo}`" frameborder="0"></iframe>
        </div>
    </main>
</template>

<style scoped>

    main {
        display: flex;
        padding: 1rem;
    }

    aside {
        width: 15rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin: 0rem 2rem;
    }

    aside a {
        color: #fcbf00;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
    }

    aside a:hover {
        transform: translateX(-4px);
    }

    .mas-juegos {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .card-juego {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
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

    .game {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .game iframe {
        width: 100%;
        height: 90vh;
        border-radius: 1rem;
        border: none;
    }

    @media (max-width: 768px) {

        main {
            flex-direction: column;
            padding: 0.5rem;
            margin: 4rem 0rem;
        }

        aside {
            width: 100%;
            margin: 0;
            flex-direction: column;
            gap: 1rem;
        }

        aside a {
            justify-content: center;
            font-size: 1rem;
        }

        aside a:hover {
            transform: none;
        }

        .mas-juegos {
            flex-direction: row;
            overflow-x: auto;
            gap: 0.8rem;
            padding-bottom: 0.5rem;
        }

        .mas-juegos > div {
            min-width: 140px;
            flex-shrink: 0;
        }

        .card-juego {
            border-radius: 0.6rem;
        }

        .card-juego img {
            height: 100px;
        }

        .titulo-juego {
            opacity: 1;
            font-size: 0.8rem;
            padding: 0.3rem 0.5rem;
        }

        .card-juego:hover .titulo-juego {
            opacity: 1;
        }

        .game {
            margin-top: 1rem;
        }

        .game iframe {
            height: 60vh;
            border-radius: 0.8rem;
        }
    }

</style>