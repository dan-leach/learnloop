import { reactive } from 'vue';

export const interactSession = reactive({
  id: '',
  pin: '',
  title: '',
  name: '',
  submissionCount: 0,
  interactions: [],
  hostStatus: {
    facilitatorIndex: 0,
    lockedInteractions: [],
  },
  reset: function () {
    this.id = '';
    this.pin = '';
    this.title = '';
    this.name = '';
    this.submissionCount = 0;
    this.interactions = [];
  },
});
