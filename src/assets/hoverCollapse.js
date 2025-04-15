import Collapse from "bootstrap/js/dist/collapse";

// Hardcoded list of collapse div IDs to control
const collapseDivIds = ["singleHelp", "seriesHelp", "templateHelp"];

export default {
  mounted(el, binding) {
    const targetId = binding.value;
    const targetEl = document.getElementById(targetId);

    if (!targetEl) {
      console.warn(`v-hover-collapse: No element found with ID '${targetId}'`);
      return;
    }

    const collapseInstance = new Collapse(targetEl, { toggle: false });

    const show = () => collapseInstance.show();
    const hide = () => collapseInstance.hide();

    el.addEventListener("mouseenter", show);
    el.addEventListener("mouseleave", hide);

    // Optional: keep open if mouse is over the info box
    targetEl.addEventListener("mouseenter", show);
    targetEl.addEventListener("mouseleave", hide);

    el.__hoverCollapse = { show, hide, collapseInstance, targetEl };
  },

  unmounted(el) {
    const { show, hide, targetEl } = el.__hoverCollapse || {};

    if (show && hide) {
      el.removeEventListener("mouseenter", show);
      el.removeEventListener("mouseleave", hide);
      targetEl?.removeEventListener("mouseenter", show);
      targetEl?.removeEventListener("mouseleave", hide);
    }

    el.__hoverCollapse = null;
  },
};
