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
                        <v-col cols="3" class="ma-0 pb-0">
                          <label>TIPO DE AMORTIZACION:</label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <v-select
                            dense
                            :items="tipo_de_amortizacion"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="1" class="ma-0 pb-0"></v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label>TIPO DE PAGO:</label>
                        </v-col>
                        <v-col cols="3">
                          <v-select
                            dense
                            :items="payment_types"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <label>NRO DE COMPROBANTE:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                         <v-text-field
                          dense
                               ></v-text-field>
                       </v-col>
                      <v-col cols="1">
                      </v-col>
                      <v-col cols="2" class="ma-0 pb-0">
                        <label> FECHA DE PAGO:</label>
                      </v-col>
                      <v-col cols="3">
                      </v-col>
                      <v-col cols="4" class="ma-0 pb-0">
                        <v-text-field
                          dense
                          label="Total Pagado"
                          ></v-text-field>
                      </v-col>
                      <v-col cols="8" class="ma-0 pb-0">
                        <v-text-field
                          dense
                            label="Glosa"
                          ></v-text-field>
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
      {name:"Garante",
      id:3
      }
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