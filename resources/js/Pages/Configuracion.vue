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
                <th>Nombre de usuario</th>
                <th>Correo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(usuario, index) in usuarios" :key="index">
                <td>{{ usuario.nombre }}</td>
                <td>{{ usuario.correo }}</td>
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

    <component :is="currentView" ref="modalRef" @close-modalCrearCuenta="cerrarModalCrearCuenta"></component>
  </div>
</template>

<script>
import Header from '@/components/Header.vue';
import ModalCrearCuenta from '@/Components/ModalCrearCuenta.vue';
import { nextTick } from 'vue';
import { mapGetters } from 'vuex';

export default {
  components: {
    Header,
    ModalCrearCuenta,
  },
  data() {
    return {
      currentProps: null,
      currentView: null,
      usuarios: [
        { nombre: 'Juan Pérez', correo: 'juan@example.com' },
        { nombre: 'Ana Torres', correo: 'ana@example.com' },
        { nombre: 'Luis Gómez', correo: 'luis@example.com' },
        { nombre: 'Marta Silva', correo: 'marta@example.com' },
        { nombre: 'Juan Pérez', correo: 'juan@example.com' },
        { nombre: 'Ana Torres', correo: 'ana@example.com' },
        { nombre: 'Luis Gómez', correo: 'luis@example.com' },
        { nombre: 'Marta Silva', correo: 'marta@example.com' },
        { nombre: 'Juan Pérez', correo: 'juan@example.com' },
        { nombre: 'Ana Torres', correo: 'ana@example.com' },
        { nombre: 'Luis Gómez', correo: 'luis@example.com' },
        { nombre: 'Marta Silva', correo: 'marta@example.com' },
        { nombre: 'Juan Pérez', correo: 'juan@example.com' },
        { nombre: 'Ana Torres', correo: 'ana@example.com' },
        { nombre: 'Luis Gómez', correo: 'luis@example.com' },
        { nombre: 'Marta Silva', correo: 'marta@example.com' },
        // Puedes agregar más datos aquí o cargarlos dinámicamente
      ],
    };
  },
  computed:{
    ...mapGetters(['usuario'])
  },
  mounted(){
    if(this.usuario.c_rol !== '1'){
      alert("No tienes autorizado entrar a esta pagina!!!")
      window.location.href = '/inicio'
      return
    }
  },
  methods: {
    abrirModalCrearCuenta() {
      this.currentView = ModalCrearCuenta;
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarModalCrearCuenta(){
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
