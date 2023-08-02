import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  linkActiveClass: 'active',
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/feedback',
      name: 'feedback',
      component: () => import('../views/feedback/FeedbackView.vue'), // lazy-loaded when the route is visited
      children: [
        {
          path: '',
          name: 'feedbackOptions',
          component: () => import('../views/feedback/FeedbackOptionsView.vue')
        },
        {
          path: 'createSession',
          name: 'createFeedbackSession',
          component: () => import('../views/feedback/CreateFeedbackSessionView.vue')
        },
        {
          path: ':id',
          name: 'giveFeedback',
          component: () => import('../views/feedback/GiveFeedbackView.vue')
        }
      ]
    },
    {
      path: '/interact',
      name: 'interact',
      component: () => import('../views/interact/InteractView.vue'),
      children: [
        {
          path: '',
          name: 'interactOptions',
          component: () => import('../views/interact/InteractOptionsView.vue')
        },
        {
          path: 'createSession',
          name: 'createInteractSession',
          component: () => import('../views/interact/CreateInteractSessionView.vue')
        },
        {
          path: ':id',
          name: 'joinInteract',
          component: () => import('../views/interact/JoinInteractView.vue')
        }
      ]
    },
    {
      path: '/privacy-policy',
      name: 'privacy-policy',
      component: () => import('../views/PrivacyPolicyView.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('../views/NotFoundView.vue')
    }
  ]
})

export default router
