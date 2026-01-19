import "./bootstrap";

import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "admin-lte/dist/js/adminlte.min.js";

import { createApp } from "vue/dist/vue.esm-bundler.js";

const app = createApp({});

const router = createRouter({
  routes: Routes,
  history: createWebHistory(),
});

app.use(router);

app.component("Login", Login);

app.mount("#app");
