<script setup>
import { RouterLink } from 'vue-router';

//declaramos un array con lso nombres de cada compañeras
const companeras = ["Lucía", "Ainhoa", "Nazaret", "Alba"];

</script>

<template>
    <div id="fondo">
        <div class="row">
            <div class="col logo">
                <img src="/logotipo_invertido.png" alt="logo">
            </div>
            <div class="col">
                <h5>Políticas</h5>
                <span>
                    <RouterLink to="/politicas-privacidad" class="dropdown-item">Políticas de privacidad</RouterLink>
                </span>
                <span>
                    <RouterLink class="dropdown-item">Políticas de cookies</RouterLink>
                </span>
                <span>
                    <RouterLink class="dropdown-item">Términos y condiciones</RouterLink>
                </span>
            </div>
            <div class="col">
                <h5>Compañeras</h5>
                <span v-for="companera in companeras">
                    <RouterLink :to="`/companera/${companera.toLocaleLowerCase()}`" class="dropdown-item">{{ companera }}</RouterLink>
                </span>
            </div>
            <div class="col">
                <i class="bi bi-envelope"></i>
                <i class="bi bi-instagram"></i>
                <i class="bi bi-youtube"></i>
            </div>
        </div>
    </div>
</template>

<style scoped>

    #fondo {
        background-color: #fcbf00;
        color: black;
        padding: 2rem 1.5rem;
    }

    .row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .col {
        flex: 1;
        min-width: 180px;
    }

    .logo {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .logo img {
        width: 10rem;
        max-width: 100%;
        transition: transform 0.3s ease;
    }

    .logo img:hover {
        transform: scale(1.05);
    }

    h5 {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 1rem;
        color: #000;
    }

    span {
        display: block;
        margin-bottom: 0.5rem;
    }

    a {
        text-decoration: none;
        color: black;
        font-size: 0.95rem;
        transition: all 0.25s ease;
        padding: 0.2rem 0.4rem;
        border-radius: 0.4rem;
        font-weight: 500;
    }

    a:hover {
        color: #ff8800;
        background-color: white;
        transform: translateX(0.2rem);
    }

    .col:last-child {
        display: flex;
        gap: 1rem;
        justify-content: center;
        align-items: center;
    }

    .col:last-child i {
        font-size: 2rem;
        cursor: pointer;
        transition: all 0.25s ease;
        padding: 0.3rem;
        border-radius: 0.4rem;
        margin-top: 3.5rem;
    }

    .col:last-child i:hover {
        color: #ff8800;
        background-color: white;
        transform: translateY(-0.2rem) scale(1.1);
    }

</style>