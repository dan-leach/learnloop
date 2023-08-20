<script setup>
import { onMounted, watch } from 'vue';
import { config } from '../../../data/config.js';
import Chart from 'chart.js/auto';

const props = defineProps(['interaction']);

const optionCounts = [];
for (let i = 0; i < props.interaction.options.length; i++) optionCounts.push(0);

const chartConfig = {
  bar: {
    type: 'bar',
    data: {
      labels: props.interaction.options,
      datasets: [
        {
          data: optionCounts,
        },
      ],
    },
    options: {
      plugins: {
        legend: {
          display: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            // Include a dollar sign in the ticks
            callback: function (value, index, ticks) {
              return Number.isInteger(value) ? value : '';
            },
          },
          grid: {
            color: function (context) {
              if (Number.isInteger(context.tick.value)) {
                return '#adadad';
              } else {
                return '#00000000';
              }
            },
          },
        },
        x: {
          ticks: {
            font: {
              size: 18,
            },
          },
        },
      },
    },
  },
  doughnut: {
    type: 'doughnut',
    data: {
      labels: props.interaction.options,
      datasets: [
        {
          data: optionCounts,
          hoverOffset: 4,
        },
      ],
    },
    options: {
      plugins: {
        legend: {
          labels: {
            font: {
              size: 18,
            },
          },
        },
      },
    },
  },
};

let chart;
watch(props.interaction, () => {
  for (let i = 0; i < props.interaction.options.length; i++)
    optionCounts[i] = 0;
  for (let submission of props.interaction.submissions)
    optionCounts[submission.response]++;
  chart.update();
});

onMounted(() => {
  chart = new Chart(
    document.getElementById('barChart'),
    chartConfig[props.interaction.chart]
  );
});
</script>

<template>
  <canvas id="barChart"></canvas>
</template>

<style scoped></style>
