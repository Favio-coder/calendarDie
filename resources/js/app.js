import './bootstrap';

import Vue from 'vue'
import { App, plugin } from '@inertiajs/inertia-vue'

Vue.use(plugin)

const el = document.getElementById('app')

new Vue({
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
}).$mount(el)

