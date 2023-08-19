import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Home.vue';

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
          path: 'create',
          name: 'feedback-create',
          component: () => import('../feedback/Create.vue'),
        },
        {
          path: 'complete',
          name: 'feedback-complete',
          component: () => import('../feedback/Complete.vue'),
        },
        {
          //this option should always be last else other routes will be interpreted as an id
          path: ':id',
          name: 'feedback-give',
          component: () => import('../feedback/Give.vue'),
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
          component: () => import('../interact/Create.vue'),
        },
        {
          path: 'host/:id?', //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: 'interact-host',
          component: () => import('../interact/Host.vue'),
        },
        {
          path: ':id',
          name: 'interact-join',
          component: () => import('../interact/Join.vue'),
        },
      ],
    },
    {
      path: '/privacy-policy',
      name: 'privacy-policy',
      component: () => import('../PrivacyPolicy.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../NotFound.vue'),
    },
  ],
});

export default router;
