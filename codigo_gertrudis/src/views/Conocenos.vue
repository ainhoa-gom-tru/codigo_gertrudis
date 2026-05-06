<script setup></script>

<template>
    <div  class="hero">
        <video id="medio" autoplay muted loop>
            <source src="/video_conocenos.webm" type="video/webm">
            <source src="/video_conocenos.mp4" type="video/mp4">
            Tu navegador no soporta el vídeo.
        </video>
        <h2>Nuestra historia</h2>
    </div>
    <section>
        <img src="/compis.png" alt="Ilustración de las cuatros personajes">
        <p>Todo comienza con un grupo de WhatsApp entre cuatro desconocidas y una casera en común. Al principio, ninguna apostaba por que fueran a llevarse bien: las primeras impresiones, basadas únicamente en las fotos de perfil, no fueron precisamente acertadas. Eran muy distintas entre sí, o al menos eso creían. Sin embargo, el tiempo acabó demostrando lo contrario. Aquellas que un día pensaron que no encajarían, hoy son mucho más que compañeras de piso: son una familia elegida.</p>
    </section>
    <section>
        <p>Es por eso que Ainhoa decidió hacer su Trabajo de Fin de Grado sobre sus compañeras de piso. Quería crear algo especial, algo que las representara a todas a pesar de sus diferencias, algo muy “Gertrudis”. Después de barajar muchas ideas, incluso la posibilidad de un juego de mesa, acabó entendiendo que la mejor forma de contarlo era a través de un espacio donde poder compartir su experiencia con el mundo.</p>
        <img src="/ainhoa.png" alt="Ilustración de Ainhoa">
    </section>
    <section>
        <img src="/revista.jpg" alt="Ilustración de la revista edición número 1">
        <p>Y así nace Código Gertrudis, un pequeño espacio pensado para todas las personas que van a comenzar su vida estudiantil fuera de casa. Un lugar donde descubrir que compartir piso no siempre es tan “traumático” como lo pintan, y que a veces puedes coincidir con personas increíbles, capaces de convertir una etapa nueva en una experiencia inolvidable. Porque, al final, quién sabe… quizás tú también acabes formando tu propio Código Gertrudis.</p>
    </section>
</template>

<style scoped>

.hero{
        position: relative;
    }

    .hero video{
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

    section {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 4rem 6rem;
    }

    section img {
        width: 20rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        object-fit: cover;
    }

    section p {
        flex: 1;
        font-size: 1.2rem;
        line-height: 1.8;
        color: black;
        padding: 1.5rem;
    }

</style>