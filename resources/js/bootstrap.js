import _ from 'lodash';
window._ = _;

import axios from 'axios';
import Swal from 'sweetalert2';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let requestCount = 0;

function showLoader() {
  const loader = document.getElementById('global-loader');
  if (loader) loader.style.display = 'flex';
  document.body.style.overflow = 'hidden';

}

function hideLoader() {
  const loader = document.getElementById('global-loader');
  if (loader) loader.style.display = 'none';
  document.body.style.overflow = '';

}

function startRequest() {
  requestCount++;
  showLoader();
}

function endRequest() {
  requestCount--;
  if (requestCount <= 0) {
    requestCount = 0;
    hideLoader();
  }
}

// Interceptores de solicitud
window.axios.interceptors.request.use(config => {
  startRequest();
  return config;
}, error => {
  endRequest();
  return Promise.reject(error);
});


// Interceptor para manejar errores
window.axios.interceptors.response.use(
  response => {
    endRequest();
    return response;
  },
  error => {
    endRequest();
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
