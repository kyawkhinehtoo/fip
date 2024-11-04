require("./bootstrap");

// Import modules...
import { createApp, h } from "vue";
import { App as InertiaApp, plugin as InertiaPlugin } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";

const el = document.getElementById("app");
const Swal = require("sweetalert2").default;
window.Toast = Swal.mixin({
  toast: true,
  position: "top-right",
  timer: 3500,
  timerProgressBar: false,
});
createApp({
  render: () =>
    h(InertiaApp, {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: (name) => require(`./Pages/${name}`).default,
    }),
})
  .mixin({ methods: { route } })
  .use(InertiaPlugin)
  .mount(el);

InertiaProgress.init({ color: "#4B5563" });
