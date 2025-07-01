<template>
  <div>
    <!-- Modal de Recursos -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content modal-sombra">
          <div class="modal-header titulo-agend-actividad text-white">
            <h5 class="modal-title">Recursos Disponibles</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <!-- Buscador -->
            <div class="mb-3">
              <input v-model="filtro" type="text" class="form-control" placeholder="🔍 Buscar recurso..." />
            </div>

            <!-- Loading animación -->
            <div v-if="isLoading" class="text-center mb-3">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando recursos...</p>
            </div>

            <!-- Tabla con scroll -->
            <div class="tabla-scroll" v-else>
              <table class="table table-bordered text-center align-middle">
                <thead class="table-light sticky-top bg-light">
                  <tr>
                    <th>#</th>
                    <th>Nombre del Recurso</th>
                    <th>Descripción</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="recursosFiltrados.length === 0">
                    <td colspan="4">No hay recursos registrados.</td>
                  </tr>
                  <tr @dblclick="abrirModalEditar(recurso)" v-for="(recurso, index) in recursosFiltrados" :key="recurso.c_recurso">
                    <td>{{ index + 1 }}</td>
                    <td>{{ recurso.l_recurso }}</td>
                    <td>{{ recurso.l_descripcion }}</td>
                    <td>
                      <button @click="elimRecurso(recurso)" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
            <button class="btn btn-primary" @click="abrirModalAgregar">
              <i class="fa-solid fa-plus me-1"></i> Agregar recurso
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal hijo -->
    <component :is="currentView" v-bind="currentProps" ref="modalRef" @recurso-agregado="recursoAgregado" @cerrar-modalAgregarRecurso="cerrarModalAgregarRecurso" />
  </div>
</template>

<script>
import ModalAgregarRecurso from './ModalAgregarRecurso.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'ModalRecursos',
  components: { ModalAgregarRecurso },
  data() {
    return {
      isModalOpen: false,
      recursos: [],
      filtro: '',
      currentView: null,
      currentProps: null,
      isLoading: false
    };
  },
  computed: {
    recursosFiltrados() {
      if (!this.filtro) return this.recursos;
      const f = this.filtro.toLowerCase();
      return this.recursos.filter(r =>
        r.l_recurso.toLowerCase().includes(f) ||
        r.l_descripcion.toLowerCase().includes(f)
      );
    }
  },
  mounted() {
    this.cargarRecursos();
  },
  methods: {
    cerrarModalAgregarRecurso() {
      this.currentProps = null;
      this.currentView = null;
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
      this.cargarRecursos();
    },
    closeModal() {
      this.$emit('cerrar-modalRecursos');
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    abrirModalEditar(recurso) {
      this.currentView = ModalAgregarRecurso;
      this.currentProps = {
        accion: 'editar',
        recursoProp: recurso
      };
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    abrirModalAgregar() {
      this.currentView = ModalAgregarRecurso;
      this.currentProps = {
        accion: 'insertar'
      };
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cargarRecursos() {
      this.isLoading = true;
      axios.get('/listRecursos')
        .then(response => {
          this.recursos = response.data.recursos;
        })
        .catch(error => {
          console.error("Error al cargar recursos:", error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    elimRecurso(recurso) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/elimRecurso', recurso)
            .then(() => {
              Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'El recurso fue eliminado correctamente.',
                confirmButtonColor: '#12BACA'
              });
              this.cargarRecursos();
            })
            .catch(() => {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo eliminar el recurso.',
                confirmButtonColor: '#d33'
              });
            });
        }
      });
    },
    recursoAgregado() {
      this.cargarRecursos();
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

.tabla-scroll {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #dee2e6;
  border-radius: 8px;
}

.sticky-top {
  position: sticky;
  top: 0;
  z-index: 2;
}

/* Loader bolitas personalizadas */
.loader-dots {
  display: flex;
  justify-content: center;
  gap: 10px;
  align-items: center;
}
.dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  animation: bounce 0.6s infinite alternate;
}
.dot-blue {
  background-color: #12BACA;
}
.dot-yellow {
  background-color: #FFD700;
  animation-delay: 0.2s;
}
@keyframes bounce {
  from {
    transform: translateY(0);
  }
  to {
    transform: translateY(-10px);
  }
}
</style>
