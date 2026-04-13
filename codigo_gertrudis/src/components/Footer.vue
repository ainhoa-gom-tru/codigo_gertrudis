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
                    <RouterLink to="/politicas-privacidad">Políticas de privacidad</RouterLink>
                </span>
                <span>
                    <RouterLink>Políticas de cookies</RouterLink>
                </span>
                <span>
                    <RouterLink>Términos y condiciones</RouterLink>
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

    #fondo{
        background-color: #fcbf00;
        color: white;
    }

    .logo{
        display: flex;
        align-items: center;
        position: relative;
    }

    img{
        width: 80%;
    }

    h5{
        margin-top: 12%;
    }

    span{
        display: flex;
    }

    span i{
        margin-top: 1%;
        margin-right: 2%;
    }

    p{
        text-decoration: underline;
        cursor: pointer;
        margin-bottom: 2%;
    }

    .redesSociales{
        cursor: pointer;
        width: 12%;
        margin-top: 30%;
        margin-left: 5%;
    }

</style>