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
                      ></v-text-field>
                      <v-text-field
                        label="Monto Maximo Sugerido"
                        v-model ="monto_maximo_sugerido"
                        v-validate.initial="`required|numeric|min_value:2001|max_value:25000`"
                        :error-messages="errors.collect('monto_solicitado')"
                        data-vv-name="monto_solicitado"
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
  plazo_meses : 30,
  monto_solicitado : null,
  loanTypeSelected:null,
  promedio_liquido_pagable:null,
  total_bonos:null,
  liquido_para_calificacion:null,
  calculo_de_cuota:null,
  indice_endeudamiento:null,
  monto_maximo_sugerido:null,
}),
beforeMount(){
this.Calculator()
},
methods:{
  async Calculator() {
    try {
      this.loading = true
      let res = await axios.post(`calculator`, {
        procedure_modality_id: 34,
        months_term: 30,
        amount_request:9130,
        affiliate_id:this.$route.query.affiliate_id,
        contributions: [
          {
            payable_liquid: 2419.30,
            seniority_bonus: 1305.60,
            border_bonus: 0.00,
            public_security_bonus: 0.00,
            east_bonus: 0.00
          },
          {
            payable_liquid: 2270.00
          },
          {
            payable_liquid: 1563.00
          }
        ]
      })
 this.calculo= res.data
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