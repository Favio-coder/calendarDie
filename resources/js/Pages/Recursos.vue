<template>
  <div>
    <Header />

    <!-- Encabezado llamativo -->
    <div class="text-center py-5 header-recursos text-white">
      <h2 class="display-5 fw-bold mb-1">📚 Recursos del Ecosistema</h2>
      <p class="lead mb-0">Accede a herramientas, espacios y redes que impulsan tu crecimiento</p>
    </div>

    <div class="p-5 bg-light">
      <div class="row g-4">
        <div v-for="recurso in recursos" :key="recurso.titulo" class="col-6 col-md-4">
          <div class="p-4 text-white text-center rounded-4 shadow-lg recurso-card h-100"
            :style="{ background: recurso.color }" @click="manejarClickRecurso(recurso.titulo)">
            <div class="emoji-icon mb-2">{{ recurso.emoji }}</div>
            <h5 class="fw-semibold">{{ recurso.titulo }}</h5>
          </div>
        </div>
      </div>
    </div>

    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalEquipo="cerrarModalEquipo"
      @cerrar-modalRecursos="cerrarModalRecursos" @cerrar-modalMentor="cerrarModalMentor" />
  </div>
</template>

<script>
import Header from '@/components/Header.vue'
import axios from 'axios'
import { nextTick } from 'vue'
import ModalEquipo from '../Components/Modals/ModalEquipo.vue'
import ModalRecursos from '../Components/Modals/ModalRecursos.vue'
import ModalMentores from '../Components/Modals/ModalMentores.vue'
import { mapGetters } from 'vuex'
import Swal from 'sweetalert2'

export default {
  components: { Header, ModalEquipo, ModalRecursos, ModalMentores },
  data() {
    return {
      currentProps: null,
      currentView: null,
      recursos: [
        { titulo: "Red de Mentores", emoji: "🧠", color: "linear-gradient(135deg, #007bff, #00c6ff)" },
        { titulo: "Equipos", emoji: "👥", color: "linear-gradient(135deg, #ffc107, #ffdd59)" },
        { titulo: "Recursos Físicos", emoji: "🏢", color: "linear-gradient(135deg, #fd7e14, #ff9f43)" },
      ],
      q_permitirEquipo: false,
      q_permitirRecursos: false
    }
  },
  mounted() {
    this.$watch(
      () => this.usuario?.c_usuario,
      (id) => { if (id) this.cargarPermisos(); },
      { immediate: true }
    );

  },
  computed:{
    ...mapGetters(['usuario'])
  },
  methods: {
    manejarClickRecurso(titulo) {
      if (titulo === 'Equipos') {
        this.abrirModalEquipo()
      } else if (titulo === 'Recursos Físicos') {
        this.abrirModalRecursos()
      } else if (titulo === 'Red de Mentores') {
        this.abrirModalMentores()
      }
    },
    abrirModalMentores() {
      this.currentView = ModalMentores;
      nextTick(() => {
        if (this.$refs.modalRef?.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    abrirModalEquipo() {
      if (this.q_permitirEquipo) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }


      this.currentView = ModalEquipo;
      nextTick(() => {
        if (this.$refs.modalRef?.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    abrirModalRecursos() {
      if (this.q_permitirRecursos) {
        Swal.fire({
          title: 'Sin permisos',
          text: '¡No puedes realizar esta acción!',
          icon: 'warning',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      this.currentView = ModalRecursos;
      nextTick(() => {
        if (this.$refs.modalRef?.openModal) {
          this.$refs.modalRef.openModal()
        }
      })
    },
    cerrarModalEquipo() {
      this.currentProps = null
      this.currentView = null
    },
    cerrarModalRecursos() {
      this.currentProps = null
      this.currentView = null
    },
    cerrarModalMentor() {
      this.currentProps = null
      this.currentView = null
    },
    cargarPermisos() {
      const specs = [
        { key: 'q_permitirEquipo', modulo: 'recursos', tipo: 'visualizar', desc: 'No visualizar el módulo de equipos' },
        { key: 'q_permitirRecursos', modulo: 'recursos', tipo: 'visualizar', desc: 'No visualizar el módulo de recursos' }
      ];

      Promise.all(
        specs.map(sp =>
          axios.post('/devPermiso', {
            c_usuario: this.usuario.c_usuario,
            modulo: sp.modulo,
            tipo: sp.tipo,
            descripcion: sp.desc
          }).then(r => ({ key: sp.key, valor: r.data.permiso }))
        )
      )
        .then(res => res.forEach(({ key, valor }) => { this[key] = valor; }))
        .catch(console.error);
    }
  }
}
</script>

<style scoped>
/* ENCABEZADO ESTILO BANNER */
.header-recursos {
  background: linear-gradient(135deg, #1D2671, #C33764);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

/* TARJETAS DE RECURSOS */
.recurso-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.recurso-card:hover {
  transform: scale(1.05);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.emoji-icon {
  font-size: 3rem;
  transition: transform 0.3s ease;
}

.recurso-card:hover .emoji-icon {
  transform: rotate(10deg) scale(1.1);
}
</style>
