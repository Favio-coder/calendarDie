<template>
  <div>
    <!-- Modal de Recursos -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content modal-sombra">
          <div class="modal-header titulo-agend-actividad text-white">
            <h5 class="modal-title">Permisos para mentores oficiales</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <!-- Loading animación -->
            <div v-if="isLoading" class="text-center mb-3">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando usuarios...</p>
            </div>

            <!-- Tabla con scroll -->
            <div class="tabla-scroll" v-else>
              <table class="table table-bordered text-center align-middle">
                <thead class="table-light sticky-top bg-light">
                  <tr>
                    <th>Nombre del usuario</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="mentoresOficiales.length === 0">
                    <td colspan="4">No hay usuarios</td>
                  </tr>
                  <tr v-for="(m, index) in mentoresOficiales" :key="m.c_usuario">
                    <td @dblclick="asignarPermiso(m)">{{ m.l_nombre + ' '+ m.l_apellido }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
            <!-- <button class="btn btn-primary" @click="abrirModalAgregar">
              <i class="fa-solid fa-plus me-1"></i> Agregar recurso
            </button> -->
          </div>
        </div>
      </div>
    </div>

    <!-- Modal hijo -->
    <component :is="currentView" v-bind="currentProps" ref="modalRef" @cerrar-modalPermisos="cerrarModalPermisos"  />
  </div>
</template>

<script>
import ModalPermisoUsua from './ModalPermisoUsua.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';

export default {
  name: 'ModalPermiso',
  components: { ModalPermisoUsua },
  data() {
    return {
      isModalOpen: false,
      mentoresOficiales: [],
      filtro: '',
      currentView: null,
      currentProps: null,
      isLoading: false
    };
  },
  computed: {
    ...mapGetters(['usuario'])
  },
  mounted() {
    this.cargarMentores();
  },
  methods: {
    cerrarModalPermisos() {
      this.currentProps = null;
      this.currentView = null;
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
      this.cargarMentores();
    },
    closeModal() {
      this.$emit('cerrar-modalPermisos');
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    // abrirModalEditar(recurso) {
    //   this.currentView = ModalAgregarRecurso;
    //   this.currentProps = {
    //     accion: 'editar',
    //     recursoProp: recurso
    //   };
    //   nextTick(() => {
    //     if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
    //       this.$refs.modalRef.openModal();
    //     }
    //   });
    // },
    // abrirModalAgregar() {
    //   this.currentView = ModalAgregarRecurso;
    //   this.currentProps = {
    //     accion: 'insertar'
    //   };
    //   nextTick(() => {
    //     if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
    //       this.$refs.modalRef.openModal();
    //     }
    //   });
    // },
    cargarMentores() {
      this.isLoading = true;
      axios.post('/listMentoresOficiales', this.usuario )
        .then(response => {
          this.mentoresOficiales = response.data.mentoresOficiales;
        })
        .catch(error => {
          console.error("Error al cargar mentores:", error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    asignarPermiso(usuario) {
      this.currentView = ModalPermisoUsua
      this.currentProps = {
        mentorOficialProp: usuario
      }
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
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
