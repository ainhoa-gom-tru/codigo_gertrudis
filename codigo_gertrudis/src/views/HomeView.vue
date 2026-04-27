<script setup>
</script>

<template>

  <section id="primera">
    <div>
      <button>
        <span><RouterLink to="/conocenos">Pincha para conocer la historia</RouterLink></span>
        <i class="bi bi-arrow-right"></i>
      </button>
    </div>
    <video id="medio" autoplay loop>
      <source src="/video_home.webm" type="video/webm">
      <source src="/video_home.mp4" type="video/mp4">
      Tu navegador no soporta el vídeo.
    </video>
  </section>

</template>

<style scoped>

</style>