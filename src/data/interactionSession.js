import { reactive } from "vue";

export const interactionSession = reactive({
  id: "",
  pin: "",
  title: "",
  name: "",
  submissionCount: 0,
  slides: [],
  hostStatus: {
    facilitatorIndex: 0,
    lockedSlides: [],
  },
  editMode: false,
  reset: function () {
    this.id = "";
    this.pin = "";
    this.title = "";
    this.name = "";
    this.submissionCount = 0;
    this.slides = [];
    this.hostStatus = {
      facilitatorIndex: 0,
      lockedSlides: [],
    };
    this.editMode = false;
  },
});
