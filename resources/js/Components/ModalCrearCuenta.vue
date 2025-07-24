<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">Crear cuenta</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div class="form-container d-flex flex-column flex-md-row gap-4">
              <!-- Foto de perfil -->
              <div class="profile-pic-section d-flex flex-column align-items-center text-center">
                <label for="fotoPerfil" class="profile-label">
                  <img :src="preview || defaultImage" class="profile-img mb-2" />
                  <div class="btn btn-secondary btn-sm">Adjuntar foto</div>
                </label>
                <input type="file" id="fotoPerfil" class="d-none" @change="handleFileUpload" ref="fotoPerfilInput"/>
              </div>

              <!-- Formulario -->
              <div class="flex-grow-1 w-100">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre(s)</label>
                    <input type="text" class="form-control" v-model="form.nombre" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" v-model="form.apellido" />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" v-model="form.correo" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" class="form-control" v-model="form.fechaNacimiento" />
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Rol</label>
                  <select class="form-select" v-model.number="form.rol">
                    <option disabled value="">Seleccione un rol</option>
                    <option value="1">Mentor oficial</option>
                    <option value="2">Mentor invitado</option>
                    <option value="3">Estudiante</option>
                  </select>
                </div>

                <!-- Sección específica para Estudiante -->
                <div v-if="form.rol === 3" class="mb-3">
                  <hr />
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Código de estudiante</label>
                      <input type="text" class="form-control" v-model="form.codigoEstudiante" />
                    </div>

                    <div class="col-md-4 mb-3">
                      <label class="form-label">Facultad</label>
                      <select class="form-select" v-model="form.facultad">
                        <option disabled value="">Seleccione</option>
                        <option v-for="fac in facultades" :key="fac.codigo_facultad" :value="fac.codigo_facultad">
                          {{ fac.nombre_facultad }}
                        </option>
                      </select>
                    </div>

                    <div v-if="form.facultad === ''" class="col-md-4 mb-3">
                      <label class="form-label">Carrera</label>
                      <select class="form-select" disabled>
                        <option disabled value="">Seleccione</option>
                        <option v-for="car in carrerasFiltradas" :key="car.codigo" :value="car.codigo_carrera">
                          {{ car.nombre_carrera }}
                        </option>
                      </select>
                    </div>
                    <div v-else class="col-md-4 mb-3">
                      <label class="form-label">Carrera</label>
                      <select class="form-select" v-model.number="form.carrera">
                        <option disabled value="">Seleccione</option>
                        <option v-for="car in carrerasFiltradas" :key="car.codigo" :value="car.codigo_carrera">
                          {{ car.nombre_carrera }}
                        </option>
                      </select>
                    </div>

                  </div>
                </div>

                <!-- Sección específica para Mentor invitado -->
                <div v-if="form.rol === 2" class="mb-3">
                  <hr />
                  <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control" placeholder="Descripción del mentor"
                      v-model="form.descripcion"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Linkedin</label>
                    <input type="text" class="form-control" v-model="form.linkedin" />
                  </div>
                </div>

                <div class="mb-1">
                  <button class="btn btn-success w-10" @click="abrirModalPassword">
                    <i class="fa-solid fa-lock"></i>
                    Establecer contraseña
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
            <button class="btn btn-crear-cuenta" @click="grabNuevaCuenta()">Crear cuenta</button>
          </div>
        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @enviar-contrasena="recibirContrasena"
      @close-modalCrearContrasena="handleCerrarModal">
    </component>
  </div>
</template>


<script>
import Swal from 'sweetalert2';
import ModalCrearContrasena from './ModalCrearContrasena.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
  name: 'ModalCrearCuenta',
  components: {
    ModalCrearContrasena
  },
  data() {
    return {
      isModalOpen: false,
      facultades: [],
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
      defaultImage: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
      currentView: null,
      currentProps: null
    }
  },
  computed: {
    ...mapGetters(['usuario']), //Llamar al store Usuario

    carrerasFiltradas() {
      const seleccionada = this.facultades.find(f => f.codigo_facultad == this.form.facultad)
      return seleccionada ? seleccionada.carreras : []
    }
  },
  mounted() {

    axios.get('/obtenerCarreras').then(
      response => {
        this.facultades = response.data
      }
    ).catch(err => {
      console.error("Existe este error: ", err)
    })


  },
  methods: {
    handleCerrarModal() {
      this.currentView = null
      this.currentProps = null
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      const formVacio = {
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
      };

      const hayCambios = Object.keys(this.form).some(
        key => this.form[key] !== formVacio[key]
      );

      if (hayCambios) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: 'Recuerda que tus cambios no se han guardado.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, cerrar',
          cancelButtonText: 'Cancelar'
        }).then(result => {
          if (result.isConfirmed) {
            this.$emit('close-modalCrearCuenta');
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
          }
        });
      } else {
        this.$emit('close-modalCrearCuenta');
        this.isModalOpen = false;
        document.body.classList.remove('modal-open');
      }
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
            for(let key in this.form){
              formData.append(key, this.form[key])
            }

            if (this.$refs.fotoPerfilInput && this.$refs.fotoPerfilInput.files[0]) {
              formData.append('foto', this.$refs.fotoPerfilInput.files[0]);
            }

            axios.post('/registrarCuenta', formData, {
              headers:{
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              this.$emit('cargar-listaUsuarios')

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

    abrirModalPassword() {
      this.currentView = ModalCrearContrasena
      this.currentProps = {
        tipo: 'crearContra'
      }
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    recibirContrasena(datos) {
      this.form.contrasena = datos.contrasena
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
</style>
