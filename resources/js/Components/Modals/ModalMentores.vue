<template>
    <div>
        <!-- Modal de Mentores -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-sombra">
                    <div class="modal-header titulo-agend-actividad text-white">
                        <h5 class="modal-title">Mentores del Ecosistema</h5>
                        <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
                    </div>

                    <div class="modal-body body-scroll">
                        <!-- Loading -->
                        <div v-if="isLoading" class="text-center mb-3">
                            <div class="loader-dots">
                                <span class="dot dot-blue"></span>
                                <span class="dot dot-yellow"></span>
                                <span class="dot dot-blue"></span>
                            </div>
                            <p class="mt-2 mb-0">Cargando mentores...</p>
                        </div>

                        <!-- Lista de mentores -->
                        <div class="row g-4" v-else>
                            <div v-for="mentor in mentores" :key="mentor.id" class="col-12 col-md-6">
                                <div class="d-flex align-items-center border rounded shadow-sm p-3 h-100 mentor-card">
                                    <img :src="mentor.l_fotoPerfil || defaultImage" alt="foto mentor"
                                        class="rounded-circle me-3 mentor-foto" />

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ mentor.l_nombre + ' ' + mentor.l_apellido }}</h6>
                                        <p class="mb-0 text-muted small">{{ mentor.l_descripcion }}</p>
                                    </div>

                                    <a v-if="mentor.l_linkedin" :href="mentor.l_linkedin" target="_blank"
                                        class="btn btn-link text-primary ms-3">
                                        <i class="fa-brands fa-linkedin fa-lg"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ModalMentores',
    data() {
        return {
            isModalOpen: false,
            mentores: [],
            isLoading: false,
            defaultImage: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
        };
    },
    mounted() {
        this.cargarMentores()
    },
    methods: {
        openModal() {
            this.isModalOpen = true;
            document.body.classList.add('modal-open');
            this.cargarMentores();
        },
        closeModal() {
            this.$emit('cerrar-modalMentor');
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        },
        cargarMentores() {

            this.isLoading = true;
            axios.get('/listMentores')
                .then(response => {
                    this.mentores = response.data.mentores;
                })
                .catch(error => {
                    console.error("Error al cargar mentores:", error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
};
</script>

<style scoped>
.modal-sombra {
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.btn-close-white {
    filter: invert(1) brightness(2);
}

.titulo-agend-actividad {
    background-color: #12BACA;
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

.mentor-foto {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border: 2px solid #ddd;
}

.mentor-card:hover {
    background-color: #f9f9f9;
}

.body-scroll {
  max-height: 60vh; /* o ajusta según prefieras */
  overflow-y: auto;
  padding-right: 12px;
}

/* Opcional: oculta scrollbar en WebKit */
.body-scroll::-webkit-scrollbar {
  width: 8px;
}
.body-scroll::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}

</style>