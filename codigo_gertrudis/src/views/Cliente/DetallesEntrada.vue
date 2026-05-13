<script setup>
import { ApiUrl, estiloRojo, estiloVerde } from '@/main';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
//obtenemos de la url el nombre de usuario para imprimir todos los detalles
const tituloEntrada = route.params.titulo;
console.log(tituloEntrada);

//creamos las siguientes variables
const entrada = ref(null);

//funcion para obtener todos los detalles de la entrada
function obtenerDetallesEntrada(){
    fetch(ApiUrl + '/blog', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        const article = data.find(nt => nt.titulo === tituloEntrada);
        entrada.value = article;
        console.log('Entrada:', entrada.value)
    })
    .catch(error => console.error('Error:', error));
}

obtenerDetallesEntrada();

</script>

<template>
    <div id="body">
        <main>
            <RouterLink to="/blog">
                <i class="bi bi-arrow-left"></i>
                Volver
            </RouterLink>
            <div class="hero">
                <img :src="`http://localhost:8001/blog/${entrada.foto}`">
                <h2>{{ entrada.titulo }}</h2>
            </div>
            <div id="todasEntradas">
                <h5 class="card-title">{{ entrada.categoria.charAt(0).toUpperCase() + entrada.categoria.slice(1) }}</h5>
                <p class="card-text">{{ entrada.texto }}</p>
            </div>
        </main>
    </div>
</template>

<style scoped>

    a {
        color: #fcbf00;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
        margin: 2rem 4rem;
    }

    a:hover {
        transform: translateX(-4px);
    }

    .hero{
        position: relative;
    }

    .hero img{
        width: 100%;
        height: 80vh;
        object-fit: cover;
        opacity: 0.3;
    }

    .hero h2{
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fcbf00;
        font-size: 3rem;
        font-weight: bold;
        color: black;
        padding: 3rem;
        border-radius: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    #todasEntradas{
        margin: 2rem 4rem;
    }

    #todasEntradas h5{
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {

        a {
            margin: 4rem;
            justify-content: center;
            font-size: 1rem;
        }

        a:hover {
            transform: none;
        }

        .hero img {
            height: 50vh;
        }

        .hero h2 {
            font-size: 1.5rem;
            padding: 1.2rem 1.5rem;
            border-radius: 20px;
            text-align: center;
            width: 85%;
        }

        #todasEntradas {
            margin: 2rem 1rem;
        }

        #todasEntradas h5 {
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        #todasEntradas p {
            font-size: 0.95rem;
            line-height: 1.6rem;
            text-align: justify;
            margin: 0rem 2rem;
        }

    }

</style>