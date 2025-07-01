<template>
  <div>
    <!-- Modal de Recursos -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content modal-sombra">
          <!-- Header -->
          <div class="modal-header titulo-agend-actividad text-white">
            <h5 v-if="accion === 'insertar'" class="modal-title">Agregar Recurso</h5>
            <h5 v-else class="modal-title">Editar Recurso</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <!-- Formulario -->
            <div class="mb-3">
              <label for="nombreRecurso" class="form-label">Nombre del Recurso</label>
              <input v-model="nuevoRecurso.nombreRecurso" type="text" class="form-control" id="nombreRecurso"
                placeholder="Ingrese nombre" />
            </div>
            <div class="mb-3">
              <label for="descripcionRecurso" class="form-label">Descripción</label>
              <textarea v-model="nuevoRecurso.descripcionRecurso" class="form-control" id="descripcionRecurso"
                placeholder="Ingrese descripción" rows="3"></textarea>
            </div>
          </div>

          <!-- Footer -->
          <div class="modal-footer justify-content-end">
            <button class="btn btn-danger me-2" @click="closeModal">Cerrar</button>
            <button v-if="accion == 'insertar'" class="btn btn-primary" @click="agregarRecurso">
              <i class="fa-solid fa-plus me-1"></i> Agregar recurso
            </button>
            <button v-else class="btn btn-primary" @click="agregarRecurso">
              <i class="fa-solid fa-pen me-1"></i> Editar recurso
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  name: 'ModalAgregarRecursos',
  data() {
    return {
      isModalOpen: false,
      nuevoRecurso: {
        nombreRecurso: '',
        descripcionRecurso: ''
      }
    };
  },
  props: {
    accion: String,
    recursoProp: Object
  },
  mounted() {
    if (this.accion === 'editar' && this.recursoProp) {
      this.nuevoRecurso = {
        nombreRecurso: this.recursoProp.l_recurso,
        descripcionRecurso: this.recursoProp.l_descripcion,
        id_recurso: this.recursoProp.c_recurso
      };
    }
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('cerrar-modalAgregarRecurso')
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    agregarRecurso() {
      const dataEnviar = {
        nombreRecurso: this.nuevoRecurso.nombreRecurso,
        descripcionRecurso: this.nuevoRecurso.descripcionRecurso,
        accion: this.accion
      };

      // Solo agregar el id si es editar
      if (this.accion === 'editar') {
        dataEnviar.idRecurso = this.nuevoRecurso.id_recurso;
      }

      window.axios.post('/grabRecurso', dataEnviar)
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: this.accion === 'insertar'
              ? 'El recurso fue agregado correctamente.'
              : 'El recurso fue editado correctamente.',
            confirmButtonColor: '#12BACA'
          });

          this.closeModal();

          if (this.accion === 'insertar') {
            this.nuevoRecurso = {
              nombreRecurso: '',
              descripcionRecurso: ''
            };
          }

          this.$emit('recurso-agregado');
        })
        .catch(error => {
          console.error('Error al guardar recurso:', error);
          // El manejo del error 422 y 500 ya lo estás haciendo con los interceptores
        });
    }


  }
};
</script>

<style scoped>
.modal-sombra {
  box-shadow: -10px 0 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 0, 0, 0.2);
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
  vertical-align: middle;
}

.btn-close-white {
  filter: invert(1) brightness(2);
}

.titulo-agend-actividad {
  background-color: #12BACA;
}

/* Ajuste responsivo si necesitas extra margen */
@media (max-width: 768px) {
  .modal-dialog {
    margin: 1rem;
  }
}
</style>
