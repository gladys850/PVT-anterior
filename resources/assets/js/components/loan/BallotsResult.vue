<template>
  <v-container fluid>
    <v-card>
      <v-row justify="center">
        <v-col cols="12"  >
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" class="py-0">
                Resultado para el Prestamo
              </v-col>
              <br>
              <v-col cols="12" md="3" >
                <v-layout row wrap>
                  <v-flex xs12 class="px-2">
                    <fieldset class="pa-3">
                      <v-text-field
                        label="Plazo en Meses"
                        v-model="plazo_meses"
                        v-validate.initial="`required|numeric|min_value:1|max_value:36`"
                        :error-messages="errors.collect('plazo')"
                        data-vv-name="plazo"
                        v-on:keyup.enter="Calculator()"
                      ></v-text-field>
                      <v-text-field
                        label="Monto Maximo Sugerido"
                        v-model ="monto_maximo_sugerido"
                        v-validate.initial="`required|numeric`"
                        :error-messages="errors.collect('monto_solicitado')"
                        data-vv-name="monto_solicitado"
                        v-on:keyup.enter="Calculator()"
                      ></v-text-field>
                    </fieldset>
                  </v-flex>
                </v-layout>
              </v-col>
            <v-col cols="12" md="4">
              <v-card-text class="py-0">
              <v-layout row wrap>
          <v-flex xs12 class="px-2">
            <fieldset class="pa-3">
             <p>PROMEDIO LIQUIDO PAGABLE:{{ promedio_liquido_pagable}}</p>
              <p>TOTAL BONOS: {{ total_bonos }}</p>
              <p>LIQUIDO PARA CALIFICACION: {{ liquido_para_calificacion}}</p>
            </fieldset>
          </v-flex>
        </v-layout>
        </v-card-text>
            </v-col>
            <v-col cols="12" md="4">
              <v-card-text class="py-0">
              <v-layout row wrap>
          <v-flex xs12 class="px-2">
            <fieldset class="pa-3">
              <p>CALCULO DE CUOTA: {{ calculo_de_cuota }}</p>
              <p>INDICE DE ENDEUDAMIENTO: {{indice_endeudamiento }}</p>
              <p>MONTO MAXIMO SUGERIDO : {{monto_maximo_sugerido}}</p>
            </fieldset>
          </v-flex>
        </v-layout>
        </v-card-text>
            </v-col>
              <v-col cols="12" md="1" class="ma-0 pa-0">
              </v-col>
            </v-row>
          </v-container >
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>
<script>
import { Validator } from 'vee-validate'
export default {
inject: ['$validator'],
name: "loan-requirement",
data: () => ({
  plazo_meses :2,
  monto_solicitado : null,
  loanTypeSelected:null,
  promedio_liquido_pagable:null,
  total_bonos:null,
  liquido_para_calificacion:null,
  calculo_de_cuota:null,
  indice_endeudamiento:null,
  calculo:null,
  monto_maximo_sugerido:2000
}),
  props: {
    bonos: {
      type: Array,
      required: true
    },
    payable_liquid: {
      type: Array,
      required: true
    },
     modality: {
      type: Object,
      required: true
    },
    datos: {
      type: Array,
      required: true
    }
  },
methods:{
  async Calculator() {
    try {
      console.log('entro a calculadora')
      let res = await axios.post(`calculator`, {
        procedure_modality_id:this.modality.id,
        months_term: this.plazo_meses,
        amount_requested:this.monto_maximo_sugerido,
        affiliate_id:this.$route.query.affiliate_id,
        contributions: [
          {
            payable_liquid: this.payable_liquid[0],
            seniority_bonus:  this.bonos[2],
            border_bonus: this.bonos[0],
            public_security_bonus: this.bonos[3],
            east_bonus:this.bonos[1]
          },
          {
            payable_liquid: this.payable_liquid[1]
          },
          {
            payable_liquid: this.payable_liquid[2]
          }
        ]
      })
 this.calculo= res.data
console.log('entro a calculadora'+this.calculo)
 this.promedio_liquido_pagable=this.calculo.promedio_liquido_pagable
 this.total_bonos=this.calculo.total_bonos
 this.liquido_para_calificacion=this.calculo.liquido_para_calificacion
 this.calculo_de_cuota=this.calculo.calculo_de_cuota
 this.indice_endeudamiento=this.calculo.indice_endeudamiento
 this.monto_maximo_sugerido=this.calculo.monto_maximo_sugerido
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
}
};
</script>