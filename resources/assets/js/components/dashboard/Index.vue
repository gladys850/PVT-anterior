<template>
  <v-container fluid>
    <v-card>
      <v-card-text>
        <v-select
          :items="loanTypes"
          label="Tipo de préstamo"
          v-model="loanTypeSelected"
        ></v-select>
        <v-layout row wrap v-if="loanTypeSelected == 'Corto plazo'">
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Boletas de Pago</legend>
                <v-text-field
                  label="1a Boleta"
                  v-model="boletas[0]"
                  v-validate.initial="'required|numeric|min:1|max:10'"
                  :error-messages="errors.collect('1ra boleta')"
                  data-vv-name="1ra boleta"
                ></v-text-field>
                <v-text-field
                  label="2a Boleta"
                  v-model="boletas[1]"
                  v-validate.initial="'required|numeric|min:1|max:10'"
                  :error-messages="errors.collect('2a boleta')"
                  data-vv-name="2a boleta"
                ></v-text-field>
                <v-text-field
                  label="3a Boleta"
                  v-model="boletas[2]"
                  v-validate.initial="'required|numeric|min:1|max:10'"
                  :error-messages="errors.collect('3ra boleta')"
                  data-vv-name="3ra boleta"
                ></v-text-field>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Bonos</legend>
              <template>
                Bono Frontera 
                <v-text-field
                label="Introduzca bono" 
                
                v-model="bonos[0]"
                ></v-text-field>
                Bono Oriente 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[1]"
                ></v-text-field>
                Bono Cargo 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[2]"
                ></v-text-field>
                Bono Seguridad Ciudadana 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[3]"
                ></v-text-field>
              </template>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Datos del Préstamo</legend>
              <template>
                Plazo en meses 
                <v-text-field
                label="Introduzca plazo"
                v-model="plazo_meses"
                v-validate.initial="'required|numeric|min:1|max:2'"
                onKeyUp="if(this.value>24){this.value='24';}"              
                :error-messages="errors.collect('meses plazo')"
                data-vv-name="meses plazo"
                ></v-text-field>
              </template>
              <template>
                Monto solicitado
                <v-text-field
                label="Introduzca monto"
                v-model ="monto_solicitado"
                v-validate.initial="'required|numeric|min:1|max:5000'"
                :error-messages="errors.collect('monto solicitado')"
                data-vv-name="monto solicitado"
               onKeyUp="if(this.value>2500){this.value='25000';}else if(this.value<0){'El monto debe ser menor igual a 25000';}"              
                ></v-text-field>
              </template>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Cálculo</legend>
              <p>PROMEDIO LIQUIDO PAGABLE:  {{ promedio }}</p>
              <p>TOTAL BONOS: {{ suma_bono }}</p>
              <p>LIQUIDO PARA CALIFICACION: {{ promedio-suma_bono }}</p>
              <p>CALCULO DE CUOTA: {{ calcular_cuota }}</p>
               
              <p>INDICE DE ENDEUDAMIENTO: {{ promedio>0 ? calcular_cuota/(promedio-suma_bono)*100 : 0 }}</p>
              <p>MONTO MAXIMO SUGERIDO : {{monto_maximo}}</p>
            </fieldset>
          </v-flex>
        </v-layout>

        <v-layout row wrap v-else-if="loanTypeSelected == 'Largo plazo'">
            <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Boletas de Pago</legend>
                <v-text-field
                  label="1a Boleta"
                  v-model="boletas[0]"
                  v-validate.initial="'required|numeric|min:1|max:10'"
                  :error-messages="errors.collect('1ra boleta')"
                  data-vv-name="1ra boleta"
                ></v-text-field>
            </fieldset>
          </v-flex>
           <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Bonos</legend>
              <template>
                Bono Frontera 
                <v-text-field
                label="Introduzca bono" 
                
                v-model="bonos[0]"
                ></v-text-field>
                Bono Oriente 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[1]"
                ></v-text-field>
                Bono Cargo 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[2]"
                ></v-text-field>
                Bono Seguridad Ciudadana 
                <v-text-field
                label="Introduzca bono"
                v-model="bonos[3]"
                ></v-text-field>
              </template>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Datos del Préstamo</legend>
              <template>
                Plazo en meses 
                <v-text-field
                label="Introduzca plazo"
                v-model="plazo_meses"
                v-validate.initial="'required|numeric|min:1|max:2'"
                :error-messages="errors.collect('meses plazo')"
                data-vv-name="meses plazo"
                ></v-text-field>
              </template>
              <template>
                Monto solicitado
                <v-text-field
                label="Introduzca monto"
                v-model ="monto_solicitado"
                v-validate.initial="'required|numeric|min:1|max:5000'"
                :error-messages="errors.collect('monto solicitado')"
                data-vv-name="monto solicitado"
                ></v-text-field>
              </template>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Cálculo</legend>
              <p>PROMEDIO LIQUIDO PAGABLE:  {{ promedio }}</p>
              <p>TOTAL BONOS: {{ suma_bono }}</p>
              <p>LIQUIDO PARA CALIFICACION: {{ promedio-suma_bono }}</p>
              <p>CALCULO DE CUOTA: {{ calcular_cuota }}</p>
               
              <p>INDICE DE ENDEUDAMIENTO: {{ promedio>0 ? calcular_cuota/(promedio-suma_bono)*100 : 0 }}</p>
              <p>MONTO MAXIMO SUGERIDO : {{monto_maximo}}</p>
            </fieldset>
          </v-flex>


        </v-layout>

        <template v-else></template> 
        </v-card-text>
        </v-card>
  </v-container>
</template>

<script>
export default {
  name: "dashboard-index",
  data: () => ({
    boletas: [],
    loanTypeSelected: null,
    loanTypes: [
      'Corto plazo',
      'Largo plazo'
    ],
    bonos: [],
    plazo_meses : null,
    monto_solicitado : null
    
  }),
  watch: {
    monto_solicitado() {
      this.$validator.validateAll()
    },
    loanTypeSelected(newVal, oldVal) {
      if (newVal != oldVal) {
        this.boletas = []
        this.bonos = []
        if (newVal = 'Corto plazo') {
          this.plazo_meses = 24
        } else {
          this.plazo_meses = 60
        }
        this.monto_solicitado = this.monto_maximo
      }
     
    }
  },
  computed: {
      suma_bono() {
      if (this.bonos.length > 0) {
        return this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
      } else {
        return 0
      }
      },
      promedio() {
      if (this.boletas.length > 0) {
        return this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
      } else {
        return 0
      }
      },
  
      calcular_cuota()
      {
       // console.log(this.plazo_meses);
       if (this.plazo_meses >0 && this.monto_solicitado>0){
        var resultado = 0;
        return (((0.01666)/(1-(1/Math.pow((1+0.01666),this.plazo_meses))))*this.monto_solicitado)
         
       } else{
          var monto_maximo=0
          var cuota_maxima=0
         if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          monto_maximo = (1-(1/Math.pow((1+0.01666),24)))*(0.5*liquido_calificacion)/0.01666
          if (monto_maximo > 25000){
            monto_maximo = 25000

          } else {
            monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
          }
          var cuota_maxima = (((0.01666)/(1-(1/Math.pow((1+0.01666),24))))*monto_maximo)
          return cuota_maxima
         }else{
           return 0
         }
        
       }
      },
      monto_maximo() {
        // this.boletas = [2000,2000,1000]
         var monto_maximo=0
         if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          monto_maximo = (1-(1/Math.pow((1+0.01666),24)))*(0.5*liquido_calificacion)/0.01666
          if (monto_maximo > 25000){
            monto_maximo = 25000

          } else {
            monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
          }
         }
         this.monto_solicitado = monto_maximo

        return monto_maximo
      }
      
     
   }


};
</script>