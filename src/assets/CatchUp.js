import Swal from 'sweetalert2';

const CatchUp = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
});

export default CatchUp;
