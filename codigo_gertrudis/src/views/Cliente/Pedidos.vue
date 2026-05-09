<script setup>
import { ApiUrl, estiloRojo } from '@/main';
import { computed, ref } from 'vue';

//crea las siguientes variables
const pedidos = ref([]);

//funcion para obtener todos los productos
function obtenerTodosPedidos(){
    fetch(ApiUrl + '/pedidos', {
        method: 'GET',
        credentials: 'include',
    })
    .then(response => response.json())
    .then(data => {
        pedidos.value = data;
        console.log('Pedidos:', data)
    })
    .catch(error => console.error('Error:', error));
}

obtenerTodosPedidos();

//agrupamos los pedidos por id
const pedidosAgrupados = computed(() => {
    return pedidos.value.reduce((acc, item) => {
        const id = item.pedido_id;

        if (!acc[id]) {
            acc[id] = {
                pedido_id: id,
                total_pedido: item.total_pedido,
                fecha_pedido: item.fecha_pedido,
                fecha_entrega: item.fecha_entrega,
                productos: []
            };
        }

        acc[id].productos.push({
            nombre: item.nombre,
            precio: item.precio,
            foto: item.foto,
            cantidad: item.cantidad
        });

        return acc;
    }, {});
});

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-ES');
}

</script>

<template>
    <h4>Lista de tus pedidos</h4>
    <p v-if="pedidos.length === 0" class="carrito" :style="estiloRojo">Aún no has realizado ningún pedido</p>
    <div v-else class="carrito">
        <div v-for="pedido in pedidosAgrupados" class="accordion accordion-flush" id="accordionFlushExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="`#collapse-${pedido.pedido_id}`" aria-expanded="false" :aria-controls="`collapse-${pedido.pedido_id}`">
                    Pedido {{ pedido.pedido_id }}
                </button>
                </h2>
                <div :id="`collapse-${pedido.pedido_id}`" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <p class="fecha">Fecha prevista de entrega: {{ formatearFecha(pedido.fecha_entrega) }}</p>
                    <div class="accordion-body" v-for="producto in pedido.productos">
                        <div class="info-productos">
                            <img :src="`http://localhost:8001/productos/${producto.foto}`" class="card-img-top" alt="Foto del producto">
                            <div class="info">
                                <h4>{{ producto.nombre }}</h4>
                                <p>Unidades: {{ producto.cantidad }}</p>
                                <p>{{ producto.cantidad * producto.precio }} €</p>
                            </div>
                        </div>
                    </div>
                    <p class="total-pedido"><b>Total</b>: {{ pedido.total_pedido }} €</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

    h4 {
        text-align: left;
        color: #fcbf00;
        margin: 2rem;
        font-weight: 700;
    }

    .carrito {
        margin: 0rem 2rem;
        width: 90%;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .accordion {
        border-radius: 1rem;
    }

    .accordion-item {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .fecha{
        margin: 1rem 2rem 0rem 2rem;
    }

    .accordion-button {
        padding: 1.3rem;
        font-weight: 600;
        color: black;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .accordion-button:not(.collapsed) {
        background-color: #fcbf00;
        box-shadow: none;
    }

    .accordion-button:hover {
        background-color: #ff8800;
        color: white;
        box-shadow: none;
        font-weight: 600;
    }

    .accordion-body {
        padding: 1.2rem;
        border-bottom: 1px solid #f1f1f1;
        margin: 1rem 0rem;
    }

    .info-productos {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .card-img-top {
        width: 7rem;
        height: 7rem;
        object-fit: cover;
        border-radius: 1rem;
    }

    .info {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .info h4 {
        margin: 0;
        color: #fcbf00;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .info p {
        margin: 0;
        font-size: 1rem;
    }

    .info p:last-child {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .total-pedido{
        margin: 0rem 2rem;
    }
    
</style>