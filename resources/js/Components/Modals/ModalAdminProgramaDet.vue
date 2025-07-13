<template>
  <div>
    <!-- Modal de Recursos -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content modal-sombra">
          <!-- Header -->
          <div class="modal-header titulo-agend-actividad text-white">
            <!-- <h5 v-if="accion === 'insertar'" class="modal-title">Agregar Programa</h5>
            <h5 v-else class="modal-title">Editar Recurso</h5> -->
            <h5 v-if="!q_editar" class="modal-title">Agregar Programa</h5>
            <h5 v-else class="modal-title">Editar Programa</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <!-- Formulario -->
            <div class="mb-3">
              <label for="nombreRecurso" class="form-label">Nombre del programa</label>
              <input v-model="nuevoPrograma.nombrePrograma" type="text" class="form-control" id="nombreRecurso"
                placeholder="Ingrese nombre" />
            </div>
            <div class="mb-3">
              <label for="descripcionRecurso" class="form-label">Descripción</label>
              <textarea v-model="nuevoPrograma.descripcionPrograma" class="form-control" id="descripcionRecurso"
                placeholder="Ingrese descripción" rows="3"></textarea>
            </div>
          </div>

          <!-- Footer -->
          <div class="modal-footer justify-content-end">
            <button class="btn btn-danger me-2" @click="closeModal">Cerrar</button>
            <button v-if="!q_editar" class="btn btn-primary" @click="agregarPrograma">
              <i class="fa-solid fa-plus me-1"></i> Agregar Programa
            </button>
            <button v-else class="btn btn-primary" @click="editarPrograma">
              <i class="fa-solid fa-pen me-1"></i> Editar Programa
            </button>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  name: 'ModalAgregarPrograma',
  data() {
    return {
      isModalOpen: false,
      nuevoPrograma: {
        nombrePrograma: '',
        descripcionPrograma: ''
      },
      q_editar: false
    };
  },
  props: {
    accion: String,
    programaProp: Object
  },
  mounted() {
    console.log("Programa heredarooosasaead: ", this.programaProp)


    if (this.accion === 'editarPrograma' && this.programaProp) {
      this.q_editar = true
      this.nuevoPrograma = {
        nombrePrograma: this.programaProp.l_programa,
        descripcionPrograma: this.programaProp.l_descripcion,
        id_programa: this.programaProp.c_programa
      };
    }
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('cerrar-modalAgregarPrograma')
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    agregarPrograma() {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se creara un nuevo programa con estos datos",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, crear",
        cancelButtonText: "Cancelar"
      }).then(result => {
        if (result.isConfirmed) {
          axios.post('/grabPrograma', this.nuevoPrograma)
            .then(response => {
              const nuevoPrograma = {
                l_descripcion: this.nuevoPrograma.descripcionPrograma,
                l_programa: this.nuevoPrograma.nombrePrograma,
                c_programa: response.data.idPrograma
              }


              const nuevos = [...this.$store.state.programas, nuevoPrograma]
              this.$store.dispatch('guardarProgramas', nuevos)
              Swal.fire({
                icon: 'success',
                title: 'Programa registrado!',
                text: 'El programa fue creado exitosamente.',
                confirmButtonText: 'Aceptar'
              });

              this.closeModal()
            })

        }
      });
    },
    editarPrograma() {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se editará este programa con estos datos",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, editar",
        cancelButtonText: "Cancelar"
      }).then(result => {
        if (result.isConfirmed) {
          axios.post('/editPrograma', this.nuevoPrograma)
            .then(response => {
              const programaActualizado = {
                l_descripcion: this.nuevoPrograma.descripcionPrograma,
                l_programa: this.nuevoPrograma.nombrePrograma,
                c_programa: this.nuevoPrograma.id_programa 
              };

              this.$store.dispatch('actualizarPrograma', programaActualizado);

              Swal.fire({
                icon: 'success',
                title: 'Programa editado!',
                text: 'El programa fue actualizado exitosamente.',
                confirmButtonText: 'Aceptar'
              });

              this.closeModal();
            });
        }
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
