<script setup>
import { useRoute } from 'vue-router';
import companeras from '@/compis/compis.json';
import { computed, ref } from 'vue';
import { ApiUrl } from '@/main';

//Creamos las siguientes variables
const productos = ref([]);
const route = useRoute();

//obtenemos de la url el nombre de la compañera para imprimir todos los detalles
const nombreCompi = route.params.nombre;
console.log(nombreCompi);

//para encontrar la informacion de la compañera en el jsons
const companeraActual = computed(() =>
  companeras.find(c => c.nombre.toLowerCase() === nombreCompi.toLowerCase())
);

//funcion para obtener todos los productos
function obtenerProductos() {
  fetch(ApiUrl + '/productos')
    .then(res => res.json())
    .then(data => {
        console.log(data);
        productos.value = data.filter(p => p.nombre.toLowerCase().includes(nombreCompi.toLowerCase())).slice(0, 4);
    })
    .catch(err => console.error(err));
}

obtenerProductos();

</script>

<template>
    <div id="presentacionCompanera">
        <img class="fondoImagen" :src="`/compis/${companeraActual.fondo}`">
        <div class="imagenCompanera">
            <img id="fondo" src="/fondoImagenCompi.png">
            <img id="foto" :src="`/compis/${companeraActual.foto}`" alt="Foto compañera de piso">
        </div>
    </div>
    <div v-if="companeraActual && companeraActual.galeria" class="galeria-container">
        <div v-for="(fotoGaleria, index) in companeraActual.galeria" :key="index" class="galeria-card">
            <img :src="`/compis/${fotoGaleria.fotografia}`" />
            <span class="tooltip">{{ fotoGaleria.tooltip }}</span>
        </div>
    </div>
    <div id="descripcion" :style="{ backgroundColor: companeraActual.color }">
        <p>{{ companeraActual.descripcion }}</p>
    </div>
    <div id="todosProductos">
        <h4>Productos relacionados</h4>
        <div class="productos-container">
            <div v-for="producto in productos" class="card" style="width: 18rem;">
            <div>
                <img :src="`http://localhost:8001/productos/${producto.foto}`" class="card-img-top" alt="Foto del producto">
                <div id="valoracion">
                    <p>4.5</p>
                    <i class="bi bi-star-fill"></i>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ producto.nombre }}</h5>
                <p class="card-text">
                    {{ producto.precio.toString().replace('.', ',') }}€
                </p>
                <button>Añadir al carrito</button>
            </div>
        </div>
        </div>
    </div>
</template>

<style scoped>

    #presentacionCompanera {
        position: relative;
        overflow: hidden;
        margin-bottom: 3rem;
        width: 100%;
    }

    .fondoImagen {
        width: 100vw;
        height: 90vh;
        object-fit: cover;
        display: block;
        z-index: 0;
    }

    .imagenCompanera{
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #fondo{
        width: 25rem;
        z-index: 2;
        margin-left: 6rem;
    }

    #foto{
        width: 20rem;
        position: absolute;
        top: 1.5rem;
        left: 8rem;
        z-index: 3;
    }

    .galeria-container {
        display: flex;
        gap: 1.5rem;
        padding: 2rem;
        justify-items: center;
    }

    .galeria-card {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        width: 100%;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .galeria-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tooltip {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,1);
        color: white;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        opacity: 0;
    }

    .galeria-card:hover .tooltip {
        opacity: 1;
    }

    #descripcion{
        padding: 2rem;
        margin: 3rem 0rem;
        color: white;
        text-align: center;
    }

    #descripcion p{
        width: 80%;
        margin: 0 auto;
    }

    #todosProductos{
        margin: 0rem 6rem;
    }

    #todosProductos h4{
        color: #fcbf00;
        margin: 1rem 0rem;
    }

    .productos-container{
        display: flex;
        flex-wrap: wrap;
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