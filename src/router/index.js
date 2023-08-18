import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  linkActiveClass: 'active',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/feedback',
      name: 'feedback',
      redirect: '/',
      children: [
        {
          path: 'feedback-create',
          name: 'create',
          component: () => import('../views/feedback/Create.vue'),
        },
        {
          path: 'complete',
          name: 'feedback-complete',
          component: () => import('../views/feedback/Complete.vue'),
        },
        {
          //this option should always be last else other routes will be interpreted as an id
          path: ':id',
          name: 'feedback-give',
          component: () => import('../views/feedback/Give.vue'),
        },
      ],
    },
    {
      path: '/interact',
      name: 'interact',
      redirect: '/',
      children: [
        {
          path: 'create',
          name: 'interact-create',
          component: () => import('../views/interact/Create.vue'),
        },
        {
          path: ':id',
          name: 'interact-join',
          component: () => import('../views/interact/Join.vue'),
        },
      ],
    },
    {
      path: '/privacy-policy',
      name: 'privacy-policy',
      component: () => import('../views/PrivacyPolicy.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../views/NotFound.vue'),
    },
  ],
});

export default router;
