<template>
    <div>
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-4 overflow-hidden sombra-moderna">
                    <!-- TÍTULO -->
                    <div class="modal-header titulo-agend-actividad">
                        <h5 class="modal-title text-white">
                            <i class="fa-solid fa-pen-to-square"></i> Crear/Editar Sesión
                        </h5>
                        <button type="button" class="btn-close boton-cerrar" @click="closeModal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <form @submit.prevent="guardarSesion">
                            <!-- Imagen -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">📸 Foto referencial de la sesión</label>
                                <input type="file" class="form-control" @change="handleImageUpload" accept="image/*" />

                                <div v-if="imagenPreview" class="text-center">
                                    <img :src="imagenPreview" class="img-fluid mt-3 rounded border mx-auto d-block"
                                        style="max-height: 250px;" />
                                </div>
                            </div>


                            <!-- Nombre -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">📌 Nombre de la sesión</label>
                                <input type="text" v-model="sesion.l_sesion" class="form-control" />
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">📝 Descripción</label>
                                <textarea v-model="sesion.l_descripcion" class="form-control" rows="3"></textarea>
                            </div>

                            <!-- Enlace de la sesión -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">🔗 Enlace de sesión</label>
                                <input type="url" v-model="sesion.l_linkSesion" class="form-control"
                                    placeholder="https://..." />
                            </div>

                            <!-- Enlace de la grabación -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">📹 Enlace de grabación</label>
                                <input type="url" v-model="sesion.l_linkGrabacion" class="form-control"
                                    placeholder="https://..." />
                            </div>

                            <!-- Recursos -->
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-2">📚 Recursos adjuntos</label>
                                <input type="file" class="form-control mb-3" @change="agregarRecurso" multiple />

                                <ul class="list-group mt-2">
                                    <li v-for="(recurso, idx) in recursosArchivos" :key="idx"
                                        class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fa-solid fa-paperclip me-2 text-secondary"></i> {{ recurso.name }}
                                        </span>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            @click="eliminarRecurso(idx)">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Botones -->
                            <div class="modal-footer px-0">
                                <button type="submit" class="btn btn-success">Guardar sesión</button>
                                <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                            </div>
                        </form>
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
    name: 'ModalCrearSesion',
    props: {
        codPrograma: {
            type: String,
            default: () => ({})
        }
    },
    data() {
        return {
            isModalOpen: false,
            sesion: {
                l_sesion: '',
                l_descripcion: '',
                l_linkSesion: '',
                l_linkGrabacion: '',
                imagen: null
            },
            imagenPreview: null,
            recursosArchivos: [] // Aquí van los archivos adjuntos
        }
    },
    methods: {
        openModal() {
            this.isModalOpen = true
            document.body.classList.add('modal-open')

            const copia = JSON.parse(JSON.stringify(this.sesionProp || this.sesion))
            this.sesion = copia

            // Inicializa recursosArchivos si ya existían
            if (!this.recursosArchivos.length && this.sesionProp?.recursosArchivos) {
                this.recursosArchivos = [...this.sesionProp.recursosArchivos]
            }
        },
        closeModal() {
            this.isModalOpen = false
            document.body.classList.remove('modal-open')
            this.$emit('close-modalSesion')
        },
        guardarSesion() {
            const formData = new FormData()

            // Agregar campos de texto
            formData.append('l_sesion', this.sesion.l_sesion)
            formData.append('l_descripcion', this.sesion.l_descripcion)
            formData.append('l_linkSesion', this.sesion.l_linkSesion)
            formData.append('l_linkGrabacion', this.sesion.l_linkGrabacion)
            formData.append('c_programa', parseInt(this.codPrograma));


            // Imagen principal
            if (this.sesion.imagen) {
                formData.append('imagen', this.sesion.imagen)
            }

            // Archivos adjuntos
            this.recursosArchivos.forEach((archivo, index) => {
                formData.append(`recursosArchivos[]`, archivo)
            })



            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas grabar esta sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, grabar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/grabSesion', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            Swal.fire({
                                title: '¡Sesión grabada!',
                                text: 'La sesión se ha registrado correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                this.closeModal(); 
                            });
                        })

                }
            });

        },
        agregarRecurso(event) {
            const archivos = Array.from(event.target.files)
            this.recursosArchivos.push(...archivos)
            event.target.value = '' // Limpia input file
        },
        eliminarRecurso(idx) {
            this.recursosArchivos.splice(idx, 1)
        },
        handleImageUpload(event) {
            const file = event.target.files[0]
            if (file) {
                this.sesion.imagen = file
                this.imagenPreview = URL.createObjectURL(file)
            }
        }
    }
}
</script>

<style scoped>
.titulo-agend-actividad {
    background: #12BACA;
}

.sombra-moderna {
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.boton-cerrar {
    filter: invert(1) brightness(2);
}

/* Responsive y ajustes visuales */
@media (max-width: 768px) {
    .modal-dialog {
        max-width: 95%;
        margin: auto;
    }

    .form-label {
        font-size: 0.9rem;
    }

    .modal-body {
        padding: 1rem;
    }
}
</style>
