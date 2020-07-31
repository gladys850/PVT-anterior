<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <v-card-text class="ma-0 pb-0">
          <v-container fluid class="ma-0 pb-0">
            <v-row justify="center" class="ma-0 pb-0">
              <v-col cols="12" class="ma-0 pb-0">
                <v-container class="py-0">
                  <ValidationObserver ref="observer">
                    <v-form>
                      <template>
                      <v-row>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>CODIGO:{{data_payment}}</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{ payment.code}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>FECHA:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{ payment.estimated_date}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>NUMERO DE CUOTA:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{ payment.quota_number}}
                        </v-col>
                        <v-col cols="12" class="ma-0 pb-0">
                          <label>DIAS ESTIMADOS:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>PENAL:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.estimated_days.penal}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>ACUMULADO:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.estimated_days.accumulated}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>CORRIENTE:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.estimated_days.current}}
                        </v-col>
                            <v-col cols="2" class="ma-0 pb-0">
                          <label>BALANCE:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.balance}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>PAGO AL CAPITAL:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.capital_payment}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>INTERES DEL PAGO:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.interest_payment}}
                        </v-col>
                           <v-col cols="2" class="ma-0 pb-0">
                          <label>CUOTA ESTIMADA:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.estimated_quota}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>SIGUIENTE BALANCE:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.next_balance}}
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>PENAL RESTANTE:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                        {{payment.penal_remaining}}
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <label>RESTANTE ACUMULADO:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                     {{payment.accumulated_remaining}}
                       </v-col>
                      <v-col cols="1">
                      </v-col>
                    </v-row>
                    </template>
                  </v-form>
                </ValidationObserver>
              </v-container>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "loan-requirement",
  props: {
    payment: {
      type: Object,
      required: true
    },
    data_payment: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    observation: {},
    tipo_de_pago: [
      {name:"Efectivo",
      id:1
      },
      {name:"Dep. en Cuenta",
      id:2
      },
      {name:"Cheque",
      id:3
      },
      {name:"Garante",
      id:4
      }
    ],
    tipo_de_amortizacion: [
      {name:"Regular",
      id:1
      },
      {name:"Total",
      id:2
      },
    ],
     payment_types:[],
    dates: {
      disbursementDate:{
            formatted: null,
            picker: false
          }
      },
    observation_type: [],
    flow: {},
    valArea: [],
    areas: []
  }),
  
 beforeMount(){
    this.getPaymentTypes()
   
  },


  methods: {

      async getLoan() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_types = res.data
        console.log(this.payment_types+'este es el tipo de desembolso');
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

        async getPaymentTypes() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_types = res.data
        console.log(this.payment_types+'este es el tipo de desembolso');
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>