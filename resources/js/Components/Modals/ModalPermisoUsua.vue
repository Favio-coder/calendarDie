<!-- ModalGestionPermisos.vue -->
<template>
    <div>
        <!-- ▾ MODAL PRINCIPAL ------------------------------------------------ -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog"
            @click.self="closeModal">

            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-sombra">
                    <!-- HEADER ----------------------------------------------------- -->
                    <div class="modal-header titulo-permisos text-white">
                        <h5 class="modal-title">
                            Gestión de Permisos – {{ mentorOficialProp.l_nombre }} {{ mentorOficialProp.l_apellido }}
                        </h5>
                        <button class="btn-close btn-close-white" @click="closeModal" />
                    </div>

                    <!-- BODY ------------------------------------------------------- -->
                    <div class="modal-body">
                        <!-- Loader -->
                        <div v-if="isLoading" class="text-center my-4">
                            <div class="loader-dots">
                                <span class="dot dot-blue"></span><span class="dot dot-yellow"></span><span
                                    class="dot dot-blue"></span>
                            </div>
                            <p class="mt-2 mb-0">Cargando permisos…</p>
                        </div>

                        <!-- Tabla de permisos -->
                        <div v-else class="tabla-scroll">
                            <table class="table table-sm table-hover align-middle text-center">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width:70%">Permiso</th>
                                        <th style="width:30%">Activo</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="perm in permisos" :key="perm.c_permiso"
                                        @click="descripcionActual = perm.l_descripcion">
                                        <td>{{ perm.l_descripcion }}</td>
                                        <td>
                                            <!-- interruptor Bootstrap -->
                                            <input class="form-check-input" type="checkbox" v-model="perm.q_activo"
                                                :true-value="1" :false-value="0">

                                        </td>
                                    </tr>

                                    <tr v-if="permisos.length === 0">
                                        <td colspan="2">No hay permisos</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FOOTER ------------------------------------------------------ -->
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="guardarCambios">Guardar</button>
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
    name: 'ModalGestionPermisos',
    props: { mentorOficialProp: { type: Object, required: true } },

    data() {
        return {
            isModalOpen: false,
            isLoading: false,
            permisos: [],      // [{ id_permiso, l_permiso, l_descripcion, q_asignado }]
            descripcionActual: ''
        }
    },

    computed: {
        // ids que quedaron activos
        idsAsignados() {
            return this.permisos.filter(p => p.q_asignado).map(p => p.id_permiso)
        }
    },

    mounted() { this.cargarPermisos() },

    methods: {
        openModal() {
            this.isModalOpen = true
            document.body.classList.add('modal-open')
        },
        closeModal() {
            this.$emit('cerrar-modalPermisos')
            this.isModalOpen = false
            document.body.classList.remove('modal-open')
        },

        cargarPermisos() {
            this.isLoading = true
            try {
                axios.post('/listPermisos', this.mentorOficialProp).then(
                    resp => {
                        this.permisos = resp.data.permisos
                    }
                )

            } catch (e) { console.error(e) }
            finally { this.isLoading = false }
        },

        guardarCambios() {
            const datosEnviar = {
                c_usuario: this.mentorOficialProp.c_usuario,
                permisos: this.permisos.map(p => ({
                    c_permiso: p.c_permiso,
                    q_activo: p.q_activo
                }))
            };

            axios.post('/grabPermisos', datosEnviar)
                .then(() => {
                    Swal.fire('Editado correctamente', 'Se agrego o elimino los permisos del usuario', 'success');
                    this.closeModal();
                })
               
        }

    }
}
</script>

<style scoped>
.modal-sombra {
    box-shadow: -10px 0 30px rgba(0, 0, 0, .4), 0 0 20px rgba(0, 0, 0, .2);
    border-radius: 12px
}

.titulo-permisos {
    background: #12BACA
}

.tabla-scroll {
    max-height: 280px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px
}

.btn-close-white {
    filter: invert(1) brightness(2)
}

.loader-dots {
    display: flex;
    justify-content: center;
    gap: 10px
}

.dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    animation: bounce .6s infinite alternate
}

.dot-blue {
    background: #12BACA
}

.dot-yellow {
    background: #FFD700;
    animation-delay: .2s
}

@keyframes bounce {
    to {
        transform: translateY(-10px)
    }
}
</style>
