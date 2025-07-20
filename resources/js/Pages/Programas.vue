<template>
  <div>
    <Header />

    <!-- Encabezado llamativo -->
    <div class="text-center py-5 header-programa text-white">
      <h2 class="display-5 fw-bold mb-1">🌟 Nuestros Programas</h2>
      <p class="lead mb-0">Iniciativas que impulsan tu emprendimiento al siguiente nivel</p>
    </div>

    <div class="container my-5">
      <!-- Tarjetas generadas dinámicamente -->
      <div v-if="programas.length != 0">
        <div @click="abrirModalPrograma(programa)" v-for="(programa, index) in programas" :key="programa.c_programa"
          class="program-card mb-4" :class="getColorClass(index)" @mouseover="hover = programa.c_programa"
          @mouseleave="hover = ''">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-white">{{ programa.l_programa }}</h5>
            <i :class="getIconClass(index)" class="fa-lg text-white"></i>
          </div>
          <transition name="fade">
            <p v-if="hover === programa.c_programa" class="descripcion mt-2 text-white">
              {{ programa.l_descripcion }}
            </p>
          </transition>
        </div>
      </div>
      <div v-else>
        <div class="text-center py-5 header-alerta text-white">
          <h2 v-if="usuario.c_rol === '1' " class="display-5 fw-bold mb-1">No tienes permiso para ver ningún programa☹️</h2>
          <h2 v-else  class="display-5 fw-bold mb-1">No fuiste asignado a ningún programa ☹️</h2>
          <p class="lead mb-0">Comunicate con el administrador</p>
        </div>
      </div>

    </div>
    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalPrograma="closeModalPrograma">
    </component>
  </div>
</template>

<script>
import Header from '@/components/Header.vue'
import axios from 'axios'
import ModalPrograma from '../Components/Modals/ModalPrograma.vue'
import { nextTick } from 'vue'
import { mapGetters } from 'vuex'


export default {
  components: { Header, ModalPrograma },
  data() {
    return {
      hover: '',
      programas: [],
      currentView: null,
      currentProps: null,
      permisosEst: []
    }
  },
  computed: {
    ...mapGetters(['usuario']),

  },
  mounted() {
    if (this.usuario.c_rol !== '1') {
      axios.post('/listPermisos', this.usuario).then(response => {
        this.permisosEst = response.data.permisos;


        axios.get('/listProgramas').then(response => {
          const todosLosProgramas = response.data.programas;

          const programasPermitidos = todosLosProgramas.filter(programa => {
            return this.permisosEst.some(permiso =>
              permiso.c_programa === programa.c_programa &&
              permiso.l_modulo === 'programa' &&
              permiso.l_tipo === 'visualizar' &&
              permiso.q_activo === '1'
            );
          });

          this.programas = programasPermitidos;

        });

      });
    } else {
      // Administrador: ve todos los programas
      axios.post('/devPermiso', {
        c_usuario: this.usuario.c_usuario,
        modulo: 'programas',
        tipo: 'visualizar',
        descripcion: 'No visualizar ningún programa'
      }).then(
        r => {
          if (!r.data.permiso) {
            //Si tiene permiso solo podra ver los programar
            axios.get('/listProgramas').then(response => {
              this.programas = response.data.programas;
            })
          }
        }
      )



    }

  },
  methods: {
    getColorClass(index) {
      const colors = ['card-azul', 'card-verde', 'card-rosa', 'card-naranja', 'card-purpura']
      return colors[index % colors.length]
    },
    getIconClass(index) {
      const icons = [
        'fa-solid fa-chalkboard-user',
        'fa-solid fa-lightbulb',
        'fa-solid fa-seedling',
        'fa-solid fa-rocket',
        'fa-solid fa-users-gear'
      ]
      return icons[index % icons.length]
    },
    abrirModalPrograma(programa) {

      this.currentView = ModalPrograma
      this.currentProps = {
        programaProp: programa
      }
      nextTick(() => {
        if (this.$refs.modalRef?.openModal) {
          this.$refs.modalRef.openModal()
        }
      })
    },
    closeModalPrograma() {
      this.currentProps = null
      this.currentView = null
    }
  }
}
</script>

<style scoped>
/* ENCABEZADO */
.header-programa {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.header-alerta {
  background: linear-gradient(135deg, #8861b1, #557abb);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

/* TARJETAS DE PROGRAMAS */
.program-card {
  border-radius: 14px;
  padding: 20px;
  color: white;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.program-card:hover {
  transform: scale(1.02);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
}

.descripcion {
  font-size: 0.95rem;
}

/* COLORES POR PROGRAMA */
.card-azul {
  background: linear-gradient(135deg, #007bff, #00c6ff);
}

.card-verde {
  background: linear-gradient(135deg, #28a745, #74d680);
}

.card-rosa {
  background: linear-gradient(135deg, #dc3545, #ff758c);
}

.card-naranja {
  background: linear-gradient(135deg, #fd7e14, #ffc078);
}

.card-purpura {
  background: linear-gradient(135deg, #6f42c1, #d6b2ff);
}

/* ANIMACIÓN */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
