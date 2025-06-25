<template>
  <div class="main-container">
    <Header />
    <div class="usuarios-header text-white text-center py-5">
      <h2 class="display-5 fw-bold mb-1">🔐 Administración de Usuarios</h2>
      <p class="lead">Gestiona las cuentas del ecosistema y sus permisos</p>
    </div>

    <div class="content container-fluid py-4">
      <div class="row g-4">

        <!-- 🧍 Columna izquierda: Lista de usuarios -->
        <div class="col-12 col-lg-6">
          <div class="filtro-container mb-3">
            <input type="text" class="form-control" v-model="filtroNombre" placeholder="Buscar por nombre..." />
            <select class="form-select mt-2" v-model="filtroRol">
              <option value="">Todos los roles</option>
              <option value="2">Mentor invitado</option>
              <option value="3">Estudiante</option>
            </select>
          </div>


          <div class="table-wrapper">
            <button @click="abrirModalCrearCuenta" class="btn btn-primary mb-2">
              <i class="fa-solid fa-plus"></i> Agregar usuario
            </button>

            <!-- Loader mientras carga -->
            <div v-if="cargandoUsuarios" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>

            <!-- Tabla -->
            <table v-else class="user-table">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Correo</th>
                  <th>Rol</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(usuario, index) in usuariosFiltrados" :key="index"
                  @dblclick="abrirModalEditCuenta(usuario)">
                  <td><img :src="usuario.l_fotoPerfil || defaultFoto" alt="Foto perfil" class="avatar" /></td>
                  <td>{{ usuario.l_nombre }}</td>
                  <td>{{ usuario.l_apellido }}</td>
                  <td>{{ usuario.l_correoElectronico }}</td>
                  <td>{{ usuario.c_rol == 2 ? 'Mentor invitado' : 'Estudiante' }}</td>
                  <td>
                    <button class="btn btn-sm btn-danger" @click="eliminarUsuario(usuario)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>


        </div>

        <!-- 🧩 Columna derecha: Acciones -->
        <div class="col-12 col-lg-6 d-flex flex-column gap-4">
          <div class="action-box" @click="accion('permisos')">
            🛡️
            <span>Permisos</span>
          </div>
          <div class="action-box" @click="accion('asignar_programa')">
            🧩
            <span>Asignar programa</span>
          </div>
          <div class="action-box" @click="accion('estadisticas')">
            📊
            <span>Estadísticas</span>
          </div>
        </div>

      </div>
    </div>

    <component :is="currentView" ref="modalRef" @close-modalCrearCuenta="cerrarModalCrearCuenta"
      v-bind="currentProps" />
  </div>
</template>

<script>
import Header from '@/components/Header.vue';
import ModalCrearCuenta from '@/Components/ModalCrearCuenta.vue';
import ModalCrearCuentaConf from '../Components/ModalCrearCuentaConf.vue';
import axios from 'axios';
import { mapGetters } from 'vuex';
import { nextTick } from 'vue';

export default {
  components: {
    Header,
    ModalCrearCuenta,
    ModalCrearCuentaConf
  },
  data() {
    return {
      currentProps: null,
      currentView: null,
      usuarios: [],
      filtroNombre: '',
      filtroRol: '',
      defaultFoto: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
    };
  },
  computed: {
    ...mapGetters(['usuario']),
    usuariosFiltrados() {
      return this.usuarios.filter(u => {
        const coincideNombre = `${u.l_nombre} ${u.l_apellido}`.toLowerCase().includes(this.filtroNombre.toLowerCase());
        const coincideRol = this.filtroRol === '' || String(u.c_rol) === this.filtroRol;
        return coincideNombre && coincideRol;
      });
    }
  },
  mounted() {
    axios.post('/listUsuarios', this.usuario).then(response => {
      this.usuarios = response.data.usuarios;
    });
  },
  methods: {
    abrirModalEditCuenta(_usuario) {
      this.currentView = ModalCrearCuentaConf;
      this.currentProps = { usuarioProp: _usuario };
      nextTick(() => {
        if (this.$refs.modalRef?.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    abrirModalCrearCuenta() {
      this.currentView = ModalCrearCuenta;
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarModalCrearCuenta() {
      axios.post('/listUsuarios', this.usuario).then(response => {
        this.usuarios = response.data.usuarios;
      });
      this.currentProps = null;
      this.currentView = null;
    },
    accion(tipo) {
      alert(`Has hecho clic en: ${tipo}`);
    },
    eliminarUsuario(usuario){
      alert(usuario)
    }
  }
};
</script>

<style scoped>
.main-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  /*overflow: hidden;
  /* ✅ Desactiva scroll vertical de toda la página */
}

.col-lg-6 {
  display: flex;
  flex-direction: column;
  height: 100%;
  /* 👈 Asegura que la tabla pueda usar el espacio */
}

.usuarios-header {
  background: linear-gradient(135deg, #4B79A1, #283E51);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.content {
  flex: 1;
  padding: 2rem;
  /*overflow: hidden;
  /*overflow: hidden;
  /* 👌 también evita que el contenido general cause scroll */
}

.filtro-container input,
.filtro-container select {
  width: 100%;
  padding: 0.5rem;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.filtro-container select {
  margin-top: 0.5rem;
}

.table-wrapper {
  overflow-y: auto;
  max-height: 300px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

/* Tabla */
.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th,
.user-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
  text-align: left;
  vertical-align: middle;
}

.user-table th {
  background-color: #f9f9f9;
  font-weight: bold;
  position: sticky;
  top: 0;
  z-index: 1;
}

/* Avatar */
.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

/* Acciones */
.action-box {
  background: #f0f8ff;
  padding: 1.2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  font-size: 1.2rem;
  text-align: center;
  transition: all 0.3s ease;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border: 2px solid transparent;
}

.action-box:hover {
  background-color: #12BACA;
  color: white;
  transform: scale(1.05);
  border-color: #0fa3b1;
}

/* Responsive */
@media (max-width: 768px) {
  .content {
    padding: 1rem;
  }

  .action-box {
    padding: 1rem;
    font-size: 1rem;
  }

  .user-table th,
  .user-table td {
    font-size: 0.9rem;
  }
}
</style>
