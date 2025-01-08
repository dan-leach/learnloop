import Swal from "sweetalert2";

const Toast = Swal.mixin({
  toast: true,
  position: "top",
  iconColor: "#17a2b8",
  showConfirmButton: false,
  timer: 2000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener("mouseenter", Swal.stopTimer);
    toast.addEventListener("mouseleave", Swal.resumeTimer);
  },
});

export default Toast;
