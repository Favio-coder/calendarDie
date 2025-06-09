<template>
    <div>
        <!-- Modal principal -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header titulo-agend-actividad">
                        <h5 class="modal-title text-white">Editar cuenta</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-container d-flex flex-column flex-md-row gap-4">
                            <!-- Foto de perfil -->
                            <div class="profile-pic-section d-flex flex-column align-items-center text-center">
                                <label for="fotoPerfil" class="profile-label">
                                    <img :src="preview || defaultImage" class="profile-img mb-2" />
                                    <div class="btn btn-secondary btn-sm">Adjuntar foto</div>
                                </label>
                                <input type="file" id="fotoPerfil" class="d-none" @change="handleFileUpload" />
                            </div>

                            <!-- Formulario -->
                            <div class="flex-grow-1 w-100">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre(s)</label>
                                        <input type="text" class="form-control" v-model="form.nombre" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Apellidos</label>
                                        <input type="text" class="form-control" v-model="form.apellido" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="email" class="form-control" v-model="form.correo" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de nacimiento</label>
                                        <input type="date" class="form-control" v-model="form.fechaNacimiento" />
                                    </div>
                                </div>


                                <div class="mb-1">
                                    <button class="btn btn-success w-10" @click="abrirModalPassword">
                                        <i class="fa-solid fa-lock"></i>
                                        Cambiar contraseña
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                        <button class="btn btn-crear-cuenta" @click="grabNuevaCuenta()">Crear cuenta</button>
                    </div>
                </div>
            </div>
        </div>
        <component :is="currentView" ref="modalRef" v-bind="currentProps" @enviar-contrasena="recibirContrasena"
            @close-modalCrearContrasena="handleCerrarModal">
        </component>
    </div>
</template>


<script>
import Swal from 'sweetalert2';
import ModalCrearContrasena from './ModalCrearContrasena.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex'

export default {
    name: 'ModalEditarCuenta',
    components: {
        ModalCrearContrasena
    },
    data() {
        return {
            isModalOpen: false,
            facultades: [],
            form: {
                nombre: '',
                apellido: '',
                correo: '',
                fechaNacimiento: '',
                rol: '',
                codigoEstudiante: '',
                facultad: '',
                carrera: '',
                descripcion: '',
                linkedin: '',
                contrasena: ''
            },
            preview: null,
            defaultImage: 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
            currentView: null,
            currentProps: null
        }
    },
    computed: {
        ...mapGetters(['usuario']), //Llamar al store el Usuario

        carrerasFiltradas() {
            const seleccionada = this.facultades.find(f => f.codigo_facultad == this.form.facultad)
            return seleccionada ? seleccionada.carreras : []
        },

    },
    mounted() {


    },
    methods: {
        handleCerrarModal() {
            this.currentView = null
            this.currentProps = null
        },
        openModal() {
            this.form = {
                nombre: this.usuario.l_nombre,
                apellido: this.usuario.l_apellido,
                correo: this.usuario.l_correoElectronico,
                fechaNacimiento: this.usuario.f_nacimiento,
                rol: this.usuario.c_rol,
                // codigoEstudiante: '',
                // facultad: '',
                // carrera: '',
                // descripcion: '',
                // linkedin: ''
            }
            this.isModalOpen = true;
            document.body.classList.add('modal-open');
        },
        closeModal() {
            this.$emit('close-modalEditarCuenta')

            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => this.preview = e.target.result;
                reader.readAsDataURL(file);
            }
        },
        grabNuevaCuenta() {
            Swal.fire({
                title: "Mensaje",
                text: "Se creara una cuenta con estas credenciales, ¿estas seguro?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Crear cuenta",
                cancelButtonText: "Cancelar"
            })
        },
        abrirModalPassword() {
            this.currentView = ModalCrearContrasena
            this.currentProps = {
                tipo: 'cambiarContra'
            }
            nextTick(() => {
                if (this.$refs.modalRef && this.$refs.modalRef.openModal) {
                    this.$refs.modalRef.openModal();
                }
            });
        },
        recibirContrasena(datos) {
            this.form.contrasena = datos.contrasena
            console.log("Recibido desde el hijo:", datos)
        }

    }
}
</script>

<style scoped>
.titulo-agend-actividad {
    background-color: #12BACA;
}

.profile-pic-section {
    min-width: 200px;
    max-width: 200px;
}

.profile-label {
    cursor: pointer;
}

.profile-img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ccc;
    transition: 0.3s;
}

.profile-img:hover {
    opacity: 0.8;
}

.btn-crear-cuenta {
    background-color: #12BACA;
    color: white;
}

.titulo-agend-actividad .btn-close {
    filter: invert(1) brightness(2);
}
</style>
