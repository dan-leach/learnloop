import Swal from "sweetalert2";

let helpToastInstance

const helpToastMixin = Swal.mixin({
  toast: true,
  showConfirmButton: false,
  timer: null,
  timerProgressBar: false,
  showCloseButton: true,
  position: "bottom",
  animation: false,
  width: "90%"
});

const inputHelp = {
  title: {
    heading: 'session title',
    html: `
      <ul>
        <li>Provide a title for your teaching event to make it clear what attendees are providing feedback for</li>
        <li>E.g. 'ST4-8 teaching day: Renal focus' or 'How to manage paediatric DKA'</li>
        <li>The title will appear on the feedback form and the certificate of attendance (if enabled)</li>
      </ul>
    `
  },
  date: {
    heading: 'session date',
    html: `
      <ul>
        <li>Provide a date on which your teaching event is taking place</li>
        <li>The date will appear on the feedback form and the certificate of attendance (if enabled)</li>
        <li>Or, select multiple dates if you want to collate feedback using the same feedback form for a session delivered multiple times</li>
      </ul>
    `
  },
  name: {
    heading: 'session facilitator',
    html: `
        <ul>
            <li>Provide the name of the person or team responsible for the teaching</li>
            <li>E.g. 'Dr Smith', or 'ST4-8 teaching reps'</li>
            <li>The facilitator name will appear on the feedback form and the certificate of attendance (if enabled)</li>
    `
  }
}

const showHelp = (inputName) => {
  const help = inputHelp[inputName]
  if (help) {
    helpToastInstance = helpToastMixin.fire({
        title: `Help for ${help.heading}`,
        html: help.html,
      });
  }
}

const hideHelp = () => {
    if (helpToastInstance.close) helpToastInstance.close()
}

export {showHelp, hideHelp}