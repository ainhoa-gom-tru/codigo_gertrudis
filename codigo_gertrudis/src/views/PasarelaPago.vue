<script setup>
import { ApiUrl, bordeNatural, bordeRojo, bordeVerde, estiloNatural, estiloRojo, estiloVerde } from '@/main';
import { computed, ref } from 'vue';

//creamos las siguientes variables
const numeroTarjeta = ref('');
const cvc = ref('');
const fechaVencimiento = ref('');
const mostrarMensaje = ref(false);
const mensaje = ref('');
const mostrarModal = ref(false);

//creamos las siguientes expresiones regulares para realizar las validaciones de los dadtos de la tarjeta
const tarjetaRegex = /^[0-9]{16}$/;
const cvcRegex = /^[0-9]{3}$/;
const fechaRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;

//validamos el numero de la tarjeta de credito
const validarTarjeta = computed(() => {
    if(!tarjetaRegex.test(numeroTarjeta.value) && numeroTarjeta.value.length >= 1){
        return false;
    } else if(numeroTarjeta.value.length === 0){
        return null;
    } else {
        return true;
    }
});

//validamos el cvc de la tarjeta
const validarCvc = computed(() => {
    if(!cvcRegex.test(cvc.value) && cvc.value.length >= 1){
        return false;
    } else if(cvc.value.length === 0){
        return null;
    } else {
        return true;
    }
});

//validamos la fecha de vencimiento de la tarjeta
const validarFecha = computed(() => {
    if(!fechaRegex.test(fechaVencimiento.value) && fechaVencimiento.value.length >= 1){
        return false;
    } else if(fechaVencimiento.value.length === 0){
        return null;
    } else {
        return true;
    }
});

//funcion para simular el pago
function confirmarPedido() {
    fetch(ApiUrl + '/pedidos', {
        method: 'POST',
        credentials: 'include',
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            mensaje.value = "Error al procesar el pago";
            mostrarMensaje.value = true;
            return;
        }
        console.log(data);
        mostrarModal.value = true;
    })
    .catch(err => {
        console.error(err)
        mensaje.value = "Error de conexión con el servidor";
        mostrarMensaje.value = true;
    });
}

//funcion para cerrar el mensaje
function cerrarMensaje(){
    mostrarMensaje.value = false;
}

//function para cerrar el modal
function cerrarModal(){
    mostrarModal.value = false;
}

//hacemos una funcion para redirigirnos a la pagina de login
function continuar(){
    //router.push("/login")
}

</script>

<template>
    <div id="fondo">
        <div v-if="mostrarMensaje" class="mensaje error">
            <button @click="cerrarMensaje">X</button>
            <i class="bi bi-x-lg"></i>
            <p class="texto-mensaje">{{ mensaje }}</p>
        </div>
        <div class="contenedor-pago">
            <img src="/fotoPasarelaPago.png">
            <form @submit.prevent="confirmarPedido">
                <div class="mb-2">
                    <label :style="validarTarjeta === null ? estiloNatural : !validarTarjeta ? estiloRojo : estiloVerde" for="numeroTarjeta" class="form-label">Número de la tarjeta
                        <span v-if="validarTarjeta === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarTarjeta !== null && !validarTarjeta" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <input v-model="numeroTarjeta" :style="validarTarjeta === null ? bordeNatural: !validarTarjeta ? bordeRojo : bordeVerde" type="text" class="form-control" id="numeroTarjeta" aria-label="Número de la tarjeta" placeholder="Ej: 4242 4242 4242 4242">
                    <p v-if="!validarTarjeta && validarTarjeta !== null" :style="estiloRojo">* No debe de haber espacios</p>
                </div>
                <div class="mb-2">
                    <label :style="validarCvc === null ? estiloNatural : !validarCvc ? estiloRojo : estiloVerde" for="cvc" class="form-label">CVC
                        <span v-if="validarCvc === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarCvc !== null && !validarCvc" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <input v-model="cvc" :style="validarCvc === null ? bordeNatural : !validarCvc ? bordeRojo : bordeVerde" type="text" class="form-control" id="cvc" aria-label="cvc de la tarjeta" placeholder="Ej: 123">
                    <p v-if="!validarCvc && validarCvc !== null" :style="estiloRojo">* El formato del CVC no es correcto</p>
                </div>
                <div class="mb-2">
                    <label :style="validarFecha === null ? estiloNatural : !validarFecha ? estiloRojo : estiloVerde" for="fechaVencimiento" class="form-label">Fecha de vencimiento
                        <span v-if="validarFecha === null" :style="estiloNatural">*</span>
                        <i v-else-if="validarFecha !== null && !validarFecha" :style="estiloRojo" class="bi bi-x"></i>
                        <i v-else :style="estiloVerde" class="bi bi-check"></i>
                    </label>
                    <input v-model="fechaVencimiento" :style="validarFecha === null ? bordeNatural : !validarFecha ? bordeRojo : bordeVerde" type="text" class="form-control" id="fechaVencimiento" aria-label="Fecha de vencimiento" placeholder="Ej: 04/38">
                    <p v-if="validarFecha === false" :style="estiloRojo">* El formato de la fecha no es correcto</p>
                </div>
                <button type="submit" class="btn-pagar" @click="confirmarPedido">
                    <i class="bi bi-wallet-fill"></i>
                    Pagar
                </button>
            </form>
        </div>
    </div>
    <div v-if="mostrarModal" class="modal-overlay"  tabindex="-1" role="dialog">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" :style="estiloVerde">Pago exitoso</h5>
                    <button type="button" class="btn-close" @click="mostrarModal = false" aria-label="Cerrar modal"></button>
                </div>
                <div class="modal-body">
                    <p>¡Has realizado tu pago con éxito!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" aria-label="Botón para cerrar modal" @click="mostrarModal = false">Cerrar</button>
                    <button type="button" class="btn btn-registro" aria-label="Botón para reealizar continuar" @click="continuar">Continuar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

    #fondo{
        background: linear-gradient(90deg, #ff8800,#fcbf00);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    form{
        background-color: white;
    }

    .contenedor-pago{
        display: flex;
        width: 90%;
        height: 90vh;
        border-radius: 40px;
        overflow: hidden;
    }

    .contenedor-pago img{
        width: 50%;
        object-fit: cover;
    }

    .contenedor-pago form{
        width: 50%;
        background-color: white;
        padding: 5%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .mensaje {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 1.2rem 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #8b2c2c, #6e1f1f);
        color: #ffdede;
    }

    .mensaje i {
        font-size: 1.5rem;
        background: rgba(255,255,255,0.15);
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .texto-mensaje {
        margin: 0;
        font-weight: 500;
    }

    .mensaje button {
        position: absolute;
        right: 1rem;
        top: 1rem;
        background: transparent;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
        color: inherit;
    }

    .btn-pagar {
        background-color: #fcbf00;
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-pagar:hover{
        background-color: #ff8800;
        color: white;
        transform: translateY(-0.125rem);
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

    .modal-header {
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

</style>