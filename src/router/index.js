import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/Home.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  linkActiveClass: 'active',
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/feedback',
      name: 'feedback',
      component: () => import('../views/feedback/Home.vue'), // lazy-loaded when the route is visited
      children: [
        {
          path: '',
          name: 'feedbackOptions',
          component: () => import('../views/feedback/Options.vue'),
        },
        {
          path: 'feedbackCreate',
          name: 'create',
          component: () => import('../views/feedback/Create.vue'),
        },
        {
          path: 'complete',
          name: 'feedbackComplete',
          component: () => import('../views/feedback/Complete.vue'),
        },
        {
          //this option should always be last else other routes will be interpreted as an id
          path: ':id',
          name: 'feedbackGive',
          component: () => import('../views/feedback/Give.vue'),
        },
      ],
    },
    {
      path: '/interact',
      name: 'interact',
      component: () => import('../views/interact/Home.vue'),
      children: [
        {
          path: '',
          name: 'interactOptions',
          component: () => import('../views/interact/Options.vue'),
        },
        {
          path: 'create',
          name: 'interactCreate',
          component: () => import('../views/interact/Create.vue'),
        },
        {
          path: ':id',
          name: 'interactJoin',
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
      name: 'NotFound',
      component: () => import('../views/NotFound.vue'),
    },
  ],
});

export default router;
