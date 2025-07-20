<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog" style="max-width: 1300px;">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">Programa - {{ programaProp.l_programa }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div v-if="usuario.c_rol === '1' || usuario.c_rol === '2'">
              <button @click="abrirModalCrearSesion" type="button" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Agregar Sesión
              </button>
            </div>
            <hr v-if="usuario.c_rol === '1' || usuario.c_rol === '2'">

            <!-- Cargando -->
            <div v-if="isLoading" class="text-center my-4">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando programas...</p>
            </div>

            <!-- Contenedor con scroll oculto -->
            <div class="scroll-contenedor" v-else>
              <div class="row">
                <div v-if="sesiones.length != 0">
                  <div @click="abrirModalSesion(item)" v-for="(item, idx) in sesiones" :key="item.c_sesion"
                    class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="position-relative card shadow border-0 h-100">
                      <button v-if="usuario.c_rol === '1' || usuario.c_rol === '2'" @click.stop="eliminarSesion(item)"
                        class="btn-close-sesion" title="Eliminar sesión">
                        <i class="fa-solid fa-xmark small-icon"></i>
                      </button>
                      <img :src="item.l_fotoSesion || defaultImage" alt="Imagen de sesión"
                        class="card-img-top img-fluid"
                        style="max-height: 150px; object-fit: cover; background-color: #f8f9fa;" />

                      <div class="card-body text-center">
                        <h6 class="card-title text-dark mb-1">Sesión {{ idx + 1 }}</h6>
                        <p class="mb-0 small text-muted">{{ item.l_sesion }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center my-4">
                  <p class="mt-2 mb-0">No hay sesiones existentes</p>
                </div>

              </div>
            </div>

          </div>

          <div class="modal-footer">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalEquipo="cerrarModalPrograma"
      @close-modalSesion="cerrarModalSesion" />
  </div>
</template>

<script>
import Swal from 'sweetalert2';

import ModalSesion from './ModalSesion.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';

import ModalAgregarSesion from './ModalAgregarSesion.vue';

export default {
  name: 'ModalPrograma',
  props: {
    programaProp: Object
  },
  components: {
    ModalSesion
  },
  data() {
    return {
      isModalOpen: false,
      isLoading: false,
      sesiones: [],
      preview: null,
      defaultImage: 'https://www.shutterstock.com/image-vector/image-icon-trendy-flat-style-600nw-643080895.jpg',
      currentView: null,
      currentProps: null,
      q_agregarElimSesion: false

    }
  },
  computed: {
    ...mapGetters(['usuario'])
  },
  mounted() {
    this.cargarSesiones();

    axios.post('/devPermiso', {
      c_usuario: this.usuario.c_usuario,
      modulo: 'programas',
      tipo: 'agregar',
      descripcion: 'No agregar y eliminar ninguna sesión'
    }).then(
      r => {
        this.q_agregarElimSesion = r.data.permiso
      }
    )

  },
  methods: {
    cargarSesiones() {
      this.isLoading = true;
      axios.post('/listSesionXprograma', this.programaProp).then(response => {
        this.sesiones = response.data.sesiones;
        this.isLoading = false;
      }).catch(() => {
        this.isLoading = false;
      });
    },
    cerrarModalPrograma() {
      this.currentView = null;
      this.currentProps = null;
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('close-modalPrograma');
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    abrirModalCrearSesion() {
      if (this.q_agregarElimSesion) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      this.currentView = ModalAgregarSesion;
      this.currentProps = {
        codPrograma: this.programaProp.c_programa
      };
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    eliminarSesion(data) {
      if (this.q_agregarElimSesion) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      const c_sesion = parseInt(data.c_sesion);
      const c_programa = parseInt(data.c_programa);

      const dataEnviar = {
        c_sesion,
        c_programa
      };

      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Eliminarás esta sesión. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;

          axios.post('/elimSesion', dataEnviar).then(() => {
            this.cargarSesiones(); // 🔁 recargar sesiones después de eliminar
            Swal.fire('¡Eliminado!', 'La sesión ha sido eliminada.', 'success');
          }).catch(() => {
            this.isLoading = false;
            Swal.fire('Error', 'No se pudo eliminar la sesión.', 'error');
          });
        }
      });
    },
    abrirModalSesion(sesion) {
      this.currentView = ModalSesion;
      this.currentProps = {
        sesionProp: sesion
      };
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarModalSesion() {
      this.cargarSesiones();
      this.currentView = null;
      this.currentProps = null;
    }
  }
}
</script>

<style scoped>
.titulo-agend-actividad {
  background-color: #12BACA;
}

.scroll-contenedor {
  max-height: 400px;
  overflow-y: auto;
  padding-right: 5px;
}

.scroll-contenedor::-webkit-scrollbar {
  width: 0px;
  background: transparent;
}

.scroll-contenedor {
  scrollbar-width: none;
}

.eliminar-btn {
  transition: transform 0.2s ease, background-color 0.2s ease;
  border-radius: 50%;
}

.eliminar-btn:hover {
  transform: scale(1.2) rotate(10deg);
  background-color: #dc3545 !important;
  color: white;
}

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

.btn-close {
  filter: invert(1) brightness(2);
}

.btn-close-sesion {
  position: absolute;
  top: 6px;
  right: 8px;
  border: none;
  background-color: transparent;
  color: #999;
  font-size: 0.85rem;
  z-index: 2;
  opacity: 0.6;
  transition: all 0.2s ease;
}

.btn-close-sesion:hover {
  color: #dc3545;
  transform: scale(1.2);
  opacity: 1;
}

.small-icon {
  font-size: 1.1rem;
}

.session-card:hover .btn-close-sesion {
  opacity: 1;
}
</style>
