import { reactive } from 'vue';

export const interactSession = reactive({
  id: '',
  pin: '',
  title: '',
  name: '',
  submissionCount: 0,
  interactions: [],
});
