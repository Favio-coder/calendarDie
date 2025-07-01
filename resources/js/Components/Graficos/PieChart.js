// PieChart.js
import { Pie } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js';

// Registrar elementos necesarios
ChartJS.register(Title, Tooltip, Legend, ArcElement);

export default {
  name: 'PieChart',
  props: {
    chartData: {
      type: Object,
      required: true
    },
    chartOptions: {
      type: Object,
      default: () => ({ responsive: true, maintainAspectRatio: false })
    }
  },
  render(h) {
    return h(Pie, {
      props: {
        chartData: this.chartData,
        chartOptions: this.chartOptions
      }
    });
  }
};
