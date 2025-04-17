import { createRouter, createWebHistory } from "vue-router";
import Home from "../Home.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  linkActiveClass: "active",
  routes: [
    {
      path: "/feedback",
      name: "feedback",
      children: [
        {
          path: "create",
          redirect: "feedback/create/type",
          children: [
            {
              path: "type",
              name: "feedback-create-type/",
              component: () => import("../feedback/CreateType.vue"),
            },
            {
              path: "details/:id?",
              name: "feedback-create-details",
              alias: "/feedback/edit/details/:id?",
              component: () => import("../feedback/CreateDetails.vue"),
            },
            {
              path: "sessions/:id?",
              name: "feedback-create-subsessions",
              alias: "/feedback/edit/sessions/:id?",
              component: () => import("../feedback/CreateSubsessions.vue"),
            },
            {
              path: "questions/:id?",
              name: "feedback-create-questions",
              alias: "/feedback/edit/questions/:id?",
              component: () => import("../feedback/CreateQuestions.vue"),
            },
            {
              path: "organisers/:id?",
              name: "feedback-create-organisers",
              alias: "/feedback/edit/organisers/:id?",
              component: () => import("../feedback/CreateOrganisers.vue"),
            },
            {
              path: "complete/:id?",
              name: "feedback-create-complete",
              component: () => import("../feedback/CreateComplete.vue"),
            },
          ],
        },
        {
          path: "instructions/:id?",
          name: "feedback-instructions",
          component: () => import("../feedback/Instructions.vue"),
        },
        {
          path: "edit/:id?",
          name: "feedback-edit",
          component: () => import("../feedback/Edit.vue"),
        },
        {
          path: "complete",
          name: "feedback-complete",
          component: () => import("../feedback/Complete.vue"),
        },
        {
          path: "view/:id?",
          name: "feedback-view",
          component: () => import("../feedback/View.vue"),
        },
        {
          path: "attendance/:id?",
          name: "feedback-attendance",
          component: () => import("../feedback/Attendance.vue"),
        },
        {
          path: "resetPIN/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "feedback-resetPIN",
          component: Home,
        },
        {
          path: "notifications/:id?",
          name: "feedback-notifications",
          component: Home,
        },
        {
          //this option should always be last else other routes will be interpreted as an id
          path: ":id?",
          name: "feedback-give",
          component: () => import("../feedback/Give.vue"),
        },
      ],
    },
    {
      path: "/interaction",
      name: "interaction",
      children: [
        {
          path: "create",
          redirect: "interaction/create/type",
          children: [
            {
              path: "type",
              name: "interaction-create-type",
              component: () => import("../interaction/CreateType.vue"),
            },
            {
              path: "details/:id?",
              name: "interaction-create-details",
              alias: "/interaction/edit/details/:id?",
              component: () => import("../interaction/CreateDetails.vue"),
            },
            {
              path: "login",
              name: "interaction-create-login",
              component: () => import("../interaction/CreateLogin.vue"),
            },
            {
              path: "slides/:id?",
              name: "interaction-create-slides",
              alias: "/interaction/edit/slides/:id?",
              component: () => import("../interaction/CreateSlides.vue"),
            },
            {
              path: "complete",
              name: "interaction-create-complete",
              component: () => import("../interaction/CreateComplete.vue"),
            },
          ],
        },
        {
          path: "instructions/:id?",
          name: "interaction-instructions",
          component: () => import("../interaction/Instructions.vue"),
        },
        {
          path: "host/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "interaction-host",
          component: () => import("../interaction/Host.vue"),
        },
        {
          path: "presenter-view/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "interaction-presenter-view",
          component: () => import("../interaction/PresenterView.vue"),
        },
        {
          path: "edit/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "interaction-edit",
          component: () => import("../interaction/Edit.vue"),
        },
        {
          path: "resetPIN/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "interaction-resetPIN",
          component: Home,
        },
        {
          path: ":id?",
          name: "interaction-join",
          component: () => import("../interaction/Join.vue"),
        },
      ],
    },
    {
      path: "/privacy-policy",
      name: "privacy-policy",
      component: () => import("../PrivacyPolicy.vue"),
    },
    {
      path: "/:id?",
      name: "home",
      component: Home,
    },
  ],
});

export default router;
