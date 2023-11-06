import { reactive } from 'vue';

export const feedbackSession = reactive({
  id: '',
  pin: '',
  title: '',
  name: '',
  date: '',
  subsessions: [],
  questions: [],
  certificate: true,
  notifications: true,
  attendance: true,
  feedback: {
    positive: '',
    constructive: '',
    score: null,
    scoreText: 'Please use the slider to indicate an overall score',
  },
  subsession: {
    id: '',
    title: '',
    name: '',
    feedback: {
      positive: '',
      constructive: '',
      score: null,
      scoreText: 'Please use the slider to indicate an overall score',
    },
  },
});
