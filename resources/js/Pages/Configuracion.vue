<template>
  <div class="main-container">
    <Header />

    <div class="content">
      <div class="config-container">
        <h2 class="title">Cuentas creadas</h2>

        <!-- Tabla con scroll -->
        <div class="table-wrapper">
          <table class="user-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Rol</th>
              </tr>
            </thead>
            <tbody>
              <tr @dblclick="abrirModalEditCuenta(usuario)" v-for="(usuario, index) in usuarios" :key="index">
                <td>{{ usuario.l_nombre }}</td>
                <td>{{ usuario.l_apellido }}</td>
                <td>{{ usuario.l_correoElectronico }}</td>
                <td>{{ usuario.c_rol }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Botón centrado debajo -->
        <div class="btn-container">
          <button class="crear-cuenta-btn" @click="abrirModalCrearCuenta()">
            <i class="fa fa-plus"></i> Crear usuario
          </button>
        </div>
      </div>
    </div>

    <component :is="currentView" ref="modalRef" @close-modalCrearCuenta="cerrarModalCrearCuenta" v-bind="currentProps"></component>
  </div>
</template>

<script>
import Header from '@/components/Header.vue';
import ModalCrearCuenta from '@/Components/ModalCrearCuenta.vue';
import { nextTick } from 'vue';
import { mapGetters } from 'vuex';
import ModalCrearCuentaConf from '../Components/ModalCrearCuentaConf.vue';
import axios from 'axios';

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
    };
  },
  computed: {
    ...mapGetters(['usuario'])
  },
  mounted() {
    axios.post('/listUsuarios', this.usuario).then(
      response => {
        this.usuarios = response.data.usuarios

        console.log("Esto se monta: ", response.data.usuarios)
      }
    )
  
  },
  methods: {
    abrirModalEditCuenta(_usuario) {
      console.log(typeof _usuario)

      this.currentView = ModalCrearCuentaConf
      this.currentProps = { usuarioProp: _usuario }
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      })
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
      this.currentProps = null
      this.currentView = null
    }
  },
};
</script>

<style scoped>
.main-container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.content {
  flex: 1;
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: start;
}

.config-container {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 400px;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  width: 100%;
  max-width: 700px;
}

.title {
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: #333;
  text-align: center;
}

.table-wrapper {
  overflow-y: auto;
  max-height: 300px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th,
.user-table td {
  padding: 0.75rem;
  border-bottom: 1px solid #eee;
  text-align: left;
}

.user-table th {
  background-color: #f9f9f9;
  font-weight: bold;
}

.btn-container {
  display: flex;
  justify-content: center;
}

.crear-cuenta-btn {
  background-color: #12BACA;
  color: white;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.crear-cuenta-btn:hover {
  background-color: #0fa3b1;
}
</style>
