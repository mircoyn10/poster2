<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Controlla se l'articolo è disponibile prima di renderizzare il contenuto -->
    <div v-if="article" class="relative h-64 bg-cover bg-center flex items-center justify-center" :style="{ backgroundImage: 'url(/storage/img/minimalist_back_3.jpg)' }">
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <h1 class="relative z-10 text-white text-4xl font-bold">{{ article.title }}</h1>
    </div>

    <div v-else>
      <!-- Mostra un messaggio di caricamento o un'animazione -->
      <p class="text-center text-gray-600 mt-10">Caricamento articolo...</p>
    </div>

    <!-- Contenitore principale dell'articolo -->
    <div v-if="article" class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6 mt-6">
      <!-- Autore e Data -->
      <div class="flex justify-between items-center mb-6">
        <p class="text-gray-600">
          Pubblicato il {{ formatDate(article.created_at) }} da
          <span class="text-indigo-600">{{ article.user.name }}</span>
        </p>
        <img
          :src="article.user.profile_photo_url"
          alt="User Avatar"
          class="h-10 w-10 rounded-full"
        />
      </div>

      <!-- Immagine dell'articolo -->
      <div v-if="article.image" class="mb-6">
        <img
          :src="`/storage/${article.image}`"
          :alt="article.title"
          class="w-full h-64 object-cover rounded-md"
        />
      </div>

      <!-- Corpo dell'articolo -->
      <div v-html="article.body" class="prose max-w-none text-gray-800"></div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'; 
import { ref, onMounted, watch } from 'vue';

// Ottieni i props dalla pagina corrente tramite Inertia
const { props } = usePage();
let article = ref(props.article);  // Usa una variabile reattiva per article

// Funzione per formattare la data
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Aggiorna l'articolo ogni volta che cambia
watch(() => props.article, (newArticle) => {
  article.value = newArticle;
});

onMounted(() => {
  article.value = props.article;
});
</script>


<style scoped>
/* Stili specifici per la pagina */
.prose {
  font-family: 'Poppins', sans-serif;
}

.bg-gray-100 {
  background-color: #f7fafc;
}

.bg-cover {
  background-size: cover;
}

.bg-center {
  background-position: center;
}

.text-gray-600 {
  color: #718096;
}
</style>
