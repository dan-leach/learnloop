<script setup>
/**
 * @module interaction/components/host/TrueFalse
 * @summary Displays a Chart.js graph of user responses for an interactive slide.
 * @description
 * This script creates a Chart.js chart (bar or doughnut) based on interaction slide data,
 * updating dynamically when responses or validation indicators change.
 *
 * @requires vue
 * @requires chart.js/auto
 */

import { onMounted, watch } from "vue";
import Chart from "chart.js/auto";

// Props from the parent
const props = defineProps(["slide", "showValidIndicators"]);

// Chart instance
let chart;

// Stores response counts per option
const optionCounts = Array(props.slide.interaction.options.length).fill(0);

// Arrays to store border styling for valid/invalid indicators
let borderColor = [];
let borderWidth = [];

/**
 * Updates the chart to visually indicate which options are correct or incorrect.
 * Applies custom border color and width depending on the correctness status.
 *
 * @memberof module:InteractionChart
 */
const updateValidIndicators = () => {
  const options = props.slide.interaction.options;

  if (props.showValidIndicators) {
    borderColor = options.map((o) =>
      o.correct
        ? "rgba(0, 255, 0, 1)"
        : o.incorrect
        ? "rgba(255, 0, 0, 1)"
        : "rgba(0, 0, 0, 0)"
    );
    borderWidth = options.map((o) => (o.correct || o.incorrect ? 5 : 0));
  } else {
    // No highlighting
    borderColor = options.map(() => "rgba(0, 0, 0, 0)");
    borderWidth = options.map(() => 0);
  }

  // Apply updated styles to the chart
  if (chart) {
    const dataset = chart.data.datasets[0];
    dataset.borderColor = borderColor;
    dataset.borderWidth = borderWidth;
    chart.update();
  }
};

// Initial indicator update
updateValidIndicators();

// Re-run indicator update whenever showValidIndicators changes
watch(() => props.showValidIndicators, updateValidIndicators);

// Common background colors shared by both chart types
const backgroundColors = [
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
];

// Chart configuration object
const chartConfig = {
  bar: {
    type: "bar",
    data: {
      labels: props.slide.interaction.options.map((o) => o.text),
      datasets: [
        {
          data: optionCounts,
          backgroundColor: backgroundColors,
          borderColor,
          borderWidth,
        },
      ],
    },
    options: {
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => (Number.isInteger(value) ? value : ""),
          },
          grid: {
            color: (ctx) =>
              Number.isInteger(ctx.tick.value) ? "#adadad" : "#00000000",
          },
        },
        x: {
          ticks: { font: { size: 18 } },
        },
      },
    },
  },
  doughnut: {
    type: "doughnut",
    data: {
      labels: props.slide.interaction.options.map((o) => o.text),
      datasets: [
        {
          data: optionCounts,
          hoverOffset: 4,
          backgroundColor: backgroundColors,
          borderColor,
          borderWidth,
        },
      ],
    },
    options: {
      plugins: {
        legend: {
          labels: { font: { size: 18 } },
        },
      },
    },
  },
};

/**
 * Recalculates the optionCounts based on all current submissions.
 * Updates the chart data accordingly.
 *
 * @memberof module:InteractionChart
 */
watch(props.slide, () => {
  // Reset all counts
  optionCounts.fill(0);

  // Tally responses
  for (const submission of props.slide.interaction.submissions) {
    const index = parseInt(submission.response);
    if (!isNaN(index) && index >= 0 && index < optionCounts.length) {
      optionCounts[index]++;
    }
  }

  // Refresh chart with updated data
  chart?.update();
});

/**
 * Initializes the Chart.js chart on component mount.
 *
 * @memberof module:InteractionChart
 */
onMounted(() => {
  const chartType = props.slide.interaction.settings.chart;
  chart = new Chart(document.getElementById("chart"), chartConfig[chartType]);
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
