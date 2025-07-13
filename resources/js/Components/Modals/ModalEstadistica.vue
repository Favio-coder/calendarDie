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
                        <button class="btn btn-success" @click="exportToExcel">📊 Exportar a Excel</button>
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
import * as XLSX from 'xlsx';
import ExcelJS from 'exceljs';

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
                            '#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6',
                            '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
                            '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
                            '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
                            '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
                            '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
                            '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
                            '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933'
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
        },
        async exportToExcel() {
            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('Estadísticas');

            ws.mergeCells('A1:B1');
            ws.getCell('A1').value = 'Estadística de Estudiantes por Carrera';
            ws.getCell('A1').font = { bold: true, size: 14 };
            ws.getCell('A1').alignment = { horizontal: 'center' };

            ws.getRow(3).values = ['Carrera', 'Cantidad de Estudiantes'];
            ws.getRow(3).eachCell(cell => {
                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF1F497D' }   
                };
                cell.alignment = { horizontal: 'center' };
                cell.border = {
                    top: { style: 'thin' },
                    left: { style: 'thin' },
                    bottom: { style: 'thin' },
                    right: { style: 'thin' }
                };
            });


            const dataRows = this.estadisticas.map(item => [
                item.NombreCarrera,
                item.CantidadEstudiantes
            ]);
            ws.addRows(dataRows);

            const startRow = 4;
            const endRow = startRow + dataRows.length - 1;
            for (let r = startRow; r <= endRow; r++) {
                ws.getRow(r).eachCell(cell => {
                    cell.border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                    };
                    if (cell.col === 2) cell.alignment = { horizontal: 'center' };
                });
            }

            ws.columns = [
                { key: 'carrera', width: 40 },
                { key: 'cantidad', width: 20 }
            ];

            const buffer = await wb.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'EstadisticasEstudiantes.xlsx';
            link.click();
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
