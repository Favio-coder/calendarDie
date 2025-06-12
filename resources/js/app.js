import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.css';
import '@fortawesome/fontawesome-free/js/all.js';
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';

import Vue from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue';
import draggable from './directives/draggable';
import store from './store/store'

Vue.use(plugin);
Vue.directive('draggable', draggable);

const el = document.getElementById('app');

new Vue({
  store, 
  render: h =>
    h(App, {
      props: {
        initialPage: JSON.parse(el.dataset.page),
        resolveComponent: name =>
          import(`@/Pages/${name}.vue`).then(module => module.default).catch(() =>
            import(`@/Components/${name}.vue`).then(module => module.default)
          ),
      },
    }),
}).$mount(el);
