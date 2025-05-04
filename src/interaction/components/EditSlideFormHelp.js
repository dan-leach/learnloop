import Swal from "sweetalert2";
import { config } from "../../data/fetchConfig.js";

const interactionTypeInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Question types",
    html:
      `
        <div class="text-start">
          <p>There are several different slide types available. Interactive slide types are denoted by &#8644;.<br> Click below to see examples and further details about each.</p>
          <div class="accordion" id="accordionTypes">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Single choice &#8644;
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-single-choice.png" class="img-fluid mx-auto d-block">
                  <p>Single choice interactions allow you to provide a selection of options from which attendees can choose only one. By default attendees will be able to respond only once but you can allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Multiple choice &#8644;
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-multiple-choice.png" class="img-fluid mx-auto d-block">
                  <p>Multiple choice interactions allow you to provide a selection of options from which attendees can choose. By default attendees must chose at least 1 option and can select as many as they wish, but optionally you can set a minimum and maximum number of options per response. By default attendees will be able to respond only once but you can optionally allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Free text &#8644;
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-free-text.png" class="img-fluid mx-auto d-block">
                  <p>Free text interactions allow attendees to provide typed responses which appear on the host screen. By default attendees will be able to respond 10 times but you can set a different number of allowed responses per person. To keep responses short the default character limit is 200, but you can set this to a different value if required. You can set the responses not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Static
              </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFour">
              <div class="accordion-body">
                <p>Static is a non-interactive slide type which allows you to show images and text. You can also show images and text on interactive slide types.</p>
              </div>
            </div>
          </div>
          </div>
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const contentLayoutInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Question types",
    html:
      `
        <div class="text-start">
          <p>There are several different slide types available. Interactive slide types are denoted by &#8644;.<br> Click below to see examples and further details about each.</p>
          <div class="accordion" id="accordionTypes">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Single choice &#8644;
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-single-choice.png" class="img-fluid mx-auto d-block">
                  <p>Single choice interactions allow you to provide a selection of options from which attendees can choose only one. By default attendees will be able to respond only once but you can allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Multiple choice &#8644;
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-multiple-choice.png" class="img-fluid mx-auto d-block">
                  <p>Multiple choice interactions allow you to provide a selection of options from which attendees can choose. By default attendees must chose at least 1 option and can select as many as they wish, but optionally you can set a minimum and maximum number of options per response. By default attendees will be able to respond only once but you can optionally allow a higher number of responses per person. You can set the results not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Free text &#8644;
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-type-example-free-text.png" class="img-fluid mx-auto d-block">
                  <p>Free text interactions allow attendees to provide typed responses which appear on the host screen. By default attendees will be able to respond 10 times but you can set a different number of allowed responses per person. To keep responses short the default character limit is 200, but you can set this to a different value if required. You can set the responses not to appear on the host view until after you reveal them by activating this option in settings when creating the slide.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Static
              </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFour">
              <div class="accordion-body">
                <p>Static is a non-interactive slide type which allows you to show images and text. You can also show images and text on interactive slide types.</p>
              </div>
            </div>
          </div>
          </div>
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const chartTypeInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Chart types",
    html:
      `
        <div class="text-start">
          <p>There are two different chart types available for showing single choice or multiple choice interaction results. Click below to see examples and further details:</p>
          <div class="accordion" id="accordionTypes">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Bar chart
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-chart-example-bar.png" class="img-fluid mx-auto d-block">
                  <p>You can hover over any of the bars to show the exact number of responses that option has received.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Doughnut chart
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionTypes">
                <div class="accordion-body">
                  <img src="` +
      config.value.client.url +
      `/img/interaction-chart-example-doughnut.png" class="img-fluid mx-auto d-block">
                  <p>You can hover over any of the segments to show the exact number of responses that option has received.</p>
                </div>
              </div>
            </div>
          </div>
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const optionsMinMaxInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Minimum and maximum number of options",
    html:
      `
        <div class="text-start">
          <p>By default attendees must select between one and all of the available options. You can change the minimum and maximum if required. If the attendee attempts to submit a response with fewer than the minimum number of options selected they will receive an alert like this one:</p>
          <img src="` +
      config.value.client.url +
      `/img/interaction-selection-min.png" class="img-fluid mx-auto d-block">
          <p>Or, if they select more options than the maximum, they will receive an alert like this one:</p>
          <img src="` +
      config.value.client.url +
      `/img/interaction-selection-max.png" class="img-fluid mx-auto d-block">
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const preservePhrasesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Preserve phrases",
    html: `
        <div class="text-start">
          <p>By default each word of a response will be added to the word cloud separately. If you want to preserve the full user response in the word cloud you can select this option.</p>
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const submissionLimitInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Number of responses",
    html:
      `
        <div class="text-start">
          <p>By default attendees can respond only once to interactions (10 times for free text interactions). You can change this number if required. Once they have responded the maximum number of times the interaction will be disabled on their device:</p>
          <img src="` +
      config.value.client.url +
      `/img/interaction-submission-limit.png" class="img-fluid mx-auto d-block">
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const hideResponsesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Hide responses until you reveal them",
    html:
      `
        <div class="text-start">
          <p>If you want to prevent attendees from seeing what others are responding until you reveal the answer you can select this option. Your screen will display this view until your click to reveal the responses:</p>
          <img src="` +
      config.value.client.url +
      `/img/interaction-hide-responses.png" class="img-fluid mx-auto d-block">
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

const showContentForAttendeesInfo = () => {
  Swal.fire({
    icon: "info",
    iconColor: "#17a2b8",
    title: "Show content on attendee devices",
    html: `
        <div class="text-start">
          <p>By default content is shown both on the host screen and attendee devices. For images this allows attendees to zoom in or save the image.<br><br> You can choose to show the content only on the host screen if you want to prevent attendees from seeing it on their devices.</p>
        </div>`,
    width: "60%",
    confirmButtonColor: "#17a2b8",
  });
};

export {
  interactionTypeInfo,
  contentLayoutInfo,
  chartTypeInfo,
  optionsMinMaxInfo,
  preservePhrasesInfo,
  submissionLimitInfo,
  hideResponsesInfo,
  showContentForAttendeesInfo,
};
