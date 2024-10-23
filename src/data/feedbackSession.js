import { reactive } from "vue";

export const feedbackSession = reactive({
  id: "",
  pin: "",
  title: "",
  name: "",
  date: "",
  subsessions: [],
  questions: [],
  organisers: [],
  certificate: true,
  notifications: true,
  attendance: true,
  multipleDates: false,
  attendee: {
    name: "",
    region: "",
    organisation: "",
  },
  feedback: {
    positive: "",
    constructive: "",
    score: null,
    scoreText: "Please use the slider to indicate an overall score",
  },
  subsession: {
    id: "",
    title: "",
    name: "",
    feedback: {
      positive: "",
      constructive: "",
      score: null,
      scoreText: "Please use the slider to indicate an overall score",
    },
  },
  reset: function () {
    this.id = "";
    this.pin = "";
    this.title = "";
    this.date = "";
    this.multipleDates = false;
    this.name = "";
    this.certificate = true;
    this.attendance = true;
    this.subsessions = [];
    this.questions = [];
    this.organisers = [];
    this.attendee = {
      name: "",
      region: "",
      organisation: "",
    };
    this.feedback = {
      positive: "",
      constructive: "",
      score: null,
      scoreText: "Please use the slider to indicate an overall score",
    };
    this.subsession = {
      id: "",
      title: "",
      name: "",
      feedback: {
        positive: "",
        constructive: "",
        score: null,
        scoreText: "Please use the slider to indicate an overall score",
      },
    };
    this.templateId = "";
  },
});
