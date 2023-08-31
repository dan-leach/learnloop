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
    create: {
      interactions: {
        types: {
          singleChoice: {
            name: 'Single choice',
            id: 'singleChoice',
            charts: ['bar', 'doughnut'],
            settings: {
              optionsLimit: 10, //fixed
              submissionLimit: 1, //default - can be changed by facilitator
              hideResponses: false,
            },
          },
          multipleChoice: {
            name: 'Multiple choice',
            id: 'multipleChoice',
            charts: ['bar', 'doughnut'],
            settings: {
              optionsLimit: 10,
              selectedLimit: {
                min: 1, //default - can be changed by facilitator
                max: 1, //will automatically increase to the number of options unless changed
              },
              submissionLimit: 1,
              hideResponses: false,
            },
          },
          text: {
            name: 'Text',
            id: 'text',
            settings: {
              optionsLimit: 0,
              characterLimit: {
                min: 1,
                max: 50,
              },
              submissionLimit: 10,
              hideResponses: false,
            },
          },
        },
        submissionLimitMax: 100,
        minimumOptions: 2,
      },
    },
  },
  client: {
    url: 'https://dev.learnloop.co.uk',
    isFullscreen: false,
    showApiConsole: true,
  },
  api: {
    timeoutDuration: 10000,
  },
});
