<template>
  <div>
    <Header />
    <div class="p-5 bg-light">
      <h2 class="mb-4 fw-bold text-dark fs-3">🌟 Recursos del Ecosistema</h2>

      <div class="row g-4">
        <div v-for="recurso in recursos" :key="recurso.titulo" class="col-6 col-md-4">
          <div class="p-4 text-white text-center rounded-4 shadow-lg recurso-card h-100"
            :style="{ background: recurso.color }" @click="recurso.titulo === 'Equipos' && abrirModalEquipo()">
            <div class="emoji-icon mb-2">{{ recurso.emoji }}</div>
            <h5 class="fw-semibold">{{ recurso.titulo }}</h5>
          </div>
        </div>

      </div>
    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalEquipo="cerrarModalEquipo"></component>
  </div>
</template>

<script>
import Header from '@/components/Header.vue'
import axios from 'axios'
import { nextTick } from 'vue'
import ModalEquipo from '../Components/Modals/ModalEquipo.vue'

export default {
  components: { Header, ModalEquipo },
  data() {
    return {
      currentProps: null,
      currentView: null,
      recursos: [
        { titulo: "Red de Aliados", emoji: "🤝", color: "linear-gradient(135deg, #7F00FF, #E100FF)" },
        { titulo: "Convenios", emoji: "📜", color: "linear-gradient(135deg, #28a745, #74d680)" },
        { titulo: "Red de Mentores", emoji: "🧠", color: "linear-gradient(135deg, #007bff, #00c6ff)" },
        { titulo: "Red de Inversiones", emoji: "💸", color: "linear-gradient(135deg, #dc3545, #ff758c)" },
        { titulo: "Equipos", emoji: "👥", color: "linear-gradient(135deg, #ffc107, #ffdd59)" },
        { titulo: "Recursos Físicos", emoji: "🏢", color: "linear-gradient(135deg, #fd7e14, #ff9f43)" },
      ]
    }
  },
  methods: {
    abrirModalEquipo() {
      this.currentView = ModalEquipo;
      nextTick(() => {
        if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
          this.$refs.modalRef.openModal();
        }
      });
    },
    cerrarModalEquipo() {
      this.currentProps = null
      this.currentView = null
    }
  }
}
</script>

<style scoped>
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
