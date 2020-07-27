<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="4">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <v-toolbar-title>PRÉSTAMO</v-toolbar-title>
                          <br>
                          <p>PLAZO EN MESES: {{ calculos.plazo}}</p>
                          <p>MONTO SOLICITADO: {{ calculos.montos }}</p>
                          <p>PROMEDIO LIQUIDO PAGABLE: {{ calculos.payable_liquid_calculated}}</p>
                          <p>TOTAL BONOS: {{ calculos.bonus_calculated }}</p>
                          <p>LIQUIDO PARA CALIFICACION: {{ calculos.liquid_qualification_calculated}}</p>
                          <p>CALCULO DE CUOTA: {{ calculos.quota_calculated }}</p>
                          <p>INDICE DE ENDEUDAMIENTO: {{calculos.indebtedness_calculated }}</p>
                          <p>MONTO MAXIMO SUGERIDO : {{calculos.amount_maximum_suggested}}</p>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <v-toolbar-title>GARANTE</v-toolbar-title>
                            <br>
                            <p>{{loan.guarantors}}</p>
                            <p>NOMBRE COMPLETO:{{ calculos.payable_liquid_calculated}}</p>
                            <p>NUMERO DE PRESTAMOS:{{ calculos.payable_liquid_calculated}}</p>
                            <p>PORCENTAJE DE PAGO:{{ calculos.payable_liquid_calculated}}</p>
                            <p>NOMBRE COMPLETO:{{ calculos.payable_liquid_calculated}}</p>
                            <p>NUMERO DE PRESTAMOS:{{ calculos.payable_liquid_calculated}}</p>
                            <p>PORCENTAJE DE PAGO:{{ calculos.payable_liquid_calculated}}</p>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="4" >
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <v-toolbar-title>DESEMBOLSO</v-toolbar-title>
                            <br>
                            <v-select
                              dense
                              :items="tipo_desembolso"
                              item-text="name"
                              item-value="value"
                              label="TIPO DE DESEMBOLSO"
                             ></v-select>
                            <v-text-field
                              label="NRO DE CHEQUE"
                            ></v-text-field>
                            <v-text-field
                              label="NRO DE DESEMBOLSO"
                            ></v-text-field>
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
  name: "specific-data-loan",
  props: {
    datos: {
      type: Object,
      required: true
    },
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
   data: () => ({
    tipo_desembolso:[
      {
      name:'Cheque',
      value:1
      },
      {
      name:'Deposito Bancario',
      value:2
      },
      {
      name:'Efectivo',
      value:3
      }
    ],
    loan:{}
  }),
  mounted() {
    this.getLoan()
  },
  methods:{
     async getLoan() {
      try {
        this.loading = true
        let res = await axios.get(`loan/${2}`)
        this.loan = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>