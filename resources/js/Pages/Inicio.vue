<template>
  <div>
    <Header />

    <!-- Banner motivacional -->
    <div class="text-center py-5 header-programa text-white banner-motivacional">
      <h2 class="display-5 fw-bold mb-2">💡 Frase del día</h2>
      <p class="lead mb-0">{{ fraseActual }}</p>
    </div>

    <!-- Datos del usuario -->
    <div class="container mt-5">
      <div class="card shadow p-4">
        <div class="row align-items-center">
          <div class="col-md-3 text-center">
            <img
              :src="fotoPerfilUrl"
              alt="Foto de perfil"
              class="rounded-circle img-fluid border border-primary"
              style="max-width: 150px;"
            />
          </div>
          <div class="col-md-9">
            <h2 class="text-primary">¡Bienvenido, {{ usuario.l_nombre }}!</h2>
            <p class="mb-1"><strong>Apellido:</strong> {{ usuario.l_apellido }}</p>
            <p class="mb-1"><strong>Correo:</strong> {{ usuario.l_correoElectronico }}</p>
            <p class="mb-0"><strong>Fecha de nacimiento:</strong> {{ usuario.f_nacimiento }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '@/components/Header.vue'

export default {
  components: {
    Header,
  },
  data() {
    return {
      frases: [
        "El éxito es la suma de pequeños esfuerzos repetidos cada día.",
        "Cree en ti y todo será posible.",
        "Nunca es tarde para comenzar algo grande.",
        "Hazlo con pasión o no lo hagas.",
        "Tu actitud determina tu altitud.",
        "Cada día es una nueva oportunidad para crecer.",
      ],
      fraseActual: "",
      intervalo: null,
    }
  },
  computed: {
    usuario() {
      return this.$store.state.usuario
    },
    fotoPerfilUrl() {
      return '/' + this.usuario.l_fotoPerfil
    },
  },
  mounted() {
    this.actualizarFrase()
    this.intervalo = setInterval(this.actualizarFrase, 8000) // cambia cada 8 segundos
  },
  beforeDestroy() {
    clearInterval(this.intervalo)
  },
  methods: {
    actualizarFrase() {
      const index = Math.floor(Math.random() * this.frases.length)
      this.fraseActual = this.frases[index]
    },
  }
}
</script>

<style scoped>
.card {
  border-radius: 15px;
  background-color: #ffffff;
}

.banner-motivacional {
  background: linear-gradient(to right, #0d6efd, #6610f2);
}
</style>
