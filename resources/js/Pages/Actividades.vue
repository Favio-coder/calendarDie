<template>
  <div class="main-container">
    <Header />
    <!-- Encabezado llamativo -->
    <div class="agenda-header text-white text-center py-3">
      <h2 class="display-5 fw-bold mb-1">📅 Agenda de Actividades</h2>
      <p class="lead">Consulta y programa eventos en tu calendario del ecosistema</p>
    </div>
    <div class="content">
      <!-- Calendario: 70% ancho -->
      <div ref="calendar" class="calendar"></div>

      <!-- Panel lateral: 30% ancho -->
      <div class="sidebar">
        <div style="display: flex; justify-content: center; align-items: center;">
          <button class="items-center button-agendar" @click="agregarEvento()">
            <i class="fas fa-plus"></i> Agendar actividad
          </button>
        </div>



        <div v-if="diaSeleccionado">
          <h3>{{ diaSeleccionadoText }}</h3>
        </div>
        <div v-else>
          <h4>Selecciona una fecha en el calendario</h4>
        </div>


        <div v-if="diaSeleccionado">
          <hr />
        </div>

        <div v-if="diaSeleccionado">
          <ul class="list-unstyled">
            <li v-for="event in eventsFordiaSeleccionado" :key="event.id" class="card shadow-sm mb-2 border-0">
              <div class="card-body d-flex justify-content-between align-items-center"
                @dblclick="handleDoubleClick(event.id)">
                <div>
                  <h6 class="mb-0 fw-bold">{{ event.title }}</h6>
                  <span>Fecha: </span><small class="text-muted">{{ formatearFecha(event.start) }}</small>
                  <br>
                  <small class="text-muted">{{ event.extendedProps.usuario }}</small>
                </div>
                <button @click="removeEvent(event)" class="btn btn-sm btn-outline-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </li>

            <li v-if="eventsFordiaSeleccionado.length === 0" class="text-muted text-center mt-3">
              No hay eventos para esta fecha
            </li>
          </ul>
        </div>

      </div>
    </div>
    <component :is="currentView" v-bind="currentProps" ref="modalRef" @cerrar-ModalAgendar="cerrarModalAgendar">
    </component>
  </div>
</template>

<script>
import Header from '@/components/Header.vue';
import Prueba from './Prueba.vue'
import ModalAgendar from '../Components/ModalAgendar.vue'
import { nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';

//Librerias
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import esLocale from '@fullcalendar/core/locales/es'

//CSS y js
import '@fullcalendar/core/main.css'
import '@fullcalendar/daygrid/main.css'
import '@fullcalendar/timegrid/main.css'
import '@fullcalendar/list/main.css'

export default {
  components: { Header, Prueba, ModalAgendar },
  data() {
    return {
      calendar: null,
      events: [],
      actividades: [],
      diaSeleccionado: null,
      diaSeleccionadoText: null,
      newEventTitle: '',
      currentProps: null,
      currentView: null,
      q_agreAct: false,
      q_elimAct: false
    }
  },
  computed: {
    ...mapGetters(['usuario']),

    eventsFordiaSeleccionado() {
      if (!this.diaSeleccionado) return []
      this.diaSeleccionadoText = this.convertirFecha(this.diaSeleccionado)
      return this.events.filter(event => event.start.startsWith(this.diaSeleccionado))
    }
  },
  mounted() {
    this.listEventos()

     const specs = [
      { key: 'q_agreAct', modulo: 'actividades', tipo: 'agregar', desc: 'No agregar ninguna actividad' },
      { key: 'q_elimAct', modulo: 'actividades', tipo: 'eliminar', desc: 'No eliminar ninguna actividad' },
    ];

    Promise.all(specs.map(sp =>
      axios.post('/devPermiso', {
        c_usuario: this.usuario.c_usuario,
        modulo: sp.modulo,
        tipo: sp.tipo,
        descripcion: sp.desc
      }).then(r => ({ key: sp.key, valor: r.data.permiso }))
    ))
      .then(resultados => {
        resultados.forEach(({ key, valor }) => { this[key] = valor; });
      });

    this.calendar = new Calendar(this.$refs.calendar, {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
      initialView: 'dayGridMonth',
      locale: esLocale,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      height: '100%',
      selectable: true,
      editable: true,
      events: this.events,
      select: this.handleDateSelect,
      eventClick: this.handleEventClick,
      selectMirror: true,
      dayMaxEvents: true
    })
    this.calendar.render()
  },
  methods: {
    cerrarModalAgendar() {
      this.listEventos()
      this.currentProps = null
      this.currentView = null
    },
    convertirFecha(fecha) {
      const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
      const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre']

      const d = new Date(fecha)
      const diaSemana = diasSemana[d.getDay()]
      const dia = d.getDate()
      const mes = meses[d.getMonth()]
      const anio = d.getFullYear()

      return `${diaSemana} ${dia} de ${mes}`
    },
    handleDateSelect(selectionInfo) {
      this.diaSeleccionado = selectionInfo.startStr
    },
    handleEventClick(clickInfo) {
      this.handleDoubleClick(clickInfo.event.id)
    },
    agregarEvento() {
      if (this.q_agreAct) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      this.currentView = ModalAgendar
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal()
        }
      })
    },
    handleDoubleClick(eventId) {
      const actividad = this.actividades.find(a => a.c_actividad == eventId);
      if (actividad) {
        this.editarEvento(actividad);
      }
    },
    editarEvento(actividad) {
      this.currentView = ModalAgendar
      this.currentProps = {
        accionActividadProp: 'editarActividad',
        actividadProp: actividad
      }
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal()
        }
      })
    },
    removeEvent(evento) {
      if (this.q_elimAct) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      const eventId = typeof evento === 'object' ? evento.id : evento

      Swal.fire({
        title: '¿Eliminar actividad?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
      }).then(result => {
        if (result.isConfirmed) {

          axios.post('/elimActividad', evento)
            .then(() => {
              // Quitar del arreglo local
              this.events = this.events.filter(e => e.id !== eventId)

              // Quitar del calendario FullCalendar
              const calendarEvent = this.calendar.getEventById(eventId)
              if (calendarEvent) calendarEvent.remove()

              Swal.fire({
                title: 'Eliminado',
                text: 'La actividad fue eliminada.',
                icon: 'success',
                confirmButtonColor: '#12BACA'
              })
            })
        }
      })
    },
    formatearFecha(fechaStr) {
      const fecha = new Date(fechaStr);
      const dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
      const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

      const diaSemana = dias[fecha.getDay()];
      const dia = fecha.getDate();
      const mes = meses[fecha.getMonth()];
      const anio = fecha.getFullYear();

      return `${diaSemana} ${dia} de ${mes} del ${anio}`;
    },
    listEventos() {
      axios.get('/listActividad').then(r => {
        this.actividades = r.data.actividades
        const rows = r.data.actividades;
        const map = new Map();

        rows.forEach(row => {
          const fechaISO = `${row.l_diaActividad}T${row.l_horaActividad.substring(0, 5)}`;
          if (!map.has(row.c_actividad)) {
            map.set(row.c_actividad, {
              id: row.c_actividad,
              title: row.l_actividad,
              start: fechaISO,
              description: row.l_descripcion,
              extendedProps: {
                usuario: `${row.l_nombre} ${row.l_apellido}`,
                recursos: [row.l_recurso]
              }
            });
          } else {
            map.get(row.c_actividad).extendedProps.recursos.push(row.l_recurso);
          }
        });

        this.events = Array.from(map.values());

        if (this.calendar) {
          this.calendar.removeAllEvents();
          this.calendar.getEventSources().forEach(src => src.remove());
        }

        if (this.calendar) {
          this.calendar.addEventSource(this.events);
        }
      });
    }

  },
  beforeDestroy() {
    if (this.calendar) {
      this.calendar.destroy()
    }
  }
}
</script>

<style scoped>
/* Ocultar scroll global completamente */
html,
body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
}

/* Contenedor principal */
.main-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Contenedor interno */
.content {
  display: flex;
  flex: 1;
  gap: 1rem;
  padding: 1rem;
  overflow: hidden;
  min-height: 0;
}

/* Calendario con scroll propio y oculto */
.calendar {
  flex: 7;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 0.5rem;
  height: 100%;
  overflow-y: auto;
  scrollbar-width: none;
  /* Firefox */
}

.calendar::-webkit-scrollbar {
  display: none;
  /* Chrome, Safari */
}

/* Sidebar fijo sin scroll */
.sidebar {
  flex: 3;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 1rem;
  overflow: hidden;
  height: 100%;
}

.button-agendar {
  background-color: #12BACA;
  color: white;
  border: none;
}

.agenda-header {
  background: linear-gradient(135deg, #1E3C72, #2A5298);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}
</style>
