<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
        <v-row justify="center">
          <v-col cols="3" class="py-2">
            <v-text-field
              class="py-0"
              dense
              :outlined="false"
              :readonly="true"
              label="Codigo de Prestamo Padre"
              v-model="loan_sismu.code"
            ></v-text-field>
          </v-col>
          <v-col cols="3" class="py-2">
            <v-text-field
              class="py-0"
              dense
              :outlined="false"
              :readonly="true"
              label="Monto"
              v-model="loan_sismu.amount_approved"
            ></v-text-field>
          </v-col>
          <v-col cols="2" class="py-2">
            <v-text-field
              class="py-0"
              dense
              :outlined="false"
              :readonly="true"
              label="Plazo"
              v-model="loan_sismu.loan_term"
            ></v-text-field>
          </v-col>
          <v-col cols="2" class="py-2">
            <v-text-field
              class="py-0"
              dense
              :outlined="false"
              :readonly="true"
              label="Saldo"
              v-model="loan_sismu.balance"
            ></v-text-field>
          </v-col>
          <v-col cols="2" class="py-2">
            <v-text-field
              class="py-0"
              dense
              :outlined="false"
              :readonly="true"
              label="Cuota"
              v-model="loan_sismu.estimated_quota"
            ></v-text-field>
          </v-col>
          <v-col cols="12" class="pt-0">
            <v-container class="py-0">
              <v-row>
                <slot name="title"></slot>
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
                        <fieldset class="py-0">
                          <ul style="list-style: none" class="py-3 ps-4 ">
                            <li v-for="(liquid,i) in liquid_calificated" :key="i" >
                              <p>PROMEDIO LIQUIDO PAGABLE:{{ liquid.payable_liquid_calculated}}</p>
                              <p>TOTAL BONOS: {{ liquid.bonus_calculated }}</p>
                              <p>LIQUIDO PARA CALIFICACION: {{ liquid.liquid_qualification_calculated}}</p>
                              </li>
                            </ul>
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
                          <p>INDICE DE ENDEUDAMIENTO: {{calculator_result.indebtedness_calculated_total }}</p>
                          <p v-show="calculator_result.maximum_suggested_valid" class="success--text font-weight-black">MONTO MAXIMO SUGERIDO : {{calculator_result.amount_maximum_suggested}}</p>
                          <p v-show="!calculator_result.maximum_suggested_valid" class="error--text font-weight-black">MONTO MAXIMO SUGERIDO : {{calculator_result.amount_maximum_suggested}}</p>
                          <p v-show="!calculator_result.maximum_suggested_valid" class="error--text font-weight-black">NO PUEDE ACCEDER A UN PRESTAMO DE ESTA MODALIDAD</p>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-card-text>
                </v-col>
                <v-col cols="12" md="1" class="ma-0 pa-0"></v-col>
              </v-row>
            </v-container>
          </v-col>
        </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "loan-requirement",
  data: () => ({
    ver:false
  }),
  props: {
    loan_sismu: {
      type: Object,
      required: true
    },
    loan_detail: {
      type: Object,
      required: true
    },
    calculator_result: {
      type: Object,
      required: true
    },
    modalidad_id: {
      type: Number,
      required: true,
      default: 0
    },
    datos: {
      type: Object,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
    liquid_calificated: {
      type: Array,
      required: true
      },
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

        if( this.calculator_result.amount_requested<this.calculator_result.amount_maximum_suggested){
          this.calculator_result.amount_requested=this.calculator_result.amount_requested
          this.loan_detail.amount_requested=this.calculator_result.amount_requested
        }else{
          this.calculator_result.amount_requested=this.calculator_result.amount_maximum_suggested
          this.loan_detail.amount_requested=this.calculator_result.amount_maximum_suggested
        }
        this.loan_detail.months_term=this.calculator_result.months_term
        this.loan_detail.indebtedness_calculated=this.calculator_result.indebtedness_calculated_total

        this.loan_detail.maximum_suggested_valid=this.calculator_result.maximum_suggested_valid
        this.loan_detail.quota_calculated_total_lender=this.calculator_result.quota_calculated_estimated_total

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
    }
  }
}
</script>