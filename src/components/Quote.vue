<script setup>
import { onMounted, ref } from "vue";

const quotes = [
  {
    text: "We all need people who will give us feedback. That’s how we improve.",
    cite: "Bill Gates",
  },
  {
    text: "Criticism, like rain, should be gentle enough to nourish a man’s growth without destroying his roots.",
    cite: "Frank A. Clark",
  },
  {
    text: "Feedback is the breakfast of champions.",
    cite: "Ken Blanchard",
  },
  {
    text: "Feedback is a gift. Ideas are the currency of our next success. Let people see you value both feedback and ideas.",
    cite: "Jim Trinka and Les Wallace",
  },
  {
    text: "There is no failure. Only feedback.",
    cite: "Robert Allen",
  },
  {
    text: "No matter how good you think you are as a leader, my goodness, the people around you will have all kinds of ideas for how you can get better. So for me, the most fundamental thing about leadership is to have the humility to continue to get feedback and to try to get better – because your job is to try to help everybody else get better.",
    cite: "Jim Yong Kim",
  },
  {
    text: "True intuitive expertise is learned from prolonged experience with good feedback on mistakes.",
    cite: "Daniel Kahneman",
  },
  {
    text: "Criticism may not be agreeable, but it is necessary. It fulfils the same function as pain in the human body. It calls attention to an unhealthy state of things.",
    cite: "Winston Churchill",
  },
  {
    text: "All that is valuable in human society depends upon the opportunity for development accorded the individual.",
    cite: "Albert Einstein",
  },
  {
    text: "Strive for continuous improvement, instead of perfection.",
    cite: "Kim Collins",
  },
  {
    text: "Never be afraid to fail. Failure is only a stepping stone to improvement.",
    cite: "Tony Jaa",
  },
  {
    text: "There is always space for improvement, no matter how long you’ve been in the business.",
    cite: "Oscar De La Hoya",
  },
  {
    text: "Knowledge rests not upon truth alone, but upon error also.",
    cite: "Carl Jung",
  },
  {
    text: "To become more effective and fulfilled at work, people need a keen understanding of their impact on others and the extent to which they’re achieving their goals in their working relationships. Direct feedback is the most efficient way for them to gather this information and learn from it.",
    cite: "Ed Batista",
  },
  {
    text: "We can’t just sit back and wait for feedback to be offered, particularly when we’re in a leadership role. If we want feedback to take root in the culture, we need to explicitly ask for it.",
    cite: "Ed Batista",
  },
  {
    text: "Average players want to be left alone. Good players want to be coached. Great players want to be told the truth.",
    cite: "Doc Rivers",
  },
  {
    text: "There is a huge value in learning with instant feedback.",
    cite: "Anant Agarwal",
  },
  {
    text: "If you continue training the same way you’ve always trained, don’t expect to get better results.",
    cite: "Jim Crapko",
  },
  {
    text: "Teach the way you’d want to be taught.",
    cite: "Unknown",
  },
  {
    text: "For good ideas and true innovation, you need human interaction, conflict, argument, debate.",
    cite: "Margaret Heffernan",
  },
];

let quote = ref({
  text: "the quote text",
  cite: "by this person",
});

function refresh() {
  const random = Math.floor(Math.random() * quotes.length);
  quote.value = quotes[random];
}
onMounted(() => {
  refresh();
});
</script>

<template>
  <div class="quote text-center m-4">
    <span>"{{ quote.text }}"</span><br />
    <i>- {{ quote.cite }}</i
    ><br />
    <a @click="refresh()">
      <font-awesome-icon :icon="['fas', 'rotate-right']" class="refresh" />
    </a>
  </div>
</template>

<style scoped>
.refresh {
  font-size: 1.5rem;
  color: black;
  transition: font-size 0.2s ease-out, rotate 0.2s ease-out;
}
.refresh:hover {
  font-size: 2.5rem;
  rotate: 90deg;
}
</style>
