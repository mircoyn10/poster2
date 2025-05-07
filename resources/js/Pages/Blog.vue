<template>
  <AppLayout>
    <Head title="Blog" />
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Poster Blog</h2>
    </template>

    <div class="py-12 bg-gray-100" style="background-image: url('http://127.0.0.1:8000/storage/img/minimalist_back_3.jpg'); background-repeat: repeat; background-position: top left;">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Index Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8 p-8 bg-opacity-80">
          <h2 class="text-3xl font-bold mb-8 text-indigo-700">Article Index</h2>
          <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <li v-for="article in allArticles" :key="article.id">
              <Link
                :href="`/article/${article.id}`"
                class="text-lg text-indigo-600 hover:text-indigo-800 transition duration-300 flex items-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 010-1.414L10.586 10 7.293 6.707a1 1 011.414-1.414l4 4a1 1 010 1.414l-4 4a1 1-1.414 0z" clip-rule="evenodd" />
                </svg>
                {{ article.title }}
              </Link>
            </li>
          </ul>
        </div>

        <!-- Latest Two Articles Section -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 mb-8 bg-opacity-80">
          <h2 class="text-3xl font-bold mb-8 text-indigo-700">Featured Articles</h2>
          <div v-if="latestArticles.length === 0" class="text-gray-500 text-center py-10">
            <p class="text-xl">No articles yet.</p>
            <p class="mt-2">Be the first to share your thoughts!</p>
          </div>
          <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <article
              v-for="article in latestArticles"
              :key="article.id"
              class="bg-white p-6 rounded-lg shadow-md transition duration-300 hover:shadow-xl border border-gray-200"
            >
              <h3 class="text-2xl font-semibold mb-3 text-gray-800 hover:text-indigo-600 transition duration-300">
                <Link :href="`/article/${article.id}`">
                  {{ article.title }}
                </Link>
              </h3>

              <div class="relative">
                <img
                  v-if="article.image"
                  :src="`/storage/${article.image}`"
                  :alt="article.title"
                  class="w-full h-64 object-cover rounded-md mb-4"
                />
                <div 
                  class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-50 rounded-md"
                  v-if="article.image"
                ></div>
              </div>

              <div 
                v-html="getArticleBody(article)" 
                class="text-gray-600 mb-4 leading-relaxed prose prose-sm max-w-none"
              ></div>

              <Link 
                v-if="article.body.length > 150" 
                :href="`/article/${article.id}`"
                class="text-indigo-600 hover:text-indigo-800 transition duration-300 mb-4 flex items-center"
              >
                Read more
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 011.414 0L10 10.586l3.293-3.293a1 1 111.414 1.414l-4 4a1 1-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                </svg>
              </Link>

              <div class="flex items-center justify-between text-sm text-gray-500 mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 100-6 3 3 000 6zm-7 9a7 7 0114 0H3z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ article.user.name }}</span>
                </div>
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 000-1v1H4a2 2 002-2v10a2 2 002 2h12a2 2 002-2V6a2 2 002-2h-1V3a1 1 10-2 0v1H7V3a1 1 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
            </article>
          </div>
        </div>

        <!-- All Articles Section with Pagination -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 bg-opacity-80">
          <h2 class="text-3xl font-bold mb-8 text-indigo-700">All Articles</h2>
          <div v-if="paginatedArticles.length === 0" class="text-gray-500 text-center py-10">
            <p class="text-xl">No articles available.</p>
          </div>
          <div v-else class="space-y-8">
            <article
              v-for="article in paginatedArticles"
              :key="article.id"
              class="bg-white p-6 rounded-lg shadow-md transition duration-300 hover:shadow-xl border border-gray-200"
            >
              <h3 class="text-2xl font-semibold mb-3 text-gray-800 hover:text-indigo-600 transition duration-300">
                {{ article.title }}
              </h3>

              <div v-html="getArticleBody(article)" class="text-gray-600 mb-4 leading-relaxed prose prose-sm max-w-none"></div>

              <button 
                v-if="article.body.length > 150" 
                @click="toggleArticle(article)" 
                class="text-indigo-600 hover:text-indigo-800 transition duration-300 mb-4 flex items-center"
              >
                {{ article.expanded ? 'Read less' : 'Read more' }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 011.414 0L10 10.586l3.293-3.293a1 1 111.414 1.414l-4 4a1 1-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>

              <div class="flex items-center justify-between text-sm text-gray-500 mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 100-6 3 3 000 6zm-7 9a7 7 0114 0H3z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ article.user.name }}</span>
                </div>
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 000-1v1H4a2 2 002-2v10a2 2 002 2h12a2 2 002-2V6a2 2 002-2h-1V3a1 1 10-2 0v1H7V3a1 1 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  <span>{{ formatDate(article.created_at) }}</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Pagination controls -->
          <div class="mt-6 flex justify-center">
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-4 py-2 mx-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              Previous
            </button>
            <span class="text-gray-600 mx-2">Page {{ currentPage }}</span>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-4 py-2 mx-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  components: {
    Head,
    Link
  },
  data() {
    return {
      allArticles: [],
      latestArticles: [],
      paginatedArticles: [],
      currentPage: 1,
      totalPages: 0,
    };
  },
  methods: {
    async fetchArticles() {
      try {
        const response = await axios.get(`/articles?page=${this.currentPage}`);
        this.allArticles = response.data.data.map(article => ({
          ...article,
          expanded: false,
        }));
        this.totalPages = response.data.meta.last_page;
        this.paginatedArticles = this.allArticles;
      } catch (error) {
        console.error('Error fetching articles:', error);
      }
    },
    async fetchLatestArticles() {
      try {
        const response = await axios.get('/articles/latest');
        this.latestArticles = response.data.data;
      } catch (error) {
        console.error('Error fetching latest articles:', error);
      }
    },
    getArticleBody(article) {
      return article.expanded ? article.body : article.body.substring(0, 150);
    },
    toggleArticle(article) {
      article.expanded = !article.expanded;
    },
    goToPage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
      this.fetchArticles();
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
  },
  mounted() {
    this.fetchArticles();
    this.fetchLatestArticles();
  },
};
</script>
