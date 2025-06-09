<template>
  <div>
    <button type="button" class="btn btn-primary" @click="openModal">
      Abrir Modal
    </button>

    <div
      class="modal fade"
      tabindex="-1"
      :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }"
      aria-modal="true"
      role="dialog"
      @click.self="closeModal"
    >
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
                    <input type="text" class="form-control me-3" disabled value="Gerardo Aylas Vilchez" style="max-width: 250px;">
                    <label class="me-2">Hora:</label>
                    <input type="text" class="form-control" value="9:00 am" style="max-width: 120px;">
                  </div>

                  <div class="mb-3">
                    <label>Invitaciones:</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                      <span class="badge rounded-circle text-white" style="background-color: orange;">FA</span>
                      <span class="badge rounded-circle text-white bg-primary">OM</span>
                      <span class="badge rounded-circle text-white bg-danger">RL</span>
                      <button class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label>Recursos:</label>
                    <div class="d-flex gap-2 mt-1">
                      <span class="badge bg-info text-dark">Impresora 3D</span>
                      <span class="badge bg-pink text-white" style="background-color: #f759c4;">Camara</span>
                      <button class="btn btn-sm btn-outline-secondary">+</button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label>Etiquetas:</label>
                    <div class="d-flex gap-2 mt-1">
                      <span class="badge bg-success text-white">Captación</span>
                      <span class="badge bg-danger text-white">Importante</span>
                      <button class="btn btn-sm btn-outline-secondary">+</button>
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
                    <div class="border rounded d-flex align-items-center justify-content-center" style="height: 150px; background-color: #f4f4f4;">
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

          <!-- Footer -->
          <div class="modal-footer justify-content-end">
            <button class="btn btn-primary" style="background-color: #8B8FE2; border-color: #8B8FE2;">
              📅 Agendar
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Fondo oscuro -->
    <div v-if="isModalOpen" class="modal-backdrop fade show" @click="closeModal"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isModalOpen: false,
    };
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
  },
};
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
  background-color: #8B8FE2;
}
.titulo-agend-actividad .btn-close {
  filter: invert(1) brightness(2);
}
</style>
