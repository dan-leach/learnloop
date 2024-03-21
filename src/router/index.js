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
          name: "feedback-create",
          component: () => import("../feedback/Create.vue"),
        },
        {
          path: "created",
          name: "feedback-created",
          component: () => import("../feedback/Created.vue"),
        },
        {
          path: "instructions/:id?",
          name: "feedback-instructions",
          component: () => import("../feedback/Instructions.vue"),
        },
        {
          path: "edit/:id?",
          name: "feedback-edit",
          component: () => import("../feedback/Create.vue"),
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
          name: "interaction-create",
          component: () => import("../interaction/Create.vue"),
        },
        {
          path: "created",
          name: "interaction-created",
          component: () => import("../interaction/Created.vue"),
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
          path: "edit/:id?", //? allows route to match even if no id provided. In this case the dialog will prompt for ID as well as PIN
          name: "interaction-edit",
          component: () => import("../interaction/Create.vue"),
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
