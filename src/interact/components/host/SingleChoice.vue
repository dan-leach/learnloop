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
  for (let submission of props.interaction.submissions) optionCounts[parseInt(submission.response)]++;
  chart.update();
});

onMounted(() => {
  props.interaction.chartType = "bar" //remove once set at session creation time
  chart = new Chart(
    document.getElementById('chart'),
    chartConfig[props.interaction.chartType]
  );
});
</script>

<template>
  <canvas id="chart" class="p-2"></canvas>
</template>

<style scoped>
#chart {
  background-color: white;
  border: 1px solid #0000002d;
  border-radius: 5px;
  max-height: 80vh;
}
</style>
