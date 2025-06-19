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

            <!-- Contenedor con scroll oculto -->
            <div class="scroll-contenedor">
              <div class="row">
                <div v-for="item in equipos" :key="item.c_equipo" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                  <div class="position-relative card shadow border-0 h-100">
                    <!-- Botón de eliminar -->
                    <button @click="eliminarEquipo(item)"
                      class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 eliminar-btn"
                      title="Eliminar equipo">
                      <i class="fa-solid fa-xmark"></i>
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
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalEquipo="cerrarModalEquipo">
    </component>
  </div>
</template>


<script>
import Swal from 'sweetalert2';
import ModalAgregarEquipo from './ModalAgregarEquipo.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
  name: 'ModalEquipo',
  components: {
    ModalAgregarEquipo
  },
  data() {
    return {
      isModalOpen: false,
      equipos: [],
      form: {
        nombre: '',
        apellido: '',
        correo: '',
        fechaNacimiento: '',
        rol: '',
        codigoEstudiante: '',
        facultad: '',
        carrera: '',
        descripcion: '',
        linkedin: '',
        contrasena: ''
      },
      preview: null,
      defaultImage: 'https://www.shutterstock.com/image-vector/image-icon-trendy-flat-style-600nw-643080895.jpg',
      currentView: null,
      currentProps: null
    }
  },
  computed: {
    ...mapGetters(['usuario']), //Llamar al store Usuario

  },
  mounted() {
    this.generarListEquipo()
  },
  methods: {
    generarListEquipo() {
      axios.get('/listEquipo').then(
        response => {
          const equipos = response.data.Equipo
          const detalles = response.data.EquipoDet
          equipos.forEach(equipo => {
            equipo.integrantes = detalles.filter(det => det.c_equipo === equipo.c_equipo)
          })
          this.equipos = equipos

          console.log("Lista completa de equipos: ", this.equipos)
        }
      ).catch(err => {
        console.error("Existe este error: ", err)
      })
    },
    cerrarModalEquipo() {
      this.generarListEquipo()
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

    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => this.preview = e.target.result;
        reader.readAsDataURL(file);
      }
    },
    grabNuevaCuenta() {
      Swal.fire({
        title: "Mensaje",
        text: "Se creará una cuenta con estas credenciales, ¿estás seguro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Crear cuenta",
        cancelButtonText: "Cancelar"
      }).then(response => {
        if (response.isConfirmed) {
          if (this.form.contrasena === '') {
            Swal.fire({
              title: "Mensaje",
              text: "¡Upss!, debes crear una contraseña para esta cuenta",
              icon: "error",
              confirmButtonColor: "#3085d6",
              confirmButtonText: "Crear contraseña",
            }).then(() => {
              this.abrirModalPassword()
            });
          } else {
            this.form.creador = this.usuario.c_usuario

            //Guardar imagen
            const formData = new FormData()
            for (let key in this.form) {
              formData.append(key, this.form[key])
            }

            if (this.$refs.fotoPerfilInput && this.$refs.fotoPerfilInput.files[0]) {
              formData.append('foto', this.$refs.fotoPerfilInput.files[0]);
            }

            console.log("Esto se va a enviar: ", formData)
            axios.post('/registrarCuenta', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              Swal.fire({
                title: "Mensaje",
                text: "Cuenta creada",
                icon: "success",
                confirmButtonText: "Salir",
              }).then(() => {
                this.closeModal();
              });
            });
          }
        }
      });
    },

    abrirModalCrearEquipo() {
      this.currentView = ModalAgregarEquipo
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    recibirContrasena(datos) {
      this.form.contrasena = datos.contrasena
      console.log("Recibido desde el hijo:", datos)
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
              // Eliminar el elemento del array
              this.generarListEquipo()
              this.equipo = this.equipo.filter(eq => eq.c_equipo !== data.c_equipo)

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
    }
  }
}
</script>

<style scoped>
.titulo-agend-actividad {
  background-color: #12BACA;
}

.profile-pic-section {
  min-width: 200px;
  max-width: 200px;
}

.profile-label {
  cursor: pointer;
}

.profile-img {
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #ccc;
  transition: 0.3s;
}

.profile-img:hover {
  opacity: 0.8;
}

.btn-crear-cuenta {
  background-color: #12BACA;
  color: white;
}

.titulo-agend-actividad .btn-close {
  filter: invert(1) brightness(2);
}

.scroll-contenedor {
  max-height: 400px;
  /* ajusta si quieres más o menos */
  overflow-y: auto;
  padding-right: 5px;
  /* evita que se corte el contenido */
}

/* Ocultar scrollbar para Chrome, Safari y Edge */
.scroll-contenedor::-webkit-scrollbar {
  width: 0px;
  background: transparent;
}

/* Firefox */
.scroll-contenedor {
  scrollbar-width: none;
  /* Firefox */
}

/* Botón de eliminar animado */
.eliminar-btn {
  transition: transform 0.2s ease, background-color 0.2s ease;
  border-radius: 50%;
}

.eliminar-btn:hover {
  transform: scale(1.2) rotate(10deg);
  background-color: #dc3545 !important;
  color: white;
}
</style>
