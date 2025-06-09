// resources/js/store.js
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Intentar cargar usuario desde localStorage
const usuarioGuardado = JSON.parse(localStorage.getItem('usuario'));

export default new Vuex.Store({
  state: {
    usuario: usuarioGuardado || null,
  },
  mutations: {
    setUsuario(state, payload) {
      state.usuario = payload;
      localStorage.setItem('usuario', JSON.stringify(payload))
    },
    limpiarUsuario(state) {
      state.usuario = null;
      localStorage.removeItem('usuario')
    },
  },
  actions: {
    guardarUsuario({ commit }, datos) {
      commit('setUsuario', datos);
    },
    cerrarSesion({ commit }) {
      commit('limpiarUsuario');
    },
  },
  getters: {
    usuario: (state) => state.usuario,
    q_autenticado: (state) => !!state.usuario,
  },
});
