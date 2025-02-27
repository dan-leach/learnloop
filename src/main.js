import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import "@/assets/main.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

/* import the fontawesome core */
import { library } from "@fortawesome/fontawesome-svg-core";
/* import font awesome icon component */
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
/* import specific icons */
import {
  faRotateRight,
  faPenToSquare,
  faQuestionCircle,
  faTrashCan,
  faCircleChevronRight,
  faCircleChevronLeft,
  faChevronDown,
  faChevronUp,
  faMaximize,
  faMinimize,
  faUsersRectangle,
  faCopy,
  faCheck,
  faTimes,
  faAsterisk,
  faLock,
  faLockOpen,
  faArrowRightArrowLeft,
  faUsers,
} from "@fortawesome/free-solid-svg-icons";
/* add icons to the library */
library.add(
  faRotateRight,
  faPenToSquare,
  faQuestionCircle,
  faTrashCan,
  faCircleChevronRight,
  faCircleChevronLeft,
  faChevronDown,
  faChevronUp,
  faMaximize,
  faMinimize,
  faUsersRectangle,
  faCopy,
  faCheck,
  faTimes,
  faAsterisk,
  faLock,
  faLockOpen,
  faArrowRightArrowLeft,
  faUsers
);

import { cookies } from "./data/cookies.js";
if (document.cookie) {
  try {
    const raw = document.cookie;
    const splits = raw.split("; ");
    const sliced = [];
    for (let split of splits) {
      const index = split.indexOf("=");
      sliced.push(split.slice(index + 1));
    }
    for (let slice of sliced) cookies.push(JSON.parse(slice));
  } catch (e) {
    console.log(e);
  }
}

const app = createApp(App).component("font-awesome-icon", FontAwesomeIcon);

// Import config and inject
import { config } from "./data/fetchConfig";
app.provide("config", config);

app.use(router);

app.mount("#app");
