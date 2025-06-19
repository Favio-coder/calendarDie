<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg px-4">
    <div class="d-flex align-items-center w-100 justify-content-between">
      <!-- Logo -->
      <div class="d-flex align-items-center me-4" style="width: 120px;" @click="irInicio">
        <div style="width: 50px; height: 50px;">
          <img src="/logoincubacentro.webp" style="width: 130px; height: 50px;" alt="">
        </div>
      </div>

      <!-- Ítems de menú -->
      <div class="d-flex align-items-center">
        <a class="nav-link mx-4 custom-hover" href="/inicio">Inicio</a>
        <a v-if="!q_autorizacion" class="nav-link mx-4 custom-hover" href="/actividades">Actividades</a>
        <a  class="nav-link mx-4 custom-hover" href="#">Programas</a>
        <a v-if="!q_autorizacion" class="nav-link mx-4 custom-hover" href="/recursos">Recursos</a>
        <a class="nav-link mx-4 custom-hover" href="#">Concursos</a>
        <a v-if="!q_autorizacion" class="nav-link mx-4 custom-hover" href="/configuracion">Configuración</a>
      </div>

      <!-- Notificaciones, nombre y foto -->
      <div class="d-flex align-items-center position-relative">
        <!-- Panel de notificaciones -->
        <div v-if="mostrarPanel" class="notification-panel shadow rounded">
          <h4 class="mb-3">Notificaciones</h4>
          <div v-if="notificaciones.length > 0">
            <div v-for="(noti, index) in notificaciones" :key="index" class="noti-item">
              {{ noti }}
            </div>
          </div>
          <div v-else class="text-center text-muted py-2">Sin notificaciones</div>
        </div>

        <!-- Botón notificaciones -->
        <button type="button" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip"
          data-bs-title="Notificaciones" class="btn btn-link position-relative me-3" @click="mostrareNotificaciones"
          ref="btnNoti">
          <i class="fas fa-bell custom-icon"></i>
        </button>

        <!-- Nombre del usuario -->
        <span class="mx-3 custom-hover" style="cursor: pointer;">{{ usuario?.l_nombre }} {{ usuario?.l_apellido
        }}</span>

        <!-- Imagen o ícono de perfil -->
        <div @click="togglePerfil" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="bottom"
          data-bs-custom-class="custom-tooltip" data-bs-title="Perfil" ref="perfilTooltip">
          <img v-if="usuario.l_fotoPerfil" :src="fotoPerfilURL" alt="Foto de perfil" class="rounded-circle"
            style="width: 40px; height: 40px;" />
          <i v-else class="fas fa-user-circle fa-3x text-secondary"></i>
        </div>

        <!-- Panel de perfil -->
        <div v-if="mostrarPerfil" class="perfil-panel shadow rounded">
          <div class="perfil-opcion" @click="editarPerfil">Editar cuenta</div>
          <div class="perfil-opcion" @click="cerrarSesion">Cerrar sesión</div>
        </div>
      </div>
    </div>
    <component :is="currentView" ref="modalRef" @close-modalEditarCuenta="cerrarModalEditCuenta"></component>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'
import '@fortawesome/fontawesome-free/css/all.css'
import ModalEditarCuenta from './ModalEditarCuenta.vue'
import ModalCrearCuentaConf from './ModalCrearCuentaConf.vue'

import { nextTick } from 'vue'

export default {
  name: 'Header',
  components: {
    ModalEditarCuenta,
  },
  data() {
    return {
      mostrarPanel: false,
      mostrarPerfil: false,
      imgPerfil: '',
      notificaciones: [],
      currentView: null,
      currentProps: null,
      q_autorizacion: false
    }
  },
  computed: {
    ...mapGetters(['usuario']),

    fotoPerfilURL() {
            // Si ya tiene foto, retornarla como URL completa, si no usar imagen por defecto
            return this.usuario.l_fotoPerfil
            ? `http://localhost:8000/${this.usuario.l_fotoPerfil}`
            : this.defaultImage;
        }
  },
  mounted() {
    if (this.usuario.c_rol !== '1') {
      this.q_autorizacion = true
    }

    const tooltipTrigger = this.$refs.btnNoti
    if (tooltipTrigger) new bootstrap.Tooltip(tooltipTrigger)

    const tooltipPerfil = this.$refs.perfilTooltip
    if (tooltipPerfil) new bootstrap.Tooltip(tooltipPerfil)

    // Cierra panel si se hace clic fuera
    document.addEventListener('click', this.cerrarPanelesExternos)
  },


  beforeDestroy() {
    document.removeEventListener('click', this.cerrarPanelesExternos)
  },



  methods: {
    mostrareNotificaciones() {
      this.mostrarPanel = !this.mostrarPanel
      this.mostrarPerfil = false
    },
    togglePerfil() {
      this.mostrarPerfil = !this.mostrarPerfil
      this.mostrarPanel = false
    },
    editarPerfil() {
      this.mostrarPerfil = false
      this.currentView = ModalEditarCuenta
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarSesion() {
      window.location.href = '/acceso'
      localStorage.removeItem('usuario')
      this.$store.commit('setUsuario', null)
      //window.location.href = '/acceso'
    },
    irInicio() {
      window.location.href = '/inicio'
    },
    cerrarPanelesExternos(event) {
      const noti = this.$refs.btnNoti
      const perfil = this.$refs.perfilTooltip
      if (!this.$el.contains(event.target)) {
        this.mostrarPanel = false
        this.mostrarPerfil = false
      }
    },
    cerrarModalEditCuenta() {
      this.currentView = null
      this.currentProps = null
    }
  }
}
</script>

<style scoped>
.custom-hover {
  position: relative;
  color: inherit;
  transition: color 0.3s ease;
}

.custom-hover:hover {
  color: #12BACA !important;
}

.custom-hover::after {
  content: '';
  position: absolute;
  left: 50%;
  bottom: 0;
  transform: translateX(-50%) scaleX(0);
  width: 100%;
  height: 2px;
  background-color: #12BACA;
  transition: transform 0.3s ease;
  transform-origin: center;
}

.custom-hover:hover::after {
  transform: translateX(-50%) scaleX(1);
}

.custom-icon {
  font-size: 2rem;
  color: #12BACA;
}

.notification-panel {
  position: absolute;
  top: 100%;
  right: 60px;
  transform: translateX(-100%);
  background: white;
  width: 300px;
  max-height: 300px;
  overflow-y: auto;
  z-index: 1000;
  padding: 15px;
  border-radius: 0.5rem;
}

.noti-item {
  padding: 8px;
  border-bottom: 1px solid #ddd;
  font-size: 0.9rem;
}

.noti-item:last-child {
  border-bottom: none;
}

.perfil-panel {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  width: 200px;
  border-radius: 0.5rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  padding: 10px 0;
}

.perfil-opcion {
  padding: 10px 20px;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background 0.2s;
}

.perfil-opcion:hover {
  background-color: #f5f5f5;
}
</style>
