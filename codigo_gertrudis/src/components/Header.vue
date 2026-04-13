<script setup>
import { RouterLink } from 'vue-router';

//declaramos un array con lso nombres de cada compañeras
const companeras = ["Lucía", "Ainhoa", "Nazaret", "Alba"];

</script>

<template>
    <nav class="navbar navbar-expand-lg bg-body">
        <div class="container-fluid">
            <div id="logo">
                <RouterLink to="/" class="nav-link">
                    <img src="/logotipo.png" alt="Logotipo de Código Gertrudis">
                </RouterLink>
            </div>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Encuentra tu producto..." aria-label="Buscar productos"/>
                <button type="submit">Buscar</button>
            </form>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <RouterLink to="/conocenos" class="nav-link">Conócenos<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/tienda" class="nav-link">Tienda<span class="sr-only"></span></RouterLink>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Compañeras</a>
                        <ul class="dropdown-menu">
                            <li v-for="(companera, index) in companeras" :key="companera">
                                <RouterLink :to="`/companera/${companera.toLocaleLowerCase()}`" class="dropdown-item">{{ companera }}</RouterLink>
                                <hr v-if="index !== companeras.length - 1" class="dropdown-divider">
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <RouterLink to="/contacto" class="nav-link">Contacto<span class="sr-only"></span></RouterLink>
                    </li>
                </ul>
            </div>
            <div class="button">
                <RouterLink to="/login" class="nav-link">
                    <i class="bi bi-person-circle"></i>
                    <span>Iniciar sesión</span>
                </RouterLink>
            </div>
        </div>
    </nav>
</template>

<style scoped>

*{
    font-family: "Noto Sans", sans-serif;
}

.navbar{
    position: relative;
    margin: auto;
    justify-content: center;
    align-items: center;
}

#logo img{
    width: 12vw;
}

.d-flex{
    margin-left: 3%;
    margin-right: 3%;
}

.d-flex input{
    border: 1px solid #fcbf00;
    border-radius: 12px;
    color: #fcbf00;
}

.d-flex input::placeholder{
    color: #fcbf00;
    opacity: 0.7;
    font-style: italic;
}

.d-flex button{
    background-color: #fcbf00;
    border: none;
    border-radius: 10px;
    padding: 4%;
    color: white;
    font-weight: bold;
    font-size: 105%;
    transition: all 0.3s ease;
}

.d-flex button:hover {
  background-color: #ff8800;
  transform: translateY(-2px);
}

li .nav-link{
    color: #fcbf00;
}

.router-link-active {
    color: #ff8800;
}

.dropdown-menu {
    padding: 0;
    border: 1px solid #fcbf00;
}

.dropdown-toggle {
    color: #fcbf00;
}

.dropdown-item {
    color: #fcbf00;
    display: block;
    width: 100%;
    padding: 10px 16px;
}


.dropdown-item:hover {
    color: white;
    background-color: #ff8800;
    opacity: 0.8;
    font-size: 110%;
}

.dropdown-divider {
    margin: 0;
    border-top: 1px solid #fcbf00;
}

.button {
    border: none;
    background-color: #fcbf00;
    color: white;
    border-radius: 0px 0px 0px 30px;
    padding: 1%;
    width: 18%;
    height: 70%;
    font-size: 110%;
    font-weight: bold;
    position: absolute;
    top: 0;
    right: 0;
    margin-top: -1%;
    margin-right: -1%;
}

.button:hover{
    background-color: #ff8800;
}

.button i{
    font-size: 160%;
    margin-right: 5%;
    margin-left: 8%;
}

.button a {
    display: flex;
    align-items: center;
    width: 100%;
    height: 100%;
    margin-top: 2%;
}

</style>