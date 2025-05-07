<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faInstagram, faTiktok, faTwitter, faFacebook } from '@fortawesome/free-brands-svg-icons';
import { faTrash, faSpinner, faCopy, faPaperPlane } from '@fortawesome/free-solid-svg-icons';

library.add(faInstagram, faTiktok, faTwitter, faFacebook, faTrash, faCopy, faPaperPlane, faSpinner);

const userPrompt = ref('');
const userCoin = ref(0);
const selectedSocials = ref({
  instagram: false,
  tiktok: false,
  twitter: false,
  facebook: false
});
const promptHistory = ref([]);
const isLoading = ref(false);
const notificationMessage = ref('');

// Axios configuration
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://127.0.0.1:8000';

const loadSavedHistory = async () => {
  try {
    const result = await axios.get('/search-history');
    promptHistory.value = result.data.map(entry => ({
      ...entry,
      timestamp: new Date().toLocaleString(),
      responses: entry.response ? Object.entries(entry.response).map(([platform, content]) => ({
        platform,
        content,
        isGenerating: false
      })) : []
    }));
  } catch (error) {
    console.error('Error loading search history:', error);
  }
};

const loadUserCoins = async () => {
  try {
    const response = await axios.get('/user/coin');
    userCoin.value = response.data.coin;
  } catch (error) {
    console.error('Error fetching user coins:', error);
  }
};

const toggleSelection = (platform) => {
  selectedSocials.value[platform] = !selectedSocials.value[platform];
};

const showNotification = (message) => {
  notificationMessage.value = message;
  setTimeout(() => notificationMessage.value = '', 3000);
};

const submitPrompt = async () => {
  const selectedPlatforms = Object.keys(selectedSocials.value).filter(platform => selectedSocials.value[platform]);
  
  if (selectedPlatforms.length === 0) {
    alert('Please select at least one platform.');
    return;
  }

  isLoading.value = true;
  const newEntry = {
    prompt: userPrompt.value,
    timestamp: new Date().toLocaleString(),
    responses: selectedPlatforms.map(platform => ({
      platform,
      content: '',
      isGenerating: true
    }))
  };
  promptHistory.value.unshift(newEntry);

  try {
    for (const platform of selectedPlatforms) {
      const response = await axios.post('/api/generate-content', {
        prompt: userPrompt.value,
        platforms: { [platform]: true }
      });

      if (response.data.responses) {
        userCoin.value = response.data.remaining_coins;
        const responseIndex = newEntry.responses.findIndex(r => r.platform === platform);
        if (responseIndex !== -1) {
          newEntry.responses[responseIndex].content = response.data.responses[platform];
          newEntry.responses[responseIndex].isGenerating = false;
        }
      }
    }
    userPrompt.value = '';
  } catch (error) {
    console.error('Error submitting prompt:', error);
    if (error.response && error.response.data.error === 'Not enough coins') {
      alert('Non hai abbastanza coin per generare il contenuto.');
    }
  } finally {
    isLoading.value = false;
  }
};

const clearHistory = async () => {
  try {
    await axios.post('/clear-history');
    promptHistory.value = [];
    showNotification('Cronologia cancellata con successo');
  } catch (error) {
    console.error('Error clearing history:', error);
  }
};

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text).then(() => {
    showNotification('Copied to clipboard!');
  }).catch(err => {
    console.error('Failed to copy: ', err);
  });
};

onMounted(async () => {
  loadSavedHistory();
  loadUserCoins();
});
</script>

<template>
  <div class="chat-container">
    <!-- Notification -->
    <div v-if="notificationMessage" class="notification">
      {{ notificationMessage }}
    </div>

    <!-- Main Chat Interface -->
    <div class="chat-interface">
      <!-- Header -->
      <div class="chat-header">
        <h1>Poster AI Chat</h1>
        <div class="coin-display">
          <span>{{ userCoin }}</span>
          <span>Coins</span>
        </div>
      </div>

      <!-- Chat History -->
      <div class="chat-history">
        <div v-for="(entry, index) in promptHistory" :key="index" class="chat-entry">
          <!-- User Prompt -->
          <div class="user-message">
            <div class="message-content">
              {{ entry.prompt }}
            </div>
          </div>

          <!-- AI Responses -->
          <div v-for="response in entry.responses" :key="response.platform" class="ai-message">
            <div class="platform-icon">
              <font-awesome-icon :icon="['fab', response.platform]" />
            </div>
            <div class="message-content">
              <div v-if="response.isGenerating" class="loading-message">
                <font-awesome-icon :icon="['fas', 'spinner']" spin />
                Generating...
              </div>
              <div v-else>
                {{ response.content }}
                <button @click="copyToClipboard(response.content)" class="copy-button">
                  <font-awesome-icon :icon="['fas', 'copy']" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Area -->
      <div class="chat-input">
        <!-- Platform Selector -->
        <div class="platform-selector">
          <button
            v-for="platform in ['instagram', 'tiktok', 'twitter', 'facebook']"
            :key="platform"
            @click="toggleSelection(platform)"
            :class="{ active: selectedSocials[platform] }"
          >
            <font-awesome-icon :icon="['fab', platform]" />
          </button>
        </div>

        <!-- Text Input -->
        <div class="text-input">
          <textarea
            v-model="userPrompt"
            placeholder="Type your prompt here..."
            :disabled="isLoading"
          ></textarea>
          <button @click="submitPrompt" :disabled="isLoading">
            <font-awesome-icon :icon="['fas', isLoading ? 'spinner' : 'paper-plane']" :spin="isLoading" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.chat-container {
  @apply h-full flex flex-col bg-gray-50;
}

.notification {
  @apply fixed top-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md z-50;
}

.chat-interface {
  @apply flex flex-col h-full max-w-3xl mx-auto w-full bg-white shadow-lg rounded-lg overflow-hidden;
}

.chat-header {
  @apply flex justify-between items-center p-4 border-b border-gray-200;
  h1 {
    @apply text-xl font-bold text-gray-800;
  }
  .coin-display {
    @apply flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full;
    span {
      @apply text-sm font-medium;
      &:first-child {
        @apply text-indigo-600;
      }
    }
  }
}

.chat-history {
  @apply flex-1 overflow-y-auto p-4 space-y-4;
}

.chat-entry {
  @apply space-y-2;
}

.user-message {
  @apply flex justify-end;
  .message-content {
    @apply bg-indigo-500 text-white px-4 py-2 rounded-2xl rounded-br-none max-w-[80%];
  }
}

.ai-message {
  @apply flex items-start space-x-2;
  .platform-icon {
    @apply text-xl p-2 rounded-full;
    &.instagram { @apply text-pink-500; }
    &.tiktok { @apply text-blue-400; }
    &.twitter { @apply text-blue-500; }
    &.facebook { @apply text-blue-700; }
  }
  .message-content {
    @apply bg-gray-100 px-4 py-2 rounded-2xl rounded-bl-none max-w-[80%] relative;
    .loading-message {
      @apply flex items-center space-x-2;
    }
    .copy-button {
      @apply absolute -right-2 -top-2 bg-white p-1 rounded-full shadow-md text-gray-500 hover:text-gray-700;
    }
  }
}

.chat-input {
  @apply border-t border-gray-200 p-4;
}

.platform-selector {
  @apply flex justify-center space-x-2 mb-4;
  button {
    @apply p-2 rounded-full transition-all duration-200;
    &:hover {
      @apply transform scale-110;
    }
    &.active {
      @apply bg-indigo-100 text-indigo-600;
    }
  }
}

.text-input {
  @apply flex items-center space-x-2;
  textarea {
    @apply flex-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none;
    min-height: 3rem;
    max-height: 8rem;
  }
  button {
    @apply p-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition-all duration-200;
    &:disabled {
      @apply opacity-50 cursor-not-allowed;
    }
  }
}
</style>
