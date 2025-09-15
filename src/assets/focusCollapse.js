import Collapse from "bootstrap/js/dist/collapse";

export default {
  mounted(el, binding) {
    const targetId = binding.value;
    const targetEl = document.getElementById(targetId);

    if (!targetEl) {
      console.warn(`v-focus-collapse: No element found with ID '${targetId}'`);
      return;
    }

    const collapseInstance = new Collapse(targetEl, { toggle: false });

    el.addEventListener("focus", () => collapseInstance.show());
    el.addEventListener("blur", () => collapseInstance.hide());

    // Save to element so we can clean up later
    el.__focusCollapse = {
      collapseInstance,
      targetEl,
    };
  },

  unmounted(el) {
    if (el.__focusCollapse) {
      // Optional: cleanup if needed (e.g. remove listeners or hide)
      el.removeEventListener("focus", el.__focusCollapse.collapseInstance.show);
      el.removeEventListener("blur", el.__focusCollapse.collapseInstance.hide);
      el.__focusCollapse = null;
    }
  },
};
