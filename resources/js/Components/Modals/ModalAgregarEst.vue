<template>
    <div>
        <!-- Modal principal -->
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog" style="max-width: 900px;">
                <div class="modal-content modal-sombra">
                    <div class="modal-header titulo-agend-actividad">
                        <h5 class="modal-title text-white">Estudiantes</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <h5>Filtros:</h5>
                        <div class="row mb-3 align-items-end">
                            <!-- Facultad -->
                            <div class="col-md-6">
                                <label class="form-label">Facultad:</label>
                                <select class="form-select" v-model="carreraSeleccionada">
                                    <option disabled selected>Seleccionar facultad</option>
                                    <option v-for="carrera in carreras" :key="carrera.c_carrera"
                                        :value="carrera.c_carrera">
                                        {{ carrera.l_carrera }}
                                    </option>
                                </select>
                            </div>

                            <!-- Búsqueda -->
                            <div class="col-md-6 mt-2 mt-md-0">
                                <label class="form-label">Buscar estudiante:</label>
                                <input type="text" class="form-control" v-model="busqueda"
                                    placeholder="🔍 Nombre o apellido">
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Código</th>
                                        <th>Carrera</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="est in estudiantesFiltrados" :key="est.c_estudiante">
                                        <td>{{ est.l_nombre }}</td>
                                        <td>{{ est.l_apellido }}</td>
                                        <td>{{ est.c_estudiante }}</td>
                                        <td>{{ est.l_carrera }}</td>

                                        <td>
                                            <button @click="agregarEstxEquipo(est)" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <component :is="currentView" ref="modalRef" v-bind="currentProps">
        </component>
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {
    name: 'ModalAgregarEst',
    props: {
        estudiantesProp: Array,
        carrerasProp: Array
    },
    data() {
        return {
            isModalOpen: false,
            estudiantes: [],
            carreras: [],
            carreraSeleccionada: null,
            busqueda: '',
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
        ...mapGetters(['usuario']),

        estudiantesFiltrados() {
            let lista = this.estudiantes;

            if (this.carreraSeleccionada) {
                lista = lista.filter(est => est.c_carrera === this.carreraSeleccionada);
            }

            if (this.busqueda.trim()) {
                const texto = this.busqueda.trim().toLowerCase();
                lista = lista.filter(est =>
                    est.l_nombre.toLowerCase().includes(texto) ||
                    est.l_apellido.toLowerCase().includes(texto)
                );
            }

            return lista;
        }
    },
    mounted() {
        console.log("Se monta el componente nuevamente!!")
        this.estudiantes = this.estudiantesProp
        this.carreras = this.carrerasProp

        // axios.get('/obtenerEstudiantesXcarrera')
        //     .then(response => {
        //         this.estudiantes = response.data.estudiantes
        //         // Extraer c_carrera y l_carrera únicos
        //         const carrerasUnicas = []
        //         const mapaCarreras = new Map()

        //         response.data.estudiantes.forEach(est => {
        //             if (!mapaCarreras.has(est.c_carrera)) {
        //                 mapaCarreras.set(est.c_carrera, est.l_carrera)
        //                 carrerasUnicas.push({
        //                     c_carrera: est.c_carrera,
        //                     l_carrera: est.l_carrera
        //                 });
        //             }
        //         });

        //         this.carreras = carrerasUnicas;
        //     })
        //     .catch(err => {
        //         console.error("Existe este error: ", err);
        //     });
    },
    methods: {
        openModal() {
            this.isModalOpen = true;
            document.body.classList.add('modal-open');
        },
        closeModal() {
            this.$emit('close-modalEquipo');
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        },
        // Logica para agregar estudiante en el equipo 
        agregarEstxEquipo(est) {
            console.log(this.estudiantes)

            console.log("Este estudiante se va agregar al equipo ", est)
            this.$emit('agregar-estudiante', est)
            this.estudiantes = this.estudiantes.filter(e => e.c_usuario !== est.c_usuario)
        },
        // agregarEstxTotal(est) {
        //     console.log("Se devuelve la pelota!!!")
        //     //this.estudiantes.push(est)
        // },
        recibirEstudianteDevuelto(est) {
            // Verificamos que no esté ya en la lista para evitar duplicados
            const yaExiste = this.estudiantes.some(e => e.c_usuario === est.c_usuario)
            if (!yaExiste) {
                this.estudiantes.push(est)
            }
        }
    }
}
</script>

<style scoped>
.titulo-agend-actividad {
    background-color: #12BACA;
}

.modal-sombra {
    box-shadow: -10px 0 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.table th,
.table td {
    vertical-align: middle;
}

.titulo-agend-actividad .btn-close {
    filter: invert(1) brightness(2);
}
</style>
