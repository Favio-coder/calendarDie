<template>
  <div
    style="min-height: 100vh; background-color: #cdf3fa; display: flex; justify-content: center; align-items: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div
      style="background: white; padding: 2rem; border-radius: 12px; width: 350px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); text-align: center;">

      <!-- Logo -->
      <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1rem;">
        <img src="/logoincubacentro.webp" alt="Logo" style="width: 150px; height: auto;" />
      </div>

      <h2 style="margin-bottom: 1.5rem; font-weight: 600; color: #333;">¡Iniciar sesión!</h2>

      <form @submit.prevent="login" style="text-align: left;">
        <!-- Correo -->
        <div style="margin-bottom: 1rem;">
          <label for="email" style="display: block; margin-bottom: 0.25rem;">Correo</label>
          <input type="text" id="email" v-model="correo"
            style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px;" />
        </div>

        <!-- Contraseña con ícono -->
        <div style="margin-bottom: 1rem; position: relative;">
          <label for="password" style="display: block; margin-bottom: 0.25rem;">Contraseña</label>
          <input :type="mostrarContrasena ? 'text' : 'password'" id="password" v-model="contrasena"
            style="width: 100%; padding: 0.5rem 2.5rem 0.5rem 0.5rem; border: 1px solid #ccc; border-radius: 6px;" />

          <!-- Ícono de ojo -->
          <svg @click="toggleMostrarContrasena" xmlns="http://www.w3.org/2000/svg"
            :fill="mostrarContrasena ? '#12BACA' : '#888'" viewBox="0 0 24 24" width="20" height="20"
            style="position: absolute; top: 35px; right: 10px; cursor: pointer;">
            <path
              d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
            <circle cx="12" cy="12" r="2.5" />
          </svg>
        </div>

        <button type="submit"
          style="width: 100%; background-color: #12BACA; color: white; border: none; padding: 0.6rem; border-radius: 6px; font-weight: 600; cursor: pointer;">
          Iniciar Sesión
        </button>
      </form>

      <p v-if="mensaje" style="margin-top: 1rem; color: red; font-size: 0.9rem;">{{ mensaje }}</p>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  data() {
    return {
      correo: '',
      contrasena: '',
      mensaje: '',
      mostrarContrasena: false,
    };
  },
  methods: {
    toggleMostrarContrasena() {
      this.mostrarContrasena = !this.mostrarContrasena;
    },
    login() {
      axios.post('/login', {
        correo: this.correo,
        contrasena: this.contrasena
      })
        .then(response => {
          if (response.data.success) {
            this.$store.dispatch('guardarUsuario', response.data.usuario)

            Swal.fire({
              title: "Login exitoso",
              text: "¡Bienvenido!",
              icon: "success",
              confirmButtonColor: "#12BACA",
            }).then(
              () => {window.location.href = '/inicio'}
            )

          } else {
            Swal.fire({
              title: "Login fallido",
              text: response.data.message || "Credenciales incorrectas.",
              icon: "error",
              confirmButtonColor: "#d33",
            });
          }
        })
    }
  }
};
</script>
