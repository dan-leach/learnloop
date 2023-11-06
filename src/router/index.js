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
      children: [
        {
          path: 'create',
          name: 'feedback-create',
          component: () => import('../feedback/Create.vue'),
        },
        {
          path: 'created',
          name: 'feedback-created',
          component: () => import('../feedback/Created.vue'),
        },
        {
          path: 'edit/:id?',
          name: 'feedback-edit',
          component: () => import('../feedback/Edit.vue'),
        },
        {
          path: 'complete',
          name: 'feedback-complete',
          component: () => import('../feedback/Complete.vue'),
        },
        {
          path: 'view/:id?',
          name: 'feedback-view',
          component: () => import('../feedback/View.vue'),
        },
        {
          path: 'attendance/:id?',
          name: 'feedback-attendance',
          component: () => import('../feedback/Attendance.vue'),
        },
        {
          //this option should always be last else other routes will be interpreted as an id
          path: ':id?',
          name: 'feedback-give',
          component: () => import('../feedback/Give.vue'),
        },
      ],
    },
    {
      path: '/interact',
      name: 'interact',
      children: [
        {
          path: 'create',
          name: 'interact-create',
          component: () => import('../interact/Create.vue'),
        },
        {
          path: 'created',
          name: 'interact-created',
          component: () => import('../interact/Created.vue'),
        },
        {
          path: 'joining-instructions/:id?',
          name: 'joining-instructions',
          component: () => import('../interact/JoiningInstructions.vue'),
        },
        {
          path: 'host/:id?', //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: 'interact-host',
          component: () => import('../interact/Host.vue'),
        },
        {
          path: 'edit/:id?', //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: 'interact-host',
          component: () => import('../interact/Edit.vue'),
        },
        {
          path: ':id?',
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
