<script setup>
import { ApiUrl } from '@/main';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';

//crea las siguientes variables
const productos = ref([]);

//funcion para obtener todos los usuarios
function obtenerTodosProductos(){
    fetch(ApiUrl + '/productos', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        console.log('Productos:', data)
        productos.value = data;
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodosProductos();

//funcion para añadir los productos al carrito
function añadirCarrito(producto) {
    fetch(ApiUrl + '/carrito', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            producto_id: producto.id,
            cantidad: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Añadido al carrito:', data);
    })
    .catch(error => console.error('Error:', error));
}

</script>

<template>
    <div class="hero">
        <img src="/fondo tienda.png">
        <h2>Productos</h2>
    </div>
    <form>
        <div>
            <input type="text" placeholder="Encuentra tu producto">
        </div>
        <div>
            <input type="number" placeholder="1€" min="1">
        </div>
        <button>
            <i class="fa fa-search" aria-hidden="true"></i>
            Buscar
        </button>
    </form>
    <div id="todosProductos">
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
                <button @click="añadirCarrito(producto)">Añadir al carrito</button>
            </div>
        </div>
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

    form{
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 6% auto;
        gap: 1rem;
        width: 80%;
    }

    form input{
        border: none;
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 2px solid #fcbf00;
    }

    form input::placeholder{
        color: #fcbf00;
        font-style: italic;
        opacity: 0.7;
    }

    form button{
        display: flex;
        align-items: center;
        gap: 0.3rem;
        background-color: #ff8800;
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
    }

    form button:hover{
        background-color: #fcbf00;
        color: black;
        transform: translateY(-0.125rem);
    }

    #todosProductos{
        display: flex;
        flex-wrap: wrap;
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