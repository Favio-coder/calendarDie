<template>
  <div>
    <!-- MODAL -->
    <div class="modal fade" :class="{ show: isModalOpen }" :style="{ display: isModalOpen ? 'block' : 'none' }"
      aria-modal="true" role="dialog" @click.self="closeModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <!-- Header -->
          <div class="modal-header text-white" style="background:#12BACA">
            <h5 v-if="!q_edit" class="modal-title">Agendar Actividad</h5>
            <h5 v-else class="modal-title">Actividad - {{ actividadProp.l_actividad }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <form @submit.prevent="guardarActividad">
              <!-- Nombre / Descripción -->
              <div v-if="!q_edit"  class="mb-3">
                <label class="form-label">Nombre de la actividad</label>
                <input v-model="form.l_actividad" type="text" class="form-control" />
              </div>
              <div v-else  class="mb-3">
                <label class="form-label">Nombre de la actividad</label>
                <input v-model="form.l_actividad" type="text" class="form-control" disabled />
              </div>

              <div v-if="!q_edit" class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.l_descripcion" class="form-control" rows="3"></textarea>
              </div>
              <div v-else class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.l_descripcion" class="form-control" rows="3" disabled></textarea>
              </div>

              <!-- Día / Hora / Responsable -->
              <div class="row">
                <div v-if="!q_edit" class="col-md-4 mb-3">
                  <label class="form-label">Día</label>
                  <input v-model="form.l_diaActividad" type="date" class="form-control" />
                </div>
                <div v-else class="col-md-4 mb-3">
                  <label class="form-label">Día</label>
                  <input v-model="form.l_diaActividad" type="date" class="form-control" disabled />
                </div>
                <div v-if="!q_edit" class="col-md-3 mb-3">
                  <label class="form-label">Hora</label>
                  <input v-model="form.l_horaActividad" type="time" class="form-control"  />
                </div>
                <div v-else  class="col-md-3 mb-3">
                  <label class="form-label">Hora</label>
                  <input v-model="form.l_horaActividad" type="time" class="form-control" disabled />
                </div>
                <div class="col-md-5 mb-3">
                  <label class="form-label">Responsable</label>
                  <input v-if="!q_edit" :value="usuario.l_nombre" class="form-control" disabled />
                  <input v-else :value="actividadProp.l_nombre" class="form-control" disabled />
                </div>
              </div>

              <!-- Selector de recursos -->
              <div class="mb-3">
                <label v-if="!q_edit" class="form-label">Añadir recurso</label>
                <div v-if="!q_edit" class="d-flex gap-2">
                  <select v-model="recursoSeleccionado" class="form-select">
                    <option value="" disabled>Seleccione…</option>
                    <option v-for="rec in recursosDisponibles" :key="rec.c_recurso" :value="rec">
                      {{ rec.l_recurso }}
                    </option>
                  </select>
                  <button type="button" class="btn btn-outline-success" @click="agregarRecurso"
                    :disabled="!recursoSeleccionado">
                    ＋
                  </button>
                </div>
              </div>

              <!-- Tabla de recursos elegidos -->
              <div v-if="form.recursosSeleccionados.length">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th style="width:75%">Recurso</th>
                      <th class="text-center" style="width:25%">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="rec in form.recursosSeleccionados" :key="rec.c_recurso">
                      <td>{{ rec.l_recurso }}</td>
                      <td class="text-center">
                        <button v-if="!q_edit" type="button" class="btn btn-sm btn-danger" @click="quitarRecurso(rec)">
                          <i class="fas fa-trash"></i>
                        </button>
                        <button v-else type="button" class="btn btn-sm btn-danger" disabled>
                          <i class="fas fa-trash" ></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="!q_edit" class="text-end">
                <button class="btn btn-primary">Guardar actividad</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import Swal from 'sweetalert2'

export default {
  name: 'ModalAgendar',
  data() {
    return {
      isModalOpen: false,
      recursosDisponibles: [],
      recursoSeleccionado: null,
      form: {
        l_actividad: '',
        l_descripcion: '',
        l_diaActividad: '',
        l_horaActividad: '',
        recursosSeleccionados: []
      },
      q_edit:false
    }
  },
  props: {
    accionActividadProp: String,
    actividadProp: Object
  },
  computed: { ...mapGetters(['usuario']) },
  mounted() {
    //this.cargarRecursos()

    if (this.accionActividadProp === 'editarActividad' && this.actividadProp) {
      axios.get('/listRecursos')
        .then(res => {
          this.q_edit = true
          this.recursosDisponibles = res.data.recursos


          if (this.accionActividadProp === 'editarActividad' && this.actividadProp) {
            this.form.l_actividad = this.actividadProp.l_actividad
            this.form.l_descripcion = this.actividadProp.l_descripcion
            this.form.l_diaActividad = this.actividadProp.l_diaActividad
            this.form.l_horaActividad = this.actividadProp.l_horaActividad.substring(0, 5)

            const idsSeleccionados = this.actividadProp.c_recursos
              ? this.actividadProp.c_recursos.split(',').map(id => id.trim())
              : []

            this.form.recursosSeleccionados = this.recursosDisponibles.filter(r =>
              idsSeleccionados.includes(String(r.c_recurso))
            )

            this.recursosDisponibles = this.recursosDisponibles.filter(
              r => !idsSeleccionados.includes(String(r.c_recurso))
            )

            this.form.c_actividad = this.actividadProp.c_actividad
          }
        })
    } else {
      this.cargarRecursos()
    }


  },
  methods: {
    openModal() {
      this.isModalOpen = true
      document.body.classList.add('modal-open')
    },
    closeModal() {
      this.$emit('cerrar-ModalAgendar')
      this.isModalOpen = false
      document.body.classList.remove('modal-open')
    },

    cargarRecursos() {
      axios.get('/listRecursos')
        .then(res => { this.recursosDisponibles = res.data.recursos })
        .catch(console.error)
    },


    agregarRecurso() {
      if (!this.recursoSeleccionado) return

      this.form.recursosSeleccionados.push(this.recursoSeleccionado)

      this.recursosDisponibles = this.recursosDisponibles.filter(
        r => r.c_recurso !== this.recursoSeleccionado.c_recurso
      )

      this.recursoSeleccionado = null
    },

    quitarRecurso(rec) {
      this.recursosDisponibles.push(rec)

      this.form.recursosSeleccionados = this.form.recursosSeleccionados.filter(
        r => r.c_recurso !== rec.c_recurso
      )
    },

    guardarActividad() {
      const payload = {
        c_usuario: this.usuario.c_usuario,
        l_actividad: this.form.l_actividad,
        l_descripcion: this.form.l_descripcion,
        l_diaActividad: this.form.l_diaActividad,
        l_horaActividad: this.form.l_horaActividad,
        recursos: this.form.recursosSeleccionados.map(r => r.c_recurso).join(',')
      }

      Swal.fire({
        title: '¿Crear actividad?',
        text: '¿Estás seguro de guardar esta actividad?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
      }).then(result => {
        if (result.isConfirmed) {
          axios.post('/grabActividad', payload)
            .then(() => {
              Swal.fire({
                title: '¡Éxito!',
                text: 'Actividad creada correctamente.',
                icon: 'success',
                confirmButtonColor: '#12BACA'
              })
              this.closeModal()
            })
        }
      })
    }

  }
}
</script>
