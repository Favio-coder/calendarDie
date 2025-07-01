<template>
  <div>
    <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
      :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-content">
          <div class="modal-header titulo-agend-actividad">
            <h5 class="modal-title text-white">Asignar Programas</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <div class="modal-body">
            <div v-if="isLoading" class="text-center my-4">
              <div class="loader-dots">
                <span class="dot dot-blue"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-blue"></span>
              </div>
              <p class="mt-2 mb-0">Cargando módulo...</p>
            </div>

            <div v-else class="row text-center justify-content-center gy-4">
              <div class="col-12 col-md-6">
                <button class="icon-card" @click="handleEstudiantes">
                  <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="#12BACA"
                      d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 9.75L4.21 7 12 4.25 19.79 7 12 11.75zM6 17v2h12v-2c0-1.66-3.58-2.5-6-2.5S6 15.34 6 17z" />
                  </svg>
                  <span class="btn-label">Estudiantes</span>
                </button>
              </div>
              <div class="col-12 col-md-6">
                <button class="icon-card" @click="handleProfesores">
                  <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="#12BACA"
                      d="M12 2C10.34 2 9 3.34 9 5s1.34 3 3 3 3-1.34 3-3S13.66 2 12 2zm6 8h-1.26C15.9 9.28 14.03 8.5 12 8.5s-3.9.78-4.74 1.5H6c-1.1 0-2 .9-2 2v2h2v7h12v-7h2v-2c0-1.1-.9-2-2-2z" />
                  </svg>
                  <span class="btn-label">Mentores</span>
                </button>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-danger" @click="closeModal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <component :is="currentView" ref="modalRef" v-bind="currentProps" @close-modalAsignProgramEst="closeModalAsign" />
  </div>
</template>

<script>
import axios from 'axios';
import ModalAgregarEquipo from './ModalAgregarEquipo.vue';
import ModalAsignProgramDet from './ModalAsignProgramDet.vue';

import { nextTick } from 'vue';
import { mapGetters } from 'vuex';

export default {
  name: 'ModalAsignProgram',
  components: {
    ModalAgregarEquipo, ModalAsignProgramDet
  },
  data() {
    return {
      isModalOpen: false,
      isLoading: false,
      currentView: null,
      currentProps: null,
      mentoresMatriculados: [],
      estudiantesMatriculados: []
    };
  },
  props: {
    usuarios:{
       type: Array,
       required:true
    }
  },
  mounted(){
    axios.post('/detMatriculaProgram', this.usuario).then(
      response => {
        this.estudiantesMatriculados = response.data.Estudiantes
        this.mentoresMatriculados = response.data.Mentores
      }
    )
    
  },
  computed: {
    ...mapGetters(['usuario'])
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },
    closeModal() {
      this.$emit('close-modalAsignProgram');
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },
    handleEstudiantes() {
      const estudiantes = this.usuarios.filter(u => u.c_rol === '3')
      this.currentView = ModalAsignProgramDet
      this.currentProps = { 
        tipoMod: 'estudiante' ,
        usuarios: estudiantes,
        inscritos: this.estudiantesMatriculados
      }
      nextTick(() => {
        this.$refs.modalRef?.openModal?.();
      });
    },
    handleProfesores() {
      const mentores = this.usuarios.filter(u => u.c_rol === '2')
      this.currentView = ModalAsignProgramDet
      this.currentProps = { 
        tipoMod: 'profesor' ,
        usuarios: mentores,
        inscritos: this.mentoresMatriculados
      };
      nextTick(() => {
        this.$refs.modalRef?.openModal?.();
      });
    },
    closeModalAsign() {
      this.currentView = null
      this.currentProps = null
    }
  }
};
</script>

<style scoped>
.titulo-agend-actividad {
  background-color: #12BACA;
}

.btn-close {
  filter: invert(1) brightness(2);
}

.loader-dots {
  display: flex;
  justify-content: center;
  gap: 10px;
  align-items: center;
}

.dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  animation: bounce 0.6s infinite alternate;
}

.dot-blue {
  background-color: #12BACA;
}

.dot-yellow {
  background-color: #FFD700;
  animation-delay: 0.2s;
}

@keyframes bounce {
  from {
    transform: translateY(0);
  }

  to {
    transform: translateY(-10px);
  }
}

/* Icon Card Buttons */
.icon-card {
  background: #ffffff;
  border: 2px solid #12BACA;
  border-radius: 16px;
  padding: 2rem;
  width: 100%;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.icon-card:hover {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 8px 20px rgba(18, 186, 202, 0.3);
  background-color: #f0fbfd;
}

.svg-icon {
  width: 64px;
  height: 64px;
  margin-bottom: 10px;
  transition: transform 0.3s ease;
}

.icon-card:hover .svg-icon {
  transform: scale(1.1) rotate(3deg);
}

.btn-label {
  display: block;
  font-size: 1.2rem;
  font-weight: 600;
  color: #333;
}
</style>
