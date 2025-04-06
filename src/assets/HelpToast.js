import Swal from "sweetalert2";

let helpToastInstance

const helpToastMixin = Swal.mixin({
  toast: true,
  showConfirmButton: false,
  timer: null,
  timerProgressBar: false,
  showCloseButton: true,
  position: "bottom",
  animation: false
});

const inputHelp = {
  title: {
    heading: 'session title',
    html: `
      <ul>
        <li>This is the title of the teaching event</li>
        <li>Point 2</li>
      </ul>
    `
  },
  date: {
    heading: 'session date',
    html: `
      <ul>
        <li>Point A</li>
        <li>Point B</li>
      </ul>
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