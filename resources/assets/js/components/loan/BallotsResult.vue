<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <v-card>
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12" class="py-0">Resultado para el Prestamo</v-col>
                  <br />
                  <v-col cols="12" md="3">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                      <ValidationProvider v-slot="{ errors }" name="plazo" rules="numeric|min_value:1|max_value:96" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Plazo en Meses"
                        v-model="calculos.plazo"
                        v-on:keyup.enter="Calculator()"
                      ></v-text-field>
                      </ValidationProvider>
                      <ValidationProvider v-slot="{ errors }" name="monto solicitado" rules="numeric" mode="aggressive">
                      <v-text-field
                        :error-messages="errors"
                        label="Monto Maximo Sugerido"
                        v-model ="calculos.montos"
                        v-on:keyup.enter="Calculator()"
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
                            <p>PROMEDIO LIQUIDO PAGABLE:{{ calculos.promedio_liquido_pagable}}</p>
                            <p>TOTAL BONOS: {{ calculos.total_bonos }}</p>
                            <p>LIQUIDO PARA CALIFICACION: {{ calculos.liquido_para_calificacion}}</p>
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
                            <p>CALCULO DE CUOTA: {{ calculos.calculo_de_cuota }}</p>
                            <p>INDICE DE ENDEUDAMIENTO: {{calculos.indice_endeudamiento }}</p>
                            <p>MONTO MAXIMO SUGERIDO : {{calculos.monto_maximo_sugerido}}</p>
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
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "loan-requirement",
  props: {
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
    calculos: {
      type: Object,
      required: true
    }
  },
  methods: {
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
          this.calculos.promedio_liquido_pagable = this.calculo.promedio_liquido_pagable
          this.calculos.total_bonos = this.calculo.total_bonos
          this.calculos.liquido_para_calificacion = this.calculo.liquido_para_calificacion
          this.calculos.calculo_de_cuota = this.calculo.calculo_de_cuota
          this.calculos.indice_endeudamiento = this.calculo.indice_endeudamiento
          this.calculos.monto_maximo_sugerido = this.calculo.monto_maximo_sugerido
          this.calculos.plazo = this.calculos.plazo
          this.calculos.montos = this.calculos.montos
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
          this.calculos.promedio_liquido_pagable = this.calculo.promedio_liquido_pagable
          this.calculos.total_bonos = this.calculo.total_bonos
          this.calculos.liquido_para_calificacion = this.calculo.liquido_para_calificacion
          this.calculos.calculo_de_cuota = this.calculo.calculo_de_cuota
          this.calculos.indice_endeudamiento = this.calculo.indice_endeudamiento
          this.calculos.monto_maximo_sugerido = this.calculo.monto_maximo_sugerido
          this.calculos.plazo = this.calculos.plazo
          if (this.calculos.montos > this.calculo.monto_maximo_sugerido) {
            this.calculos.montos = this.calculo.monto_maximo_sugerido
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