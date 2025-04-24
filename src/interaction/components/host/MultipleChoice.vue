<script setup>
/**
 * @module interaction/components/host/MultipleChoice
 * @summary Displays a chart for interaction slide responses.
 * @description
 * This module uses Chart.js to visualize responses to multiple choice or poll slides.
 * It supports dynamic updates of chart data and valid answer highlighting.
 *
 * @requires vue
 * @requires chart.js/auto
 */

import { onMounted, watch } from "vue";
import Chart from "chart.js/auto";

// Props from parent component
const props = defineProps(["slide", "showValidIndicators"]);

// Chart instance
let chart;

// Holds count of responses per option
const optionCounts = Array(props.slide.interaction.options.length).fill(0);

// Arrays to store border styles for valid/invalid indicators
let borderColor = [];
let borderWidth = [];

/**
 * Updates border styles on the chart to indicate correct/incorrect answers.
 * Called on mount and whenever `showValidIndicators` changes.
 *
 * @memberof module:interaction/components/host/Content
 */
const updateValidIndicators = () => {
  const options = props.slide.interaction.options;

  if (props.showValidIndicators) {
    // Show green border for correct and red for incorrect options
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

  // Update chart with new border styles
  if (chart) {
    chart.data.datasets[0].borderColor = borderColor;
    chart.data.datasets[0].borderWidth = borderWidth;
    chart.update();
  }
};

// Initialize valid indicators on first load
updateValidIndicators();

// Watch for indicator toggle and reapply styles
watch(() => props.showValidIndicators, updateValidIndicators);

// Shared background colors for both chart types
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

// Chart configuration, supports both bar and doughnut types
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
      plugins: { legend: { display: false } },
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
          ticks: {
            font: { size: 18 },
          },
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
          labels: {
            font: { size: 18 },
          },
        },
      },
    },
  },
};

/**
 * Updates the option counts based on slide submissions.
 * Triggered when the slide prop changes (e.g., when navigating slides).
 *
 * @memberof module:interaction/components/host/Content
 */
watch(props.slide, () => {
  const { options, submissions } = props.slide.interaction;

  // Reset counts
  optionCounts.fill(0);

  // Tally new counts from submission data
  for (const submission of submissions) {
    const selectedIndices = JSON.parse(submission.response);
    for (const index of selectedIndices) {
      optionCounts[parseInt(index)]++;
    }
  }

  // Update chart with new data
  chart?.update();
});

/**
 * Initializes the Chart.js chart once the component is mounted.
 *
 * @memberof module:interaction/components/host/Content
 */
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
