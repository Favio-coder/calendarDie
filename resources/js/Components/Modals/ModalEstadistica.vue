<template>
    <div>
        <div class="modal fade" tabindex="-1" :class="{ show: isModalOpen }"
            :style="{ display: isModalOpen ? 'block' : 'none' }" aria-modal="true" role="dialog">
            <div class="modal-dialog" style="max-width: 1300px;">
                <div class="modal-content">
                    <div class="modal-header titulo-agend-actividad">
                        <h5 class="modal-title text-white">Estadísticas</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">


                        <div v-if="isLoading" class="text-center my-4">
                            <div class="loader-dots">
                                <span class="dot dot-blue"></span>
                                <span class="dot dot-yellow"></span>
                                <span class="dot dot-blue"></span>
                            </div>
                            <p class="mt-2 mb-0">Cargando estadísticas...</p>
                        </div>

                        <div class="scroll-contenedor" v-else>
                            <div v-if="chartData">
                                <PieChart :chartData="chartData" :chartOptions="chartOptions" style="height: 300px;" />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" @click="closeModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <component :is="currentView" ref="modalRef" v-bind="currentProps" />
    </div>
</template>

<script>
import Swal from 'sweetalert2';
import ModalAgregarEquipo from './ModalAgregarEquipo.vue';
import { nextTick } from 'vue';
import axios from 'axios';
import { mapGetters } from 'vuex';
import PieChart from '../Graficos/PieChart';

export default {
    name: 'ModalEstadistica',
    components: {
        PieChart
    },
    data() {
        return {
            isModalOpen: false,
            estadisticas: [],
            isLoading: false,
            chartData: null,
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            },
            currentView: null,
            currentProps: null
        };
    },
    computed: {
        ...mapGetters(['usuario'])
    },
    mounted() {
        this.generarListEquipo();
    },
    methods: {
        generarListEquipo() {
            this.isLoading = true;
            axios.get('/genEstadisticas').then(response => {
                this.estadisticas = response.data.estadisticas;
                this.setChartData();
                this.isLoading = false;
            });
        },
        setChartData() {
            const labels = this.estadisticas.map(item => item.NombreCarrera);
            const data = this.estadisticas.map(item => parseInt(item.CantidadEstudiantes));
            this.chartData = {
                labels,
                datasets: [
                    {
                        label: 'Cantidad de Estudiantes',
                        data,
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ]
                    }
                ]
            };
        },
        openModal() {
            this.isModalOpen = true;
            document.body.classList.add('modal-open');
        },
        closeModal() {
            this.$emit('close-modalEstadistica');
            this.isModalOpen = false;
            document.body.classList.remove('modal-open');
        }
    }
};
</script>

<style scoped>
.titulo-agend-actividad {
    background-color: #12BACA;
}

.scroll-contenedor {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 5px;
}

.scroll-contenedor::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}

.scroll-contenedor {
    scrollbar-width: none;
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

.btn-close {
    filter: invert(1) brightness(2);
}
</style>
