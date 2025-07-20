<template>
    <div>
        <!-- Modal de Recursos -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-sombra">
                    <div class="modal-header titulo-agend-actividad text-white">
                        <h5 class="modal-title">Administración de programas</h5>
                        <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Loading animación -->
                        <div v-if="isLoading" class="text-center mb-3">
                            <div class="loader-dots">
                                <span class="dot dot-blue"></span>
                                <span class="dot dot-yellow"></span>
                                <span class="dot dot-blue"></span>
                            </div>
                            <p class="mt-2 mb-0">Cargando programas...</p>
                        </div>

                        <!-- Tabla con scroll -->
                        <div class="tabla-scroll" v-else>
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-light sticky-top bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre del programa</th>
                                        <th>Descripción</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="programas.length === 0">
                                        <tr>
                                            <td colspan="4">No hay programas registrados.</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-for="(p, index) in programas" :key="p.c_programa"
                                            @dblclick="editarPrograma(p)">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ p.l_programa }}</td>
                                            <td>{{ p.l_descripcion }}</td>
                                            <td>
                                                <button @click="elimPrograma(p)" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                        <button class="btn btn-primary" @click="agregarPrograma">
                            <i class="fa-solid fa-plus me-1"></i> Agregar programa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal hijo -->
        <component :is="currentView" v-bind="currentProps" ref="modalRef"
            @cerrar-modalAgregarPrograma="cerrarModalAgregarPrograma" />
    </div>
</template>

<script>
import { nextTick } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { mapGetters } from 'vuex';
import ModalAdminProgramaDet from './ModalAdminProgramaDet.vue';

export default {
    name: 'ModalAdminProgramas',
    components: { ModalAdminProgramaDet },
    data() {
        return {
            isModalOpen: false,
            currentView: null,
            currentProps: null,
            isLoading: false
        };
    },
    computed: {
        ...mapGetters(['programas']),


    },
    mounted() {

    },
    methods: {
        cerrarModalAgregarPrograma() {
            this.currentProps = null;
            this.currentView = null;
        },
        openModal() {
            this.isModalOpen = true;
            document.body.classList.add('modal-open');
        },
        closeModal() {
            this.$emit('cerrar-modalAdminProgram');
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        },
        elimPrograma(programa) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Se eliminará este programa permanentemente.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (result.isConfirmed) {
                    axios.post('/elimPrograma', programa)
                        .then(response => {
                            this.$store.dispatch('eliminarPrograma', programa.c_programa)

                            Swal.fire({
                                icon: 'success',
                                title: 'Programa eliminado',
                                text: "Se elimino correctamente el programa",
                                confirmButtonText: 'Aceptar'
                            });
                        })
                        .catch(error => {
                            console.error(error)
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al eliminar',
                                text: 'Ocurrió un error eliminando el programa.',
                                confirmButtonText: 'Aceptar'
                            });
                        });
                }
            });
        },
        agregarPrograma() {
            this.currentView = ModalAdminProgramaDet
            nextTick(() => {
                if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
                    this.$refs.modalRef.openModal();
                }
            });
        },
        editarPrograma(programa) {
            this.currentView = ModalAdminProgramaDet
            this.currentProps = {
                accion: 'editarPrograma',
                programaProp: programa
            }
            nextTick(() => {
                if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
                    this.$refs.modalRef.openModal();
                }
            });

        }
        // abrirModalEditar(recurso) {
        //   this.currentView = ModalAgregarRecurso;
        //   this.currentProps = {
        //     accion: 'editar',
        //     recursoProp: recurso
        //   };
        //   nextTick(() => {
        //     if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
        //       this.$refs.modalRef.openModal();
        //     }
        //   });
        // },
        // abrirModalAgregar() {
        //   this.currentView = ModalAgregarRecurso;
        //   this.currentProps = {
        //     accion: 'insertar'
        //   };
        //   nextTick(() => {
        //     if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
        //       this.$refs.modalRef.openModal();
        //     }
        //   });
        // }
    }
};
</script>

<style scoped>
.modal-sombra {
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    vertical-align: middle;
}

.btn-close-white {
    filter: invert(1) brightness(2);
}

.titulo-agend-actividad {
    background-color: #12BACA;
}

.tabla-scroll {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 2;
}

/* Loader bolitas personalizadas */
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
</style>
