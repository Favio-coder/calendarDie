<template>
  <div>
    <!-- Modal Principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">
              {{ tipoMod === 'estudiante' ? 'Asignar Estudiante a Programa' : 'Asignar Mentor a Programa' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div v-if="isLoading" class="text-center my-4">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando usuarios...</p>
            </div>

            <div v-else>
              <!-- Filtros -->
              <div class="mb-3 row">
                <div class="mb-3">
                  <input type="text" class="form-control" v-model="filtro" placeholder="Buscar por nombre o apellido">
                </div>
                <div v-if="tipoMod === 'estudiante'" class="col-md-4">
                  <select v-model="filtroEquipo" class="form-select">
                    <option value="">Todos los equipos</option>
                    <option v-for="equipo in equiposDisponibles" :key="equipo" :value="equipo">{{ equipo }}</option>
                  </select>
                </div>
              </div>

              <!-- Tabla principal -->
              <div class="table-wrapper">
                <div class="table-responsive tabla-scroll">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Nombre</th>
                        <th v-if="tipoMod === 'estudiante'">Equipo</th>
                        <th>Programa</th>
                        <th>Acción</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(usuario, index) in usuariosFiltrados" :key="index">
                        <td>{{ usuario.l_apellido }} {{ usuario.l_nombre }}</td>
                        <td v-if="tipoMod === 'estudiante'">
                          {{ usuario.l_equipo || 'No tiene equipo' }}
                        </td>
                        <td>
                          <select class="form-select" v-model="usuarioSeleccionado[usuarioIndex(usuario)]"
                            :disabled="tipoMod === 'estudiante' && !usuario.l_equipo">
                            <option disabled value="">Seleccione un programa</option>
                            <option v-for="programa in programas" :key="programa.c_programa"
                              :value="programa.c_programa">
                              {{ programa.l_programa }}
                            </option>
                          </select>
                        </td>
                        <td>
                          <button class="btn btn-success" @click="asignarPrograma(usuario)"
                            :disabled="(tipoMod === 'estudiante' && !usuario.l_equipo) || !usuarioSeleccionado[usuarioIndex(usuario)]">
                            Asignar
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Tabla secundaria -->
              <div v-if="inscritosLocales.length > 0" class="mt-4">
                <h5 class="mb-3">Usuarios Asignados</h5>
                <div class="mb-3">
                  <input type="text" class="form-control" v-model="filtroAsignados" placeholder="Buscar en asignados">
                </div>
                <div class="table-wrapper">
                  <div class="table-responsive tabla-scroll">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Nombre</th>
                          <th v-if="tipoMod === 'estudiante'">Equipo</th>
                          <th>Programa</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(usuario, index) in filtradosAsignados" :key="'asignado-' + index">
                          <td>{{ usuario.l_apellido }} {{ usuario.l_nombre }}</td>
                          <td v-if="tipoMod === 'estudiante'">{{ usuario.l_equipo || 'No tiene equipo' }}</td>
                          <td>{{ usuario.l_programa ? usuario.l_programa : 'No inscrito' }}</td>
                          <td>
                            <div class="d-flex gap-2">
                              <button v-if="!usuario.q_enviado" class="btn btn-primary btn-sm"
                                @click="eliminarAsignado(index)">
                                <i class="fas fa-times"></i>
                              </button>
                              <button v-if="usuario.q_enviado" class="btn btn-danger btn-sm"
                                @click="eliminarElemento(usuario)">
                                <i class="fas fa-trash"></i>
                              </button>
                              <button v-if="tipoMod === 'estudiante' && usuario.q_enviado && usuario.c_matricula"
                                class="btn btn-secondary btn-sm" @click="genPDF(usuario)">
                                <i class="fas fa-print"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" @click="guardarAsignaciones">Guardar</button>
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalImpresion="cerrarModalImpresion">
    </component>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Swal from 'sweetalert2';
import axios from 'axios';
import ModalImpresion from '../layouts/ModalImpresion.vue';

export default {
  name: 'ModalAsignProgramDet',
  components: {
    ModalImpresion
  },
  props: {
    tipoMod: { type: String, required: true },
    usuarios: { type: Array, required: true },
    inscritos: { type: Array, required: true }
  },
  data() {
    return {
      isModalOpen: false,
      isLoading: false,
      usuarioSeleccionado: {},
      filtro: '',
      filtroEquipo: '',
      filtroAsignados: '',
      inscritosLocales: [],
      currentView: null,
      currentProps: null
    };
  },
  computed: {
    ...mapGetters(['usuario', 'programas']),
    equiposDisponibles() {
      const equipos = this.usuarios.filter(u => u.l_equipo).map(u => u.l_equipo);
      return [...new Set(equipos)];
    },
    usuariosFiltrados() {
      return this.usuarios
        .filter(u => u.c_rol === (this.tipoMod === 'estudiante' ? '3' : '2'))
        .filter(u => {
          const nombreCompleto = `${u.l_nombre} ${u.l_apellido}`.toLowerCase();
          const equipo = u.l_equipo?.toLowerCase() || '';
          const pasaFiltroNombre = nombreCompleto.includes(this.filtro.toLowerCase());
          const pasaFiltroEquipo = this.tipoMod === 'estudiante'
            ? (this.filtroEquipo === '' || equipo === this.filtroEquipo.toLowerCase())
            : true;

          if (this.tipoMod === 'estudiante') {
            const yaAsignado = this.inscritosLocales.some(a =>
              a.c_usuario === u.c_usuario &&
              a.c_equipo === u.c_equipo &&
              a.c_programa != null
            );
            return pasaFiltroNombre && pasaFiltroEquipo && !yaAsignado;
          } else {
            return pasaFiltroNombre && pasaFiltroEquipo; // Mentores siempre visibles
          }
        });
    },
    filtradosAsignados() {
      return this.inscritosLocales
        .filter(u => u.c_programa != null)
        .filter(u => {
          const nombreCompleto = `${u.l_apellido} ${u.l_nombre}`.toLowerCase();
          return nombreCompleto.includes(this.filtroAsignados.toLowerCase());
        });
    }
  },
  watch: {
    inscritos: {
      immediate: true,
      handler(val) {
        this.inscritosLocales = val.map(u => ({
          ...u,
          q_enviado: true,
          l_programa: u.l_programa || this.obtenerNombrePrograma(u.c_programa)
        }));
      }
    }
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('close-modalAsignProgramEst');
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    usuarioIndex(usuario) {
      return `${usuario.l_correoElectronico}-${usuario.l_equipo || 'sin-equipo'}`;
    },
    obtenerNombrePrograma(id) {
      const prog = this.programas.find(p => p.c_programa == id);
      return prog ? prog.l_programa : 'Desconocido';
    },
    asignarPrograma(usuario) {
      const programaId = this.usuarioSeleccionado[this.usuarioIndex(usuario)];
      if (!programaId) return;

      // Verificar si ya existe esa combinación
      const yaExiste = this.inscritosLocales.some(i =>
        i.c_usuario === usuario.c_usuario && i.c_programa === programaId
      );

      if (yaExiste) {
        Swal.fire({
          icon: 'warning',
          title: 'Asignación duplicada',
          text: 'Este usuario ya está asignado a este programa.',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      const reenviadoOriginal = this.inscritos.find(i =>
        i.c_usuario === usuario.c_usuario &&
        i.c_equipo === usuario.c_equipo &&
        i.c_programa === programaId &&
        i.c_matricula
      );

      const copia = {
        ...usuario,
        c_programa: programaId,
        l_programa: this.obtenerNombrePrograma(programaId),
        q_enviado: !!reenviadoOriginal,
        c_matricula: reenviadoOriginal?.c_matricula || null
      };

      this.inscritosLocales.push(copia);
      this.$delete(this.usuarioSeleccionado, this.usuarioIndex(usuario));

      if (this.tipoMod === 'estudiante') {
        // Para estudiantes sí lo eliminamos del listado superior
        const indexEnUsuarios = this.usuarios.findIndex(u =>
          u.c_usuario === usuario.c_usuario && u.c_equipo === usuario.c_equipo
        );
        if (indexEnUsuarios !== -1) {
          this.usuarios.splice(indexEnUsuarios, 1);
        }
      }
    },
    eliminarAsignado(index) {
      this.inscritosLocales.splice(index, 1);
    },
    guardarAsignaciones() {
      const nuevos = this.inscritosLocales.filter(u => u.q_enviado === false);
      if (nuevos.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'Sin asignaciones nuevas',
          text: 'No hay usuarios nuevos para guardar.',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      const asignacionesEnviar = {
        asignaciones: nuevos,
        tipoEnvio: this.tipoMod,
      };

      axios.post('/grabMatriculaMentorEstudiante', asignacionesEnviar)
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: '¡Asignación exitosa!',
            text: response.data.mensaje || 'Los datos fueron guardados correctamente.',
            confirmButtonText: 'Aceptar'
          });

          this.inscritosLocales.forEach(u => u.q_enviado = true);
        });
    },
    genPDF(usuario){
      axios.post('/genPdfCompromiso', { usuario }, { responseType: 'blob' })
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
    cerrarModalImpresion(){
      this.currentProps=null
      this.currentView=null
    },
    eliminarElemento(data) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas eliminar esta asignación?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
      }).then((result) => {
        if (result.isConfirmed) {
          const dataEnviar = {
            c_matricula: data.c_matricula,
            tipo: this.tipoMod
          };

          axios.post('/elimMatriculaMentorEst', dataEnviar).then(() => {
            const copia = {
              ...data,
              q_enviado: false,
              c_programa: null,
              c_matricula: null
            };

            this.inscritosLocales = this.inscritosLocales.filter(u => u.c_matricula !== data.c_matricula);
            delete copia.l_programa;
            this.usuarios.push(copia);

            Swal.fire('Eliminado', 'La asignación fue eliminada.', 'success');
          }).catch(() => {
            Swal.fire('Error', 'No se pudo eliminar la asignación.', 'error');
          });
        }
      });
    }
  }
};
</script>

<style scoped>
.titulo-agend-actividad {
  background-color: #12BACA;
}

.btn-close {
  filter: invert(1) brightness(2);
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

.table-wrapper {
  position: relative;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  overflow: hidden;
  margin-bottom: 1rem;
}

.tabla-scroll {
  max-height: 300px;
  overflow-y: auto;
}
</style>
