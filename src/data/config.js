import { reactive } from 'vue';

export const config = reactive({
  isFullscreen: false,
  feedback: {},
  interact: {
    join: {
      currentIndexPollInterval: 3000,
    },
    host: {
      newSubmissionsPollInterval: 3000,
    },
  },
});
