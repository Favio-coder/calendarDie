<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog " style="max-width: 900px;">
        <div class="modal-content modal-sombra">
          <div class="modal-header titulo-agend-actividad">
            <h5 v-if="!q_editar" class="modal-title text-white">Agregar Equipo</h5>
            <h5 v-else class="modal-title text-white">Editar Equipo</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <!-- Contenedor de foto + formulario -->
            <div class="d-flex flex-column flex-md-row gap-4">
              <!-- Foto de perfil -->
              <div class="profile-pic-section d-flex flex-column align-items-center text-center">
                <label for="fotoPerfil" class="profile-label">
                  <img :src="preview || defaultImage" class="profile-img mb-2" />
                  <div class="btn btn-secondary btn-sm">Adjuntar logo</div>
                </label>
                <input type="file" id="fotoPerfil" class="d-none" @change="handleFileUpload" ref="logoEquipoInput" />
              </div>

              <!-- Formulario a la derecha de la foto -->
              <div class="flex-grow-1 w-100">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Nombre del equipo</label>
                    <input type="text" class="form-control" v-model="form.nombreEquipo"
                      placeholder="Nombre del equipo" />
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Descripción del equipo</label>
                    <textarea class="form-control" placeholder="Descripción del equipo"
                      v-model="form.descripcionEquipo"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tabla + botón debajo del formulario -->
            <div class="mt-4">
              <h6>Estudiantes del equipo</h6>

              <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                  <thead class="table-light">
                    <tr>
                      <th>Nombre</th>
                      <th>Apellido</th>
                      <th>Código</th>
                      <th>Carrera</th>
                      <th>Líder</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="est in estudiantesEquipo" :key="est.c_estudiante">
                      <td>{{ est.l_nombre }}</td>
                      <td>{{ est.l_apellido }}</td>
                      <td>{{ est.c_estudiante }}</td>
                      <td>{{ est.l_carrera }}</td>
                      <td>
                        <div class="form-check form-switch d-flex justify-content-center">
                          <input class="form-check-input" type="checkbox" :checked="est.q_lider == '1'"
                            @change="asignarLider(est)" />
                        </div>
                      </td>
                      <td>
                        <button v-if="!q_editar"  @click="elimEstxEquipo(est)" class="btn btn-danger btn-sm">
                          <i class="fa-solid fa-trash-can"></i>
                        </button>
                         <button v-else @click="elimEstxEquipoTotal(est)" class="btn btn-danger btn-sm">
                          <i class="fa-solid fa-trash-can"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Botón Agregar Estudiantes -->
              <div class="text-end mt-2">
                <button class="btn btn-success" @click="abrirModalAgregarEst">
                  <i class="fa-solid fa-plus"></i> Agregar Estudiantes
                </button>
              </div>
            </div>
          </div>


          <div class="modal-footer">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
            <button v-if="!q_editar" class="btn btn-crear-cuenta" @click="grabNuevoEquipo()">Crear equipo</button>
            <button v-else class="btn btn-crear-cuenta" @click="editarEquipo()">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @agregar-estudiante="agregarEstxEquipo"
      @close-modalEquipo="cerrarModalEquipo">
    </component>
  </div>
</template>


<script>
import Swal from 'sweetalert2';
import ModalAgregarEst from './ModalAgregarEst.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
  name: 'ModalAgregarEquipo',
  components: {
    ModalAgregarEst
  },
  props: {
    equipoProp: {
      type: Object,
      required: false
    },
    tipoEquipoProp: {
      type: String,
      required: false
    }
  },
  data() {
    return {
      isModalOpen: false,
      facultades: [],
      estudiantes: [],
      carreras: [],
      estudiantesEquipo: [],
      eliminados: [],
      form: {
        nombreEquipo: '',
        descripcionEquipo: '',
        estudiantesEquipo: []
      },
      preview: null,
      defaultImage: 'https://www.shutterstock.com/image-vector/image-icon-trendy-flat-style-600nw-643080895.jpg',
      currentView: null,
      currentProps: null,
      q_editar: false
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
    const esEdicion = this.tipoEquipoProp === 'editarEquipo'

    if (esEdicion) {
      this.q_editar = true
      this.preview = this.equipoProp.l_logoEquipo
      this.form.nombreEquipo = this.equipoProp.l_equipo
      this.form.descripcionEquipo = this.equipoProp.l_descripEquipo
    }

    axios.get('/obtenerEstudiantesXcarrera')
      .then(res => {
        const estudiantes = res.data.estudiantes
        this.estudiantes = estudiantes       

        if (!esEdicion) return

        const mapaCarreras = new Map()
        this.carreras = estudiantes.reduce((acc, est) => {
          if (!mapaCarreras.has(est.c_carrera)) {
            mapaCarreras.set(est.c_carrera, est.l_carrera)
            acc.push({ c_carrera: est.c_carrera, l_carrera: est.l_carrera })
          }
          return acc
        }, [])

        if (Array.isArray(this.equipoProp.integrantes)) {
          const mapaLideres = new Map(
            this.equipoProp.integrantes.map(i => [i.c_usuario, i.q_lider])
          )

          this.estudiantesEquipo = estudiantes
            .filter(e => mapaLideres.has(e.c_usuario))
            .map(e => ({
              ...e,                       
              q_lider: mapaLideres.get(e.c_usuario) 
            }))
        }

      })
      .catch(err => console.error('Error:', err))
      .finally(() => (this.isLoading = false))
  },
  methods: {
    cerrarModalEquipo() {
      this.currentView = null
      this.currentProps = null
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('close-modalEquipo');
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
    grabNuevoEquipo() {
      Swal.fire({
        title: "Mensaje",
        text: "Se creará un equipo, ¿estás seguro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Crear equipo",
        cancelButtonText: "Cancelar"
      }).then(response => {
        if (response.isConfirmed) {
          // if (this.estudiantesEquipo.length == 0) {
          //   Swal.fire({
          //     title: "Mensaje",
          //     text: "¡Un estudiante debe estar al menos en un equipo!",
          //     icon: "warning",
          //     confirmButtonColor: "#3085d6",
          //     confirmButtonText: "Volver",
          //   })
          //   return
          // }

          const formData = new FormData()

          //Guardar imagen
          const fileInput = this.$refs.logoEquipoInput
          const file = fileInput.files[0]
          if (file) {
            formData.append('logo', file)
          }

          formData.append('nombreEquipo', this.form.nombreEquipo)
          formData.append('descripcionEquipo', this.form.descripcionEquipo);
          formData.append('estudiantes', JSON.stringify(this.estudiantesEquipo));

          // Mostrar todo en consola
          // console.log("Contenido del FormData:");
          // for (let pair of formData.entries()) {
          //   console.log(pair[0], pair[1]);
          // }

          axios.post('/grabarEquipo', formData).then(response => {
            Swal.fire({
              icon: 'success',
              title: '¡Equipo registrado!',
              text: 'El equipo fue creado exitosamente.',
              confirmButtonText: 'Aceptar'
            }).then(() => {
              this.closeModal(); // Cierra el modal
              this.resetFormulario(); // Reinicia todo para la siguiente vez
            });
          }).catch(error => {
            console.error(error);
          });

        }
      });
    },
     editarEquipo () {
        Swal.fire({
          title: 'Mensaje',
          text:  'Se editará este equipo, ¿estás seguro?',
          icon:  'question',
          showCancelButton: true,
          confirmButtonText: 'Editar equipo',
          cancelButtonText:  'Cancelar'
        }).then(res => {
          if (!res.isConfirmed) return

          const fd = new FormData()
          fd.append('c_equipo', this.equipoProp.c_equipo)
          fd.append('nombreEquipo',this.form.nombreEquipo)
          fd.append('descripcionEquipo',this.form.descripcionEquipo)
          fd.append('estudiantes', JSON.stringify(this.estudiantesEquipo));
          fd.append('estudiantesEliminar', JSON.stringify(this.eliminados))
          fd.append('logoActual', this.equipoProp.l_logoEquipo) 

          const file = this.$refs.logoEquipoInput?.files[0]
          if (file) fd.append('logo', file)

          axios.post('/editEquipo', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          .then(() => {
            Swal.fire('¡Actualizado!', 'El equipo fue modificado.', 'success')
              .then(() => { this.closeModal(); this.resetFormulario() })
          })
          
        })
      },
    abrirModalAgregarEst() {
      this.currentView = ModalAgregarEst
      this.currentProps = {
        estudiantesProp: this.estudiantes,
        carrerasProp: this.carreras,
        estudiantesEquipo: this.estudiantesEquipo
      };
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },

    //Logica de agregar estudiantes a la lista para crear el equipo
    agregarEstxEquipo(est) {
      this.estudiantesEquipo.push(est)
    },

    elimEstxEquipo(est) {
      this.estudiantesEquipo = this.estudiantesEquipo.filter(e => e.c_usuario !== est.c_usuario)
      this.$refs.modalRef?.recibirEstudianteDevuelto(est)
      //this.abrirModalAgregarEst()
    },
    elimEstxEquipoTotal(est) {
      this.estudiantesEquipo = this.estudiantesEquipo.filter(e => e.c_usuario !== est.c_usuario)
      this.$refs.modalRef?.recibirEstudianteDevuelto(est)

      this.eliminados.push(est)
    },
    asignarLider(estSeleccionado) {
      const yaEsLider = estSeleccionado.q_lider == "1";

      this.estudiantesEquipo = this.estudiantesEquipo.map(est => {
        if (yaEsLider && est.c_usuario === estSeleccionado.c_usuario) {
          return { ...est, q_lider: "0" };
        }

        return {
          ...est,
          q_lider: est.c_usuario === estSeleccionado.c_usuario ? "1" : "0"
        };
      });
    },
    resetFormulario() {
      this.estudiantesEquipo = [];
      this.form = {
        nombreEquipo: '',
        descripcionEquipo: '',
        estudiantesEquipo: []
      };
      this.preview = null;
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

.modal-sombra {
  box-shadow: -5px 0 25px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 0, 0, 0.15);
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}
</style>
