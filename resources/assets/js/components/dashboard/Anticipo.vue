<template>
  <v-container fluid>
    <v-card>
      <v-card-text>
        <v-layout row wrap>
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
                v-model="plazo_meses_lp"
                v-validate.initial="`required|numeric|min_value:1|max_value:2`"          
                :error-messages="errors.collect('meses plazo')"
                data-vv-name="meses plazo"
                ></v-text-field>
              </template>
                <template>
                Monto solicitado
                <v-text-field
                label="Introduzca monto"
                v-model ="monto_solicitado"
                v-validate.initial="'required|numeric|min_value:1|max_value:2000'"
                :error-messages="errors.collect('monto solicitado')"
                data-vv-name="monto solicitado"
                ></v-text-field>
                <p class="red--text">{{ monto_solicitado>2000 ? "(Para acceder a un Anticipo el monto debe ser menor a 2000)" :"" }}</p>
              </template>
            </fieldset>
          </v-flex>
          <v-flex xs3 class="px-2">
            <fieldset class="pa-3">
              <legend class=" mx-2 px-1">Cálculo</legend>
              <p>LIQUIDO PAGABLE:  {{ liquido_pagable }}</p>
              <p>TOTAL BONOS: {{ suma_bono }}</p>
              <p>LIQUIDO PARA CALIFICACION: {{ liquido_pagable-suma_bono }}</p>
              <p>CALCULO DE CUOTA: {{ calcular_cuota_LP}}</p>  
              <p class="red--text">{{ liquido_pagable>0 ? (calcular_cuota_LP/(liquido_pagable-suma_bono)*100)>90 ? "( EL INDICE DE ENDEUDAMIENTO NO PUEDE EXCEDER EL 90 % )" :"" : "" }}</p>
              <p>INDICE DE ENDEUDAMIENTO: {{ liquido_pagable>0 ? (calcular_cuota_LP/(liquido_pagable-suma_bono)*100) : 0 }}</p>
              <p>MONTO MAXIMO SUGERIDO : {{monto_maximo_LP}}</p>
            </fieldset>
          </v-flex>
        </v-layout>
        </v-card-text>
        </v-card>
  </v-container>

</template>

<script>

export default {
  name: 'largo-plazo',
  data: () => ({
    boletas: [null],
    bonos: [null,null,null,null],
    plazo_meses_lp: 2,
    monto_solicitado : null,
    loanTypeSelected:null,
  }),
  watch: {
    monto_solicitado() {
      this.$validator.validateAll()
    },
  },
  computed: {

      suma_bono() {
      if (this.bonos.length > 0) {
        return this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
      } else {
        return 0
      }
      },
      liquido_pagable() {
      if (this.boletas.length > 0) {
        return this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) 
      } else {
        return 0
      }
      },
        calcular_cuota_LP()
      {
       if (this.plazo_meses_lp >0 && this.monto_solicitado>0){
        var resultado = 0;
        return (((0.03)/(1-(1/Math.pow((1+0.03),this.plazo_meses_lp))))*this.monto_solicitado)
         
       } else{
          var cuota_maxima=0
         if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) 
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          var monto_maximo = (1-(1/Math.pow((1+0.03),this.plazo_meses_lp)))*(0.9*liquido_calificacion)/0.03
          if (monto_maximo > 2000){
            monto_maximo = 2000
          } else {
            monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
          }
          var cuota_maxima = (((0.03)/(1-(1/Math.pow((1+0.03),this.plazo_meses_lp))))*monto_maximo)
           console.log(cuota_maxima)
          return cuota_maxima
         }else{
           return 0
         }
       }
      },
      monto_maximo_LP() {
             if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          var monto_maximo = (1-(1/Math.pow((1+0.03),this.plazo_meses_lp)))*(0.9*liquido_calificacion)/0.03
          if (monto_maximo > 2000){
            monto_maximo = 2000
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

