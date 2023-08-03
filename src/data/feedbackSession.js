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
    scoreText: '',
  },
});
