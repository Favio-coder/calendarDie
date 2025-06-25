<template>
  <div>
    <!-- Modal principal -->
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 overflow-hidden sombra-moderna">
          <!-- TÍTULO -->
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">
              <i class="fa-solid fa-chalkboard"></i> Sesión - {{ sesionProp.l_sesion }}
            </h5>
            <button type="button" class="btn-close boton-cerrar" @click="closeModal"></button>
          </div>

          <div class="modal-body p-4">
            <!-- CONTENIDO PRINCIPAL -->
            <div class="contenido-sesion">
              <!-- Descripción -->
              <div class="mb-4">
                <h6 class="fw-bold">📝 Descripción</h6>
                <p class="descripcion">
                  {{ sesionProp.l_descripcion || 'No se ha agregado una descripción para esta sesión.' }}
                </p>
              </div>

              <!-- Enlaces -->
              <div class="enlaces d-flex flex-column gap-3 mb-4">
                <div>
                  <a v-if="sesionProp.l_linkSesion" :href="sesionProp.l_linkSesion" target="_blank" class="link-icon">
                    <i class="fa-brands fa-zoom fa-lg text-primary me-2"></i> Unirse a la sesión
                  </a>
                  <span v-else class="text-muted">
                    <i class="fa-brands fa-zoom fa-lg me-2"></i> No hay enlace de Zoom aún.
                  </span>
                </div>

                <div>
                  <a v-if="sesionProp.l_linkGrabacion" :href="sesionProp.l_linkGrabacion" target="_blank" class="link-icon">
                    <i class="fa-solid fa-circle-play fa-lg text-success me-2"></i> Ver grabación
                  </a>
                  <span v-else class="text-muted">
                    <i class="fa-solid fa-circle-play fa-lg me-2"></i> No hay grabación aún.
                  </span>
                </div>
              </div>

              <!-- Recursos -->
              <div class="recursos">
                <h6 class="fw-bold mb-2">📚 Recursos adjuntos</h6>
                <ul v-if="sesionProp.recursosSesion && sesionProp.recursosSesion.length">
                  <li v-for="(recurso, idx) in sesionProp.recursosSesion" :key="idx">
                    <i class="fa-solid fa-paperclip me-2 text-dark"></i>
                    <a :href="recurso.url" target="_blank">{{ recurso.nombre }}</a>
                  </li>
                </ul>
                <p v-else class="text-muted">Aún no se han adjuntado recursos.</p>
              </div>
            </div>
          </div>

          <!-- CIERRE -->
          <div class="modal-footer bg-light border-top">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModalSesion',
  props: {
    sesionProp: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      isModalOpen: false
    }
  },
  methods: {
    openModal() {
      this.isModalOpen = true
      document.body.classList.add('modal-open')
    },
    closeModal() {
      this.isModalOpen = false
      document.body.classList.remove('modal-open')
      this.$emit('close-modalSesion')
    }
  }
}
</script>

<style scoped>
.titulo-agend-actividad {
  background: #12BACA;
}

.descripcion {
  font-size: 1rem;
  color: #333;
}

.link-icon {
  text-decoration: none;
  font-weight: 500;
  color: #212529;
  transition: color 0.2s ease;
}

.link-icon:hover {
  color: #0a58ca;
}

.recursos ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.recursos li {
  margin-bottom: 8px;
}

.modal-dialog {
  max-width: 900px;
}

.sombra-moderna {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.boton-cerrar {
  filter: invert(1) brightness(2);
}

@media (max-width: 768px) {
  .descripcion {
    font-size: 0.95rem;
  }
}
</style>
