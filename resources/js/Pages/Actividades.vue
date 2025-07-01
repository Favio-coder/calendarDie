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
        <div v-if="diaSeleccionado">
          <h3>{{ diaSeleccionadoText }}</h3>
        </div>
        <div v-else>
          <h4>Selecciona una fecha en el calendario</h4>
        </div>

        <!-- Sección de boton -->
        <div style="display: flex; justify-content: center; align-items: center;">
          <button class="items-center button-agendar" @click="agregarEvento()">
            <i class="fas fa-plus"></i> Agendar actividad
          </button>
        </div>


        <div v-if="diaSeleccionado">
          <hr />
        </div>

        <div v-if="diaSeleccionado">
          <ul>
            <li v-for="event in eventsFordiaSeleccionado" :key="event.id">
              {{ event.title }} — {{ event.start.slice(0, 10) }}
              <button @click="removeEvent(event.id)" style="margin-left: 0.5rem; color: red;">Eliminar</button>
            </li>
            <li v-if="eventsFordiaSeleccionado.length === 0">No hay eventos para esta fecha</li>
          </ul>



        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" @cerrar-ModalAgendar="cerrarModalAgendar"></component>
  </div>
</template>

<script>
//Componentes Vue
import Header from '@/components/Header.vue'
import Prueba from './Prueba.vue'
import ModalAgendar from '../Components/ModalAgendar.vue'
import { nextTick } from 'vue'

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
      events: [
        {
          id: '1',
          title: 'Reunión inicial',
          start: new Date().toISOString().slice(0, 10)
        },
        {
          id: '2',
          title: 'Reunión inicial',
          start: new Date().toISOString().slice(0, 10)
        },
      ],
      diaSeleccionado: null,
      diaSeleccionadoText: null,
      newEventTitle: '',
      currentProps: null,
      currentView: null
    }
  },
  computed: {
    eventsFordiaSeleccionado() {
      if (!this.diaSeleccionado) return []
      this.diaSeleccionadoText = this.convertirFecha(this.diaSeleccionado)
      return this.events.filter(event => event.start.startsWith(this.diaSeleccionado))
    }
  },
  mounted() {
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
      console.log("Cerrar agendar")

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
      if (confirm(`¿Eliminar evento "${clickInfo.event.title}"?`)) {
        this.removeEvent(clickInfo.event.id)
      }
    },
    agregarEvento() {
      this.currentView = ModalAgendar
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal()
        }
      })
    },
    removeEvent(eventId) {
      this.events = this.events.filter(e => e.id !== eventId)
      const calendarEvent = this.calendar.getEventById(eventId)
      if (calendarEvent) calendarEvent.remove()
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
