<template>
  <v-flex xs12 class="px-3">
    <v-row justify="center">
      <v-col cols="12"  >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="9" >
                BOLETAS DE PAGO
              </v-col>
              <v-col cols="12" md="4" class="py-0"  >
                <v-text-field
                  dense
                  v-model="boletas[0]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1raBoleta')"
                  data-vv-name="1raBoleta"
                  label="1ra Boleta"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible">
                <v-text-field
                  dense
                  v-model="boletas[1]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2daBoleta')"
                  data-vv-name="2daBoleta"
                  label="2ra Boleta"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible" >
                <v-text-field
                  dense
                  v-model="boletas[3]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3raBoleta')"
                  data-vv-name="3raBoleta"
                  label="3ra Boleta"
                ></v-text-field>
              </v-col>
              <v-col cols="12" class="py-0" >
                BONOS
              </v-col>
              <v-col cols="12" md="3" class="py-0">
                <v-text-field
                  dense
                  v-model="bonos[0]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1erBono')"
                  data-vv-name="1erBono"
                  label="Bono Frontera"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3"  class="py-0">
                <v-text-field
                  dense
                  v-model="bonos[1]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2doBono')"
                  data-vv-name="2doBono"
                  label="Bono Oriente"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" class="py-0" >
                <v-text-field
                  dense
                  v-model="bonos[2]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3erBono')"
                  data-vv-name="3erBono"
                  label="Bono Cargo"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" class="py-0">
                <v-text-field
                  dense
                  v-model="bonos[3]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('4toBono')"
                  data-vv-name="4toBono"
                  label="Bono Seguridad Ciudadana"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" class="py-0">
                <v-checkbox
                  v-model="refinanciamiento"
                  :label="`Refinanciamiento`"
                ></v-checkbox>
              </v-col>
              <v-col cols="12"  md="2"  v-if="refinanciamiento==true" >
                CUOTA MUSERPOL
              </v-col>
              <v-col cols="12" md="2" class="py-0" v-if="refinanciamiento==true">
                <v-text-field
                  dense
                  outlined
                  v-model="muserpol"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('muserpol')"
                  data-vv-name="muserpol"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-flex>
</template>
<script>
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "dashboard-index",
  data: () => ({
    boletas: [null,null,null],
    bonos: [null,null,null,null],
    plazo_meses : 24,
    monto_solicitado : null,
    loanTypeSelected:null,
    visible: true,
    refinanciamiento: false,
    muserpol: null,
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
      promedio() {
        if (this.boletas.length > 0) {
            return this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
        } else {
            return 0
        }
      },
  
      calcular_cuota()
      {
       if (this.plazo_meses >0 && this.monto_solicitado>0){
        var resultado = 0;
        return (((0.01666)/(1-(1/Math.pow((1+0.01666),this.plazo_meses))))*this.monto_solicitado)
         
       } else{
          var cuota_maxima=0
         if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          var monto_maximo = (1-(1/Math.pow((1+0.01666),this.plazo_meses)))*(0.5*liquido_calificacion)/0.01666
          if (monto_maximo > 25000){
            monto_maximo = 25000
          } else {
            monto_maximo = Math.trunc(Math.round(Math.floor(monto_maximo))/1000)*1000
          }
          var cuota_maxima = (((0.01666)/(1-(1/Math.pow((1+0.01666),this.plazo_meses))))*monto_maximo)
          return cuota_maxima
         }else{
           return 0
         }
       }
      },
      monto_maximo() {
         if (this.boletas.length > 0) {
         let total_bono = 0
         var liquido_calificacion=0;
         var promedio = this.boletas.reduce((a, b) => { return Number(a) + Number(b) }) / this.boletas.length
         if (this.bonos.length > 0) {
           total_bono = this.bonos.reduce((a, b) => { return Number(a) + Number(b) }) 
         }
         liquido_calificacion = promedio -total_bono
          var monto_maximo = (1-(1/Math.pow((1+0.01666),this.plazo_meses)))*(0.5*liquido_calificacion)/0.01666
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