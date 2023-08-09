import { reactive } from 'vue';

export const feedbackSession = reactive({
  id: '',
  title: '',
  name: '',
  date: '',
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
  }
});
