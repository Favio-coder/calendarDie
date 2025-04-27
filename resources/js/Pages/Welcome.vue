<template>
    <div>
      Se exporta la siguiente data del backend:
      <ul>
        <li v-for="element in list">
            {{ element }}
        </li>
      </ul>
      <div v-if="q_mensaje">
          {{ message }}
        </div>

    </div>
</template>
  
<script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        nombre: "Favio",
        data: [],
        list: [],
        message: '',
        q_mensaje: false 
      };
    },
    mounted(){

      axios.get('/obtenerDatos').then(
        response => {
          this.list = response.data.items
          this.message = response.data.mensaje
        }
      ).catch(err =>{
        console.error("Existe el siguiente error: ", err)
      })


      this.renderizarMensaje()
    },
    methods:{
      renderizarMensaje(){
        const array = [1,1,2]

        if(array.length === 3){
            this.q_mensaje = true
        }
      }
    }
  }
</script>
