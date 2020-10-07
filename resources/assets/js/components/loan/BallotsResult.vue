<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0" v-show="modalidad.procedure_type_id!=12">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="3">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                      <ValidationProvider v-slot="{ errors }" name="plazo" :rules="'numeric|min_value:'+datos.minimum_term+'|max_value:'+datos.maximum_term" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Plazo en Meses"
                        v-model="calculator_result.months_term"
                        v-on:keyup.enter="simuladores()"
                      ></v-text-field>
                      </ValidationProvider>
                      <ValidationProvider v-slot="{ errors }" name="monto solicitado" :rules="'numeric|min_value:'+datos.minimun_amoun+'|max_value:'+datos.maximun_amoun" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Monto Solicitado"
                        v-model ="calculator_result.amount_requested"
                        v-on:keyup.enter="simuladores()"
                      ></v-text-field>
                      </ValidationProvider>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <p>PROMEDIO LIQUIDO PAGABLE:{{ liquid_calificated[0].payable_liquid_calculated}}</p>
                            <p>TOTAL BONOS: {{ liquid_calificated[0].bonus_calculated }}</p>
                            <p>LIQUIDO PARA CALIFICACION: {{ liquid_calificated[0].liquid_qualification_calculated}}</p>
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
                            <p>CALCULO DE CUOTA: {{ calculator_result.quota_calculated_estimated_total }}</p>
                            <p>INDICE DE ENDEUDAMIENTO: {{calculator_result.indebtnes_calculated_total }}</p>
                            <p class="success--text font-weight-black">MONTO MAXIMO SUGERIDO : {{calculos.amount_maximum_suggested}}</p>
                            {{calculos}}
                            <br>
                            {{liquid_calificated}}
                            <br>
                            {{calculator_result}}
                            <br>
                            {{loan_detail}}
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="1" class="ma-0 pa-0"></v-col>
                </v-row>
              </v-container>
              <BallotsResultHipotecary
              v-show="modalidad.procedure_type_id==12"
              :lenders.sync="lenders"
              :intervalos.sync="intervalos"
              :datos.sync="datos"
              :liquid_calificated.sync="liquid_calificated"
              :loan_detail.sync="loan_detail"
              :modalidad_net_realizable_value.sync="modalidad.net_realizable_value"
             />
            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>

import BallotsResultHipotecary from '@/components/loan/BallotsResultHipotecary'
export default {
  name: "loan-requirement",
   data: () => ({
    ver:false
  }),
  props: {
    loan_detail: {
      type: Object,
      required: true
    },
    calculator_result: {
      type: Object,
      required: true
    },
      lenders: {
      type: Array,
      required: true
    },
     modalidad_id: {
      type: Number,
      required: true,
      default: 0
    },
     intervalos: {
      type: Object,
      required: true
    },
    datos: {
      type: Object,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
    calculos: {
      type: Object,
      required: true
    },
    liquid_calificated: {
      type: Array,
      required: true
    },
    modalidad_net_realizable_value:{
      type: Number,
      required: true,
      default:0
    }
  },
    components: {
    BallotsResultHipotecary
  },
 computed: {
    ver1() {
      if(this.intervalos.procedure_type_id==12)
      {
        return true
      }else
      {
        return false
      }
    }
  },
  methods: {
     async simuladores() {
       try {
        let res = await axios.post(`simulator`, {
          procedure_modality_id:this.modalidad.id,
          amount_requested: this.calculator_result.amount_requested,
          months_term:  this.calculator_result.months_term,
          guarantor: false,
          liquid_qualification_calculated_lender: 0,
          liquid_calculated:this.liquid_calificated
        })
      this.calculator_result = res.data
      this.loan_detail.amount_requested=this.calculator_result.amount_requested
this.loan_detail.months_term=this.calculator_result.months_term
this.loan_detail.liquid_qualification_calculated=this.simulator.liquid_qualification_calculated_total
this.loan_detail.indebtedness_calculated=this.simulator.indebtnes_calculated_total
/*      for (this.j = 0; this.j< this.simulator.length; this.j++){
          this.simulator[this.j].affiliate_nombres=this.datos_calculadora_hipotecario[this.j].affiliate_name
          console.log(""+this.simulator[this.j].affiliate_nombres)
        }
*/
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async Calculator() {
      try {
        if (this.modalidad.quantity_ballots > 1) {
          let res = await axios.post(`calculator`, {
            procedure_modality_id: this.modalidad.id,
            months_term: this.calculos.plazo,
            amount_requested: this.calculos.montos,
            affiliate_id: this.$route.query.affiliate_id,
            contributions: [
              {
                payable_liquid: this.payable_liquid[0],
                seniority_bonus: this.bonos[2],
                border_bonus: this.bonos[0],
                public_security_bonus: this.bonos[3],
                east_bonus: this.bonos[1]
              },
              {
                payable_liquid: this.payable_liquid[1],
                seniority_bonus: 0,
                border_bonus: 0,
                public_security_bonus: 0,
                east_bonus: 0
              },
              {
                payable_liquid: this.payable_liquid[2],
                seniority_bonus: 0,
                border_bonus: 0,
                public_security_bonus: 0,
                east_bonus: 0
              }
            ]
          })
          this.calculo = res.data

          this.calculos.payable_liquid_calculated = this.calculo.payable_liquid_calculated
          this.calculos.bonus_calculated = this.calculo.bonus_calculated
          this.calculos.liquid_qualification_calculated = this.calculo.liquid_qualification_calculated
          this.calculos.quota_calculated = this.calculo.quota_calculated
          this.calculos.indebtedness_calculated = this.calculo.indebtedness_calculated
          this.calculos.amount_maximum_suggested = this.calculo.amount_maximum_suggested
          this.calculos.plazo = this.calculos.plazo

            if (this.calculos.montos>this.calculo.amount_maximum_suggested) {
            this.calculos.montos = this.calculo.amount_maximum_suggested
          } else {
            this.calculos.montos = this.calculos.montos
          }
        } else {
          let res = await axios.post(`calculator`, {
            procedure_modality_id: this.modalidad.id,
            months_term: this.calculos.plazo,
            amount_requested: this.calculos.montos,
            affiliate_id: this.$route.query.affiliate_id,
            contributions: [
              {
                payable_liquid: this.payable_liquid[0],
                seniority_bonus: this.bonos[2],
                border_bonus: this.bonos[0],
                public_security_bonus: this.bonos[3],
                east_bonus: this.bonos[1]
              }
            ]
          })
          this.calculo = res.data

          this.calculos.payable_liquid_calculated = this.calculo.payable_liquid_calculated
          this.calculos.bonus_calculated = this.calculo.bonus_calculated
          this.calculos.liquid_qualification_calculated = this.calculo.liquid_qualification_calculated
          this.calculos.quota_calculated = this.calculo.quota_calculated
          this.calculos.indebtedness_calculated = this.calculo.indebtedness_calculated
          this.calculos.amount_maximum_suggested = this.calculo.amount_maximum_suggested
          this.calculos.plazo = this.calculos.plazo

            if (this.calculos.montos>this.calculo.amount_maximum_suggested) {
            this.calculos.montos = this.calculo.amount_maximum_suggested
          } else {
            this.calculos.montos = this.calculos.montos
          }  
       }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>