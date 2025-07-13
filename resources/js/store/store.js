// resources/js/store.js
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// Intentar cargar desde localStorage
const usuarioGuardado = JSON.parse(localStorage.getItem('usuario'));
const programasGuardados = JSON.parse(localStorage.getItem('programas')) || [];
const equiposGuardados = JSON.parse(localStorage.getItem('equipos')) || [];

export default new Vuex.Store({
  state: {
    usuario: usuarioGuardado || null,
    programas: programasGuardados,
    equipos: equiposGuardados
  },
  mutations: {
    setUsuario(state, payload) {
      state.usuario = payload;
      localStorage.setItem('usuario', JSON.stringify(payload));
    },
    limpiarUsuario(state) {
      state.usuario = null;
      localStorage.removeItem('usuario');
    },
    setProgramas(state, payload) {
      state.programas = payload;
      localStorage.setItem('programas', JSON.stringify(payload));
    },
    setEquipos(state, payload) {
      state.equipos = payload;
      localStorage.setItem('equipos', JSON.stringify(payload));
    },
    quitarPrograma(state, idPrograma) {
      state.programas = state.programas.filter(p => p.c_programa !== idPrograma)
      localStorage.setItem('programas', JSON.stringify(state.programas))
    },
    quitarEquipo(state, idEquipo) {
      state.equipos = state.equipos.filter(e => e.c_equipo !== idEquipo)
      localStorage.setItem('equipos', JSON.stringify(state.equipos))
    },
    actualizarPrograma(state, programaActualizado) {
      const index = state.programas.findIndex(p => p.c_programa === programaActualizado.c_programa);
      if (index !== -1) {
        Vue.set(state.programas, index, programaActualizado);
        localStorage.setItem('programas', JSON.stringify(state.programas));
      }
    }
  },
  actions: {
    guardarUsuario({ commit }, datos) {
      commit('setUsuario', datos);
    },
    cerrarSesion({ commit }) {
      commit('limpiarUsuario');
      localStorage.removeItem('programas');
      localStorage.removeItem('equipos');
    },
    guardarProgramas({ commit }, programas) {
      commit('setProgramas', programas);
    },
    guardarEquipos({ commit }, equipos) {
      commit('setEquipos', equipos);
    },
    eliminarPrograma({ commit }, idPrograma) {
      commit('quitarPrograma', idPrograma)
    },
    eliminarEquipo({ commit }, idEquipo) {
      commit('quitarEquipo', idEquipo)
    },
    actualizarPrograma({ commit }, programaActualizado) {
      commit('actualizarPrograma', programaActualizado);
    }
  },
  getters: {
    usuario: (state) => state.usuario,
    q_autenticado: (state) => !!state.usuario,
    programas: (state) => state.programas,
    equipos: (state) => state.equipos
  },
});
