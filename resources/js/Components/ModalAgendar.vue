<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog" @click.self="closeModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">Agendar Evento</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">

                <!-- Columna izquierda -->
                <div class="col-md-7">
                  <div class="mb-3">
                    <label>Nombre de la actividad:</label>
                    <input type="text" class="form-control" placeholder="Inserte el nombre de la actividad">
                  </div>

                  <div class="mb-3">
                    <label>Descripción de la actividad:</label>
                    <textarea class="form-control" placeholder="Describe la actividad"></textarea>
                  </div>

                  <div class="mb-3 d-flex align-items-center">
                    <label class="me-2">Responsable:</label>
                    <input type="text" class="form-control me-3" disabled v-model="usuario.l_nombre"
                      style="max-width: 250px;">
                    <label class="me-2">Hora:</label>
                    <input type="text" class="form-control" value="9:00 am" style="max-width: 120px;">
                  </div>

                  <!-- Invitaciones -->
                  <div class="mb-3">
                    <label>Invitaciones:</label>
                    <!-- <div class="scroll-vertical">
                      <div v-for="(inv, i) in invitaciones" :key="i" class="badge bg-secondary text-white d-block mb-1">
                        {{ inv }}</div>
                      <button class="btn btn-sm btn-outline-secondary mt-2"
                        @click="mostrarPopup('invitacion')">+</button>
                    </div> -->
                  </div>

                  <!-- Recursos y Etiquetas juntos -->
                  <div class="mb-3 d-flex gap-4">
                    <!-- Recursos -->
                    <div class="flex-fill">
                      <label>Recursos:</label>
                      <!-- <div class="scroll-vertical">
                        <div v-for="(rec, i) in recursos" :key="i" class="badge bg-info text-dark d-block mb-1">{{ rec
                          }}</div>
                        <button class="btn btn-sm btn-outline-secondary mt-2"
                          @click="mostrarPopup('recurso')">+</button>
                      </div> -->
                    </div>

                    <!-- Etiquetas -->
                    <div class="flex-fill">
                      <label>Etiquetas:</label>
                      <div class="scroll-vertical">
                        <div v-for="(etq, i) in etiquetas" :key="i" class="badge bg-success text-white d-block mb-1">{{
                          etq }}</div>
                        <button class="btn btn-sm btn-outline-secondary mt-2"
                          @click="mostrarPopup('etiqueta')">+</button>
                      </div>
                    </div>
                  </div>


                  <div class="mb-3">
                    <label>Recursos:</label>
                    <div class="scroll-hidden d-flex gap-2 mt-1">
                      <span v-for="(rec, i) in recursos" :key="i" class="badge bg-info text-dark">{{ rec }}</span>
                      <button class="btn btn-sm btn-outline-secondary" @click="mostrarPopup('recurso')">+</button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label>Etiquetas:</label>
                    <div class="scroll-hidden d-flex gap-2 mt-1">
                      <span v-for="(etq, i) in etiquetas" :key="i" class="badge bg-success text-white">{{ etq }}</span>
                      <button class="btn btn-sm btn-outline-secondary" @click="mostrarPopup('etiqueta')">+</button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label>Notas:</label>
                    <textarea class="form-control" placeholder="Escribe unas notas"></textarea>
                  </div>
                </div>

                <!-- Columna derecha -->
                <div class="col-md-5">
                  <div class="mb-3">
                    <label>Fotografía referencial del evento</label>
                    <div class="border rounded d-flex align-items-center justify-content-center"
                      style="height: 150px; background-color: #f4f4f4;">
                      Fotografía referencial del evento
                    </div>
                    <button class="btn btn-secondary btn-sm mt-2">
                      📎 Adjuntar imagen
                    </button>
                  </div>

                  <div class="mb-3">
                    <label>Adjuntos:</label>
                    <button class="btn btn-secondary btn-sm d-block">
                      📎 Adjuntar archivos
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-primary" @click="closeModal">Cerrar</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Popup de entrada de datos -->
    <div v-if="showInputPopup" class="popup-overlay">
      <div class="popup-box">
        <input type="text" v-model="nuevoItem" placeholder="Nuevo elemento" class="form-control mb-2">
        <button class="btn btn-sm btn-success me-2" @click="agregarItem">Agregar</button>
        <button class="btn btn-sm btn-secondary" @click="showInputPopup = false">Cancelar</button>
      </div>
    </div>
  </div>
</template>


<script>
import { mapGetters } from 'vuex';


export default {
  name: 'ModalAgendar',
  data() {
    return {
      isModalOpen: false,
      invitaciones: ['FA', 'OM', 'RL'],
      recursos: ['Impresora 3D', 'Camara'],
      etiquetas: ['Captación', 'Importante'],
      nuevoItem: '',
      tipoActual: '',
      showInputPopup: false,
      popupVisible: false,
    }
  },
  computed:{
    ...mapGetters(['usuario'])
  },
  methods: {
    mostrarPopup() {
      this.popupVisible = true;
    },
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('cerrar-ModalAgendar')

      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    mostrarPopup(tipo) {
      this.tipoActual = tipo;
      this.nuevoItem = '';
      this.showInputPopup = true;
    },
    agregarItem() {
      const item = this.nuevoItem.trim();
      if (item === '') return;
      if (this.tipoActual === 'invitacion') {
        this.invitaciones.push(item);
      } else if (this.tipoActual === 'recurso') {
        this.recursos.push(item);
      } else if (this.tipoActual === 'etiqueta') {
        this.etiquetas.push(item);
      }
      this.nuevoItem = '';
      this.showInputPopup = false;
    }
  }
}
</script>

<style scoped>
.modal {
  transition: opacity 0.15s linear;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1040;
}

.modal-open {
  overflow: hidden;
}

.titulo-agend-actividad {
  background-color: #12BACA;
}

.titulo-agend-actividad .btn-close {
  filter: invert(1) brightness(2);
}

/* Scroll horizontal sin mostrar barra */
.scroll-hidden {
  overflow-x: auto;
  white-space: nowrap;
  padding-bottom: 5px;
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scroll-hidden::-webkit-scrollbar {
  display: none;
}

/* Popup personalizado */
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1100;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
}

.popup-box {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 300px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}
</style>
