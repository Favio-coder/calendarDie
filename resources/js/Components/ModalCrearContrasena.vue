<template>
    <div>
        <!-- Modal principal -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div v-if="tipo === 'crearContra' " class="modal-header titulo-crear-contra">
                        <h5 class="modal-title text-white">Crear contraseña</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div v-else class="modal-header titulo-crear-contra">
                        <h5 class="modal-title text-white">Cambiar contraseña</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input :type="mostrarContrasena ? 'text' : 'password'" class="form-control"
                                    v-model="contrasena" autocomplete="new-password">
                                <span class="input-group-text toggle-password" @click="mostrarContra(1)">
                                    <svg v-if="mostrarContrasena" xmlns="http://www.w3.org/2000/svg" class="icon"
                                        viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M17.94 17.94A10.08 10.08 0 0112 20c-5.05 0-9.27-3.14-11-8 1.03-2.79 2.96-5.03 5.4-6.38" />
                                        <path d="M1 1l22 22" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24"
                                        width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Repetir contraseña</label>
                            <div class="input-group">
                                <input :type="mostrarContrasenaRepetir ? 'text' : 'password'" class="form-control"
                                    v-model="repetirContrasena" autocomplete="new-password">
                                <span class="input-group-text toggle-password" @click="mostrarContra(2)">
                                    <svg v-if="mostrarContrasenaRepetir" xmlns="http://www.w3.org/2000/svg" class="icon"
                                        viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M17.94 17.94A10.08 10.08 0 0112 20c-5.05 0-9.27-3.14-11-8 1.03-2.79 2.96-5.03 5.4-6.38" />
                                        <path d="M1 1l22 22" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24"
                                        width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                        <button class="btn btn-success" @click="grabarContrasena">Guardar contraseña</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
    name: 'ModalCrearContrasena',
    props: {
        tipo: {
            type: String,
            required: true, 
            default: ''
        }
    },
    data() {
        return {
            isModalOpen: false,
            //Datos para enviar
            contrasena: '',
            repetirContrasena: '',
            //Condicional
            mostrarContrasena: false,
            mostrarContrasenaRepetir: false

        };
    },
    mounted(){
        console.log("Eso se recibe: ", this.tipo)
    },  
    methods: {
        openModal() {
            //Se resetea los campos
            this.contrasena = ''
            this.repetirContrasena = ''
            this.mostrarContrasena = false
            this.mostrarContrasenaRepetir = false

            this.isModalOpen = true;
            document.body.classList.add('modal-open');
        },
        closeModal() {
            this.$emit('close-modalCrearContrasena')
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        },
        grabarContrasena() {
            if (this.contrasena !== this.repetirContrasena) {
                Swal.fire({
                    title: "Error",
                    text: "Las contraseñas no coinciden",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Cerrar",
                })
                return;
            }

            const contrasenaDatos = {
                contrasena: this.contrasena,
                repetirContrasena: this.repetirContrasena
            }

            this.$emit('enviar-contrasena', contrasenaDatos)

            this.closeModal();
        },
        mostrarContra(tipo) {
            if (tipo === 1) {
                this.mostrarContrasena = !this.mostrarContrasena;
            } else {
                this.mostrarContrasenaRepetir = !this.mostrarContrasenaRepetir;
            }
        }
    }
};
</script>

<style scoped>
.titulo-crear-contra {
    background-color: #12BACA;
}

.titulo-crear-contra .btn-close {
    filter: invert(1) brightness(2);
}

.modal-content {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.toggle-password {
    cursor: pointer;
    color: #555;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
}

.icon {
    width: 20px;
    height: 20px;
}

.input-group-text {
    background-color: #f0f0f0;
    border-left: none;
}

.input-group .form-control {
    border-right: none;
}
</style>
