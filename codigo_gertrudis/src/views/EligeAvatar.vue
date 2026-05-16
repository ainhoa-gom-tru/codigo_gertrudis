<script setup>
import { ApiUrl, estiloVerde } from '@/main';
import router from '@/router';
import { ref } from 'vue';

//creamos la siguiente variable
const avatarSeleccionado = ref('');
const exito = ref(false);

//recuperamos el usuario logueaod del local storage
const usuarioLogueado = JSON.parse(localStorage.getItem('user') || 'null');

//creamos una variable la imagen del usuario logueado
const fotoUsuario = ref('');
const rolUsuario = ref('');

if(usuarioLogueado){
  fotoUsuario.value = usuarioLogueado.avatar;
  avatarSeleccionado.value = usuarioLogueado.avatar;
  rolUsuario.value = usuarioLogueado.rol;
}

//array con las fotos de los avatares
const avatars = ['avatar1.png','avatar2.png','avatar3.png','avatar4.png','avatar5.png','avatar6.png','avatar7.png','avatar8.png','avatar9.png','avatar10.png','avatar11.png','avatar12.png'];


//funcion para cambiar  el avatar
function cambiarAvatar(avatar){
  fotoUsuario.value = avatar;
  avatarSeleccionado.value = avatar;
}

//funciton para actualziar el avatar del usuario
function actualizarAvatar(){
  fetch(ApiUrl + '/usuarios', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      id: usuarioLogueado.id,
      avatar: fotoUsuario.value
    })
  })
  .then(response => response.json())
  .then(data => {
    console.log('Respuesta:', data)
    usuarioLogueado.avatar = fotoUsuario.value;
    localStorage.setItem('user', JSON.stringify(usuarioLogueado));
    fotoUsuario.value = fotoUsuario.value;
    exito.value = true;
  })
  .catch(error => console.error('Error:', error));
}


//funcion para volver al home si le das a salir
function salir(){
    if(rolUsuario.value === 'admin'){
        router.push("/gestion-usuarios")
    } else if (rolUsuario.value === 'desarrollador'){
        router.push("/gestion-juegos")
    } else {
        router.push("/");
    }
}

//funcion para cerrar el modal
function cerrarModal(){
    exito.value = false;
}

</script>

<template>
    <div class="contenedor-avatar">
        <aside>
            <button v-for="(avatar, index) in avatars" :key="index" @click="cambiarAvatar(avatar)" :class="{ activo: avatarSeleccionado === avatar }">
                <img :src="`/avatar/${avatar}`" alt="avatar">
            </button>
        </aside>
        <main>
            <img :src="`/avatar/${fotoUsuario}`" class="img-circle" alt="Avatar de tu usuario">
        </main>
        <section>
            <button @click="actualizarAvatar">Guardar</button>
            <button @click="salir">Salir</button>
        </section>
    </div>
    <div v-if="exito" class="modal-overlay " tabindex="-1" role="dialog">
        <div class="modal-container" >
            <div class="modal-content">
                <div class="modal-header">
                    <h1 :style="estiloVerde" class="modal-title fs-5" id="eliminarUsuario">Avatar actualizado exitosamente</h1>
                    <button type="button" class="btn-close" @click="cerrarModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¡Has actualizado tu avatar con éxito!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" @click="cerrarModal">Cerrar</button>
                </div>     
            </div>
        </div>
    </div>
</template>

<style scoped>

    .contenedor-avatar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4rem;
        min-height: 100vh;
        padding: 1rem;
        flex-wrap: wrap;
    }

    aside {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1.5rem;
        align-content: start;
        flex: 1;
    }

    aside button {
        background-color: white;
        border: 0.125rem solid transparent;
        border-radius: 1rem;
        padding: 0.4rem;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    aside button:hover {
        border-color: #fcbf00;
        transform: translateY(-0.15rem) scale(1.03);
    }

    aside button.activo {
        border-color: #ff8800;
        background-color: #ff8800;
        transform: scale(1.08);
    }

    aside img {
        width: 4.2rem;
        height: 4.2rem;
        border-radius: 50%;
        object-fit: cover;
    }

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        flex: 2;
    }

    .img-circle {
        width: 12rem;
        height: 12rem;
        border-radius: 50%;
        object-fit: cover;
    }

    section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        flex: 1;
    }

    section button {
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 0.6rem;
        padding: 0.7rem 1.2rem;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    section button:hover {
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.15rem);
    }

    section button:last-child {
        background-color: #ff8800;
        color: white;
    }

    section button:last-child:hover {
        background-color: #fcbf00;
        color: black;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-container {
        width: 90%;
        max-width: 25rem;
        border-radius: 0.875rem;
        background: white;
        box-shadow: 0 0.625rem 1.875rem rgba(0,0,0,0.3);
        padding: 1.5rem;
    }

    .modal-content {
        width: 100%;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 0.0625rem solid #eee;
        padding-bottom: 1rem;
    }

    .modal-body {
        padding: 1rem 0;
        font-size: 1rem;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        padding-top: 1rem;
    }

    .btn-cerrar {
        background-color: #ff8800;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-cerrar:hover{
        background-color: #fcbf00;
        color: black;
        transform: translateY(-0.125rem);
    }

    .btn-close{ 
        background-color: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .btn-close:hover {
        transform: none;
        background-color: transparent;
    }

</style>