import _ from 'lodash';
window._ = _;

import axios from 'axios';
import Swal from 'sweetalert2';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Interceptor para manejar errores
window.axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response) {
      // Error 422: Validación
      if (error.response.status === 422) {
        const errores = error.response.data.errors;
        const mensaje = Object.values(errores)[0][0];

        Swal.fire({
          icon: 'error',
          title: 'Mensaje',
          text: mensaje,
          confirmButtonText: 'Cerrar',
          confirmButtonColor: '#d33',
        });
      }

      // Error 500: Error interno del servidor
      else if (error.response.status === 500) {
        const mensaje = error.response.data.mensaje || 'La petición falló por un error interno.';

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: mensaje,
          confirmButtonText: 'Cerrar',
          confirmButtonColor: '#d33',
        });
      }
    }

    return Promise.reject(error);
  }
);
