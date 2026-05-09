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
            <h2>{{ entrada.titulo }}</h2>
            <div class="hero">
                <img :src="`http://localhost:8001/blog/${entrada.foto}`">
            </div>
            <div id="todasEntradas">
                <h5 class="card-title">{{ entrada.categoria.charAt(0).toUpperCase() + entrada.categoria.slice(1) }}</h5>
                <p class="card-text">{{ entrada.texto }}</p>
            </div>
        </main>
    </div>
</template>

<style scoped>

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
        display: flex;
        flex-wrap: wrap;
        margin-top: 4rem;
        margin-left: 2%;
        margin-bottom: 2%;
    }

    .card{
        margin: 1%;
        padding: 1rem;
        border-radius: 2rem;
        color: black;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: none;
    }

    .card img{
        border-radius: 1.5rem;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-text{
        margin: 0.3rem 0;
        overflow: hidden;
        position: relative;
        padding-right: 1.2rem;
    }

    #valoracion{
        display: flex;
        align-items: center;
        gap: 0.3rem;
        background-color: white;
        padding: 0rem 0.5rem;
        border-radius: 0.8rem;
        position: absolute;
        top: 7%;
        right: 9%;
        font-weight: bold;
        color: #fcbf00;
        height: 2.5rem;
    }

    #valoracion p{
        margin-top: 0.8rem;
    }

    #valoracion i{
        margin-top: -0.3rem;
    }

    .card-title{
        color: #ff8800;
        font-weight: bold;
    }

    .card-text{
        margin: 0.3rem 0;
    }

    .card button{
        background-color: #fcbf00;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card button:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
    }

</style>