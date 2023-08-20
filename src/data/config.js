import { reactive } from 'vue';

export const config = reactive({
  feedback: {},
  interact: {
    join: {
      currentIndexPollInterval: 3000,
    },
    host: {
      newSubmissionsPollInterval: 3000,
    },
  },
  client: {
    isFullscreen: false,
    showApiConsole: true,
  },
  api: {
    timeoutDuration: 10000,
  },
});
