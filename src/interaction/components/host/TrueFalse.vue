<script setup>
import { onMounted, watch } from "vue";
import Chart from "chart.js/auto";

const props = defineProps(["slide"]);

const optionCounts = [];
for (let i = 0; i < props.slide.interaction.options.length; i++)
  optionCounts.push(0);

const borderColor = props.slide.interaction.options.map((option) => {
  if (option.correct) return "rgba(0, 255, 0, 1)";
  if (option.incorrect) return "rgba(255, 0, 0, 1)";
  return "rgba(0, 0, 0, 0)";
});

const borderWidth = props.slide.interaction.options.map((option) => {
  if (option.correct || option.incorrect) return 5;
  return 0;
});

const chartConfig = {
  bar: {
    type: "bar",
    data: {
      labels: props.slide.interaction.options.map((option) => option.text),
      datasets: [
        {
          data: optionCounts,
          backgroundColor: [
            "rgba(255, 99, 132, 0.5)",
            "rgba(66, 245, 215, 0.5)",
            "rgba(255, 159, 64, 0.5)",
            "rgba(255, 205, 86, 0.5)",
            "rgba(245, 66, 227, 0.5)",
            "rgba(75, 192, 192, 0.5)",
            "rgba(54, 162, 235, 0.5)",
            "rgba(153, 102, 255, 0.5)",
            "rgba(161, 163, 167, 0.5)",
            "rgba(176, 245, 66, 0.5)",
          ],
          borderColor: borderColor,
          borderWidth: borderWidth,
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
              return Number.isInteger(value) ? value : "";
            },
          },
          grid: {
            color: function (context) {
              if (Number.isInteger(context.tick.value)) {
                return "#adadad";
              } else {
                return "#00000000";
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
    type: "doughnut",
    data: {
      labels: props.slide.interaction.options.map((option) => option.text),
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
watch(props.slide, () => {
  for (let i = 0; i < props.slide.interaction.options.length; i++)
    optionCounts[i] = 0;
  for (let submission of props.slide.interaction.submissions) {
    optionCounts[parseInt(submission.response)]++;
  }
  chart.update();
});

onMounted(() => {
  chart = new Chart(
    document.getElementById("chart"),
    chartConfig[props.slide.interaction.settings.chart]
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
