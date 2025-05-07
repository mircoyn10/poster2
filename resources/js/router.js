import { createRouter, createWebHistory } from 'vue-router';

// Lazy load delle componenti per migliorare le performance
const ArticleList = () => import('./Pages/ArticleList.vue');

const routes = [
    {
        path: '/',
        name: 'home',
        component: ArticleList,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
