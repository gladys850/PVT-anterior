<template>
  <v-container >
    <ValidationObserver ref="observer">
      <v-form>
        <v-card>
          <v-card-text >
            <v-row justify="center" class="ma-0 pb-0">
              <v-col cols="12" class="ma-0 pb-0">
                <v-container class="py-0">
                  <ValidationObserver ref="observer">
                    <v-form>
                      <template>
                      <v-row>
                        <v-col cols="3" class="ma-0 pb-0" v-show="!editable">
                          <label v-if="isNew" >TIPO DE AMORTIZACION:</label>
                          <label v-if="ver">TIPO DE COBRO:</label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="!editable">
                          <v-text-field
                            v-if="ver"
                            dense
                            label="Amortización Préstamos"
                            :readonly="true"
                          ></v-text-field>
                          <v-select
                            v-if="isNew"
                            dense
                            v-model="data_payment.amortization"
                            :outlined="!editable"
                            :readonly="editable"
                            :items="tipo_de_amortizacion"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="8" class="ma-0 pb-0" v-show="editable">
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable">
                          <label><strong> CODIGO DE PAGO: </strong></label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable">
                          <label><strong>{{loan_payment.code}}</strong></label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="editable">
                          <label>TIPO DE VOUCHER:</label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="editable">
                          <v-text-field
                            dense
                            label="Amortización Préstamos"
                            :outlined="!editable"
                            :readonly="editable"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="1" class="ma-0 pb-0"></v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label> FECHA DE PAGO:</label>
                        </v-col>
                        <v-col cols="3">
                          <v-menu
                            v-model="dates.paymentDate.show"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                            :disabled="editable || ver"
                          >
                            <template v-slot:activator="{ on }">
                              <v-text-field
                                dense
                                :outlined="isNew"
                                :readonly="editable || ver"
                                v-model="dates.paymentDate.formatted"
                                hint="Día/Mes/Año"
                                persistent-hint
                                append-icon="mdi-calendar"
                                v-on="on"
                              ></v-text-field>
                            </template>
                            <v-date-picker v-model="data_payment.payment_date" no-title @input="dates.paymentDate.show = false"></v-date-picker>
                          </v-menu>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <label>TOTAL PAGADO:</label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <v-text-field
                            dense
                            v-model="data_payment.pago_total"
                            :outlined="isNew"
                            :readonly="editable || ver"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="1">
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable" v-if="!ver">
                          <label >TIPO DE PAGO:</label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="editable" v-if="!ver">
                          <v-select
                            dense
                            v-model="data_payment.pago"
                            :onchange="Onchange()"
                            :outlined="editable"
                            :readonly="!editable"
                            :items="payment_types"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="efectivo">
                          <label  v-show="editable" v-if="!ver" >NRO COMPROBANTE:</label>
                        </v-col>
                        <v-col cols="3" v-show="efectivo">
                          <v-text-field
                            v-show="editable" v-if="!ver"
                            v-model="data_payment.comprobante"
                            :outlined="editable"
                            :readonly="!editable"
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6" class="ma-0 pb-0" >
                          <v-text-field
                            v-show="editable" v-if="!ver"
                            v-model="data_payment.glosa"
                            :outlined="editable"
                            :readonly="!editable"
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
        </v-card-text>
      </v-card>
    </v-form>
  </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "add-amortization",
  props: {
    data_payment: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loan: {},
    tipo_de_amortizacion: [
      {name:"Regular",
      id:1
      },
      {name:"Total",
      id:2
      }
    ],
    efectivo:false,
    loan_payment:{},
    payment_types:[],
    voucher_type:[],
    dates: {
      paymentDate:{
        formatted: null,
        picker: false,
      }
    },
  }),
   computed: {
    isNew() {
      return  this.$route.params.hash == 'new'
    },
    editable(){
       return  this.$route.params.hash == 'edit'
    },
    ver(){
       return  this.$route.params.hash == 'view'
    },
  },
  beforeMount(){
    this.getPaymentTypes()
    this.getVoucherTypes()
    if(this.$route.params.hash == 'view')
    {
      this.formatDate('paymentDate',this.data_payment.payment_date)
    }
      if(this.$route.params.hash == 'edit')
    {
      this.formatDate('paymentDate',this.data_payment.payment_date)
    }
  },
   watch: {
    'data_payment.payment_date': function(date) {
      this.formatDate('paymentDate', date)
    }
  },
  methods: {
    Onchange(){
      console.log('este es el dato'+this.data_payment.pago)
        if(this.data_payment.pago==3)
        {
          this.efectivo= false
        }else{
          this.efectivo= true
        }
    },
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
    //Metodo para sacar los tipos de voucher
     async getVoucherTypes() {
      try {
        this.loading = true
        let res = await axios.get(`voucher_type`)
        this.voucher_type = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para sacar los tipos de pago
    async getPaymentTypes() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_types = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>