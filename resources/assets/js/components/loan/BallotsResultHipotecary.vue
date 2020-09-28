<template>
  <v-container fluid class="py-0">
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="3"  class="py-0">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-1">
                        <fieldset class="pa-3">
                      <ValidationProvider v-slot="{ errors }" name="plazo" :rules="'numeric|min_value:'+datos.minimum_term+'|max_value:'+datos.maximum_term" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Plazo en Meses"
                        v-model="intervalos.maximum_term"
                        v-on:keyup.enter="calculadora()"
                      ></v-text-field>
                      </ValidationProvider>
                      <ValidationProvider v-slot="{ errors }" name="monto solicitado" :rules="'numeric|min_value:'+datos.minimun_amoun+'|max_value:'+datos.maximun_amoun" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Monto Solicitado"
                        v-model ="monto_hipotecario"
                        v-on:keyup.enter="calculadora()"
                      ></v-text-field>
                      </ValidationProvider>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="9" class="py-0">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-1">
                          <fieldset class="pa-2">
                              <v-toolbar-title>Liquido Pagable</v-toolbar-title>
                              {{liquid_calificated}}
                             <ul style="list-style: none" class="pa-0">
                              <li v-for="(liquido,i) in datos_calculadora_hipotecario" :key="i" >
                                <v-progress-linear></v-progress-linear>
                                <p class="py-0 mb-0">Nombre del Afiliado: {{ liquido.affiliate_name}}</p>
                                <p class="py-0 mb-0">Liquido Pagable: {{liquido.payable_liquid_calculated+"  "}}{{"  "+"Total de Bonos:"+liquido.bonus_calculated}}{{" "}}Liquido para Calificacion: {{" "+liquido.liquid_qualification_calculated}}</p>
                              </li>
                            </ul>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="12" class="pl-1">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-0">
                          <fieldset class="pa-3">
                            <v-toolbar-title>Calculo</v-toolbar-title>
                              <ul style="list-style: none" class="pa-0">
                                <li v-for="(calculado,i) in calculo1234" :key="i" >
                                  <v-progress-linear></v-progress-linear>
                                  <p class="py-0 mb-0">Nombre del Afiliado: {{ calculado.affiliate_nombres}}</p>
                                  <p class="py-0 mb-0">Cuota Estimada: {{calculado.quota_calculated_estimated+" "}}{{" "}}Interes Calculado: {{calculado.indebtedness_calculated}} % {{" "+"Porcentaje de Pago:"}} {{calculado.percentage_payment}}{{" "}}is_valid: {{calculado.is_valid}}</p>
                                </li>
                              </ul>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
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
  name: "ballots-result-hipotecary",
  data: () => ({
    datos: {},
    bonos: {},
    payable_liquid: {},
    modalidad: {},
    calculos: {},
    editar:true,
    monto:null,
    plazo:null,
    interval:[],
    loanTypeSelected:null,
    visible:false,
    num_type:9,
    monto_hipotecario:null,
    calculo1234:null,
        calculo123:null,
        nombres:[]
  
  }),
  props: {
   datos: {
      type: Object,
      required: true
    },
    datos_calculadora_hipotecario: {
      type: Array,
      required: true
    },
    intervalos: {
      type: Object,
      required: true
    },/*
    bonos: {
      type: Array,
      required: true
    },
    payable_liquid: {
      type: Array,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
     prueba: {
      type: Array,
      required: true
    },*/
    calculos: {
      type: Object,
      required: true
    },
    liquid_calificated: {
      type: Array,
      required: true
    }
  },
  methods: {
    async calculadora() {
      try {
      let res1 = await axios.post(`simulator`, {
              procedure_modality_id: 46,
              amount_requested: this.monto_hipotecario,
              months_term: this.intervalos.maximum_term,
              guarantor: true,
              quota_lender: 1498,
              liquid_calculated: [
        {
            affiliate_id: 51419,
            liquid_qualification_calculated: 2200
        },
        {
            affiliate_id:1,
            liquid_qualification_calculated: 2700
        }
    ]
          })
          this.calculo1234 = res1.data

      for (this.j = 0; this.j< this.calculo1234.length; this.j++) {

        this.calculo1234[this.j].affiliate_nombres=this.datos_calculadora_hipotecario[this.j].affiliate_name

        console.log(""+this.calculo1234[this.j].affiliate_nombres)
      }


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