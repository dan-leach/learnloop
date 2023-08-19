<script setup>
import { onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps(['interaction']);

const optionCounts = [];
for (let i = 0; i < props.interaction.options.length; i++) optionCounts.push(0);

const config = {
  type: 'bar',
  data: {
    labels: props.interaction.options,
    datasets: [
      {
        label: 'Responses',
        data: optionCounts,
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(255, 205, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(54, 162, 235, 0.2)',
        ],
        borderColor: [
          'rgb(255, 99, 132)',
          'rgb(255, 159, 64)',
          'rgb(255, 205, 86)',
          'rgb(75, 192, 192)',
          'rgb(54, 162, 235)',
        ],
        borderWidth: 1,
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
    },
  },
};

let chart;
watch(props.interaction, () => {
  for (let i = 0; i < props.interaction.options.length; i++)
    optionCounts[i] = 0;
  for (let submission of props.interaction.submissions)
    optionCounts[submission.data]++;
  chart.update();
});

onMounted(() => {
  chart = new Chart(document.getElementById('barChart'), config);
});
</script>

<template>
  <canvas id="barChart"></canvas>
</template>

<style scoped></style>
