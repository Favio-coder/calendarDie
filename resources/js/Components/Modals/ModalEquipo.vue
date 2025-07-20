<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog" style="max-width: 1300px;">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">Equipos de emprendimiento</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div>
              <button @click="abrirModalCrearEquipo" type="button" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Agregar Equipo
              </button>
            </div>
            <hr>

            <!-- Cargando -->
            <div v-if="isLoading" class="text-center my-4">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando equipos...</p>
            </div>

            <!-- Contenedor con scroll oculto -->
            <div class="scroll-contenedor" v-else>
              <div class="row">
                <div v-for="item in equipos" :key="item.c_equipo" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <div class="position-relative card shadow border-0 h-100" @dblclick="editarEquipo(item)">
                    <!-- Botón de eliminar -->
                    <button @click="eliminarEquipo(item)"
                      class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 eliminar-btn"
                      title="Eliminar equipo">
                      <i class="fa-solid fa-xmark"></i>
                    </button>

                    <!-- Botón de imprimir-->
                    <button @click="imprimirEquipo(item)"
                      class="btn btn-light btn-sm position-absolute top-0 me-5 mt-2 end-0 imprimir-btn"
                      title="Imprimir equipo">
                      <i class="fa-solid fa-print"></i>
                    </button>

                    <!-- Imagen del equipo -->
                    <img :src="item.l_logoEquipo || defaultImage" alt="Logo del equipo" class="card-img-top img-fluid"
                      style="max-height: 150px; object-fit: contain; background-color: #f8f9fa;" />

                    <!-- Cuerpo de la tarjeta -->
                    <div class="card-body text-center">
                      <h6 class="card-title text-dark mb-0 text-truncate">{{ item.l_equipo }}</h6>
                    </div>
                  </div>
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
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalEquipo="cerrarModalEquipo"
      @close-modalImpresion="cerrarModalImpresion" />
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import ModalAgregarEquipo from './ModalAgregarEquipo.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';
import ModalImpresion from '../layouts/ModalImpresion.vue';



export default {
  name: 'ModalEquipo',
  components: {
    ModalAgregarEquipo, ModalImpresion
  },
  data() {
    return {
      isModalOpen: false,
      equipos: [],
      isLoading: false,
      preview: null,
      defaultImage: 'https://www.shutterstock.com/image-vector/image-icon-trendy-flat-style-600nw-643080895.jpg',
      currentView: null,
      currentProps: null
    }
  },
  computed: {
    ...mapGetters(['usuario'])
  },
  mounted() {
    this.generarListEquipo()
  },
  methods: {
    generarListEquipo() {
      this.isLoading = true
      axios.get('/listEquipo')
        .then(response => {
          const equipos = response.data.Equipo
          const detalles = response.data.EquipoDet
          equipos.forEach(equipo => {
            equipo.integrantes = detalles.filter(det => det.c_equipo === equipo.c_equipo)
          })
          this.equipos = equipos
        })
        .catch(err => {
          console.error("Existe este error: ", err)
        })
        .finally(() => {
          this.isLoading = false
        })
    },
    cerrarModalEquipo() {
      this.generarListEquipo()
      this.currentView = null
      this.currentProps = null
    },
    cerrarModalImpresion() {
      this.currentView = null
      this.currentProps = null
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    abrirModalCrearEquipo() {
      this.currentView = ModalAgregarEquipo
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    editarEquipo(equipo){

      this.currentView = ModalAgregarEquipo
      this.currentProps = {
        tipoEquipoProp: 'editarEquipo',
        equipoProp: equipo
      }
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarModalImpresion() {
      this.currentView = ModalAgregarEquipo
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    eliminarEquipo(data) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: `Eliminarás el equipo "${data.l_equipo}" y todos sus integrantes.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/elimEquipo', data)
            .then(() => {
              this.generarListEquipo()
              Swal.fire({
                icon: 'success',
                title: 'Equipo eliminado',
                text: 'El equipo fue eliminado correctamente.'
              })
            })
            .catch(err => {
              console.error("Error al eliminar el equipo:", err)
            })
        }
      })
    },
    imprimirEquipo(equipo) {
      axios.post('/genPdfEquipo', { equipo }, { responseType: 'blob' })
        .then(res => {
          const blob = new Blob([res.data], { type: 'application/pdf' });
          const url = URL.createObjectURL(blob);
          this.currentView = ModalImpresion;
          this.currentProps = { pdfBlobUrl: url };
          this.$nextTick(() => {
            if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
              this.$refs.modalRef.openModal();
            }
          });
        })
        .catch(err => {
          console.error('Error generando PDF:', err);
        });

    },
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

/* Loader bolitas */
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

.imprimir-btn {
  transition: transform 0.2s ease, background-color 0.2s ease;
  border-radius: 50%;
}

.imprimir-btn:hover {
  transform: scale(1.2) rotate(-10deg);
  background-color: #0d6efd !important;
  /* Azul Bootstrap */
  color: white;
}
</style>
