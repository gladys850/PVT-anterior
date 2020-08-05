<template>
  <div>
    <v-stepper v-model="e1" >
      <v-stepper-header class=" !pa-0 ml-0" >
        <template>
          <v-stepper-step editable
            :key="`${1}-step`"
            :complete="e1 > 1"
            :step="1">Modalidad
          </v-stepper-step>
          <v-divider v-if="1 !== steps" :key="1" ></v-divider>
          <v-stepper-step editable
            :key="`${2}-step`"
            :complete="e1 > 2"
            :step="2">Calculo
          </v-stepper-step>
          <v-divider v-if="2 !== steps" :key="2" ></v-divider>
         </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content :key="`${1}-content`" :step="1">
          <v-card color="grey lighten-1">
            <AddAmortization
            :data_payment.sync="data_payment"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer> <v-spacer></v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn
                    color="primary"
                    @click="nextStep(1)" v-show="!ver">
                    Siguiente
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content :key="`${2}-content`" :step="2" >
          <v-card color="grey lighten-1">
              <AddPayment
                :payment.sync="payment"
                :data_payment.sync="data_payment"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer><v-spacer> </v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                  @click="beforeStep(2)">Atras</v-btn>
                  <v-btn right
                    color="primary"
                    @click="savePayment()">
                    Siguiente
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </div>
</template>
<style >
.v-expansion-panel-content__wrap {
    padding: 0 0px 0px;
}
.v-stepper__content {
    padding: 0px 0px 0px;
}
</style>
<script>
import AddAmortization from '@/components/payment/AddAmortization'
import AddPayment from '@/components/payment/AddPayment'

export default {
  name: "payment-steps",
  components: {
    AddAmortization,
    AddPayment
  },
   data: () => ({
    bus: new Vue(),
    e1: 1,
    steps: 2,
    payment:{
      estimated_date:{}
    },
    data_payment:{},
  }),
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    },
    ver(){
       return  this.$route.params.hash == 'view'
    },
  },
  watch: {
    steps (val) {
      if (this.e1 > val) {
        this.e1 = val
      }
    },
  },
  methods: {
    nextStep (n) {
      if (n == this.steps) {
        this.e1 = 1
      }
      else {
        if(n==1)
        {
          if(!this.isNew)
          {
          this.savePaymentTreasury()
          
               console.log('entro por verdad')
          }
          else{
this.Calcular()
               console.log('entro por falso')
          }
        }
        if(n==2)
        {
               console.log('entro a guardar payments')
        }
        this.e1 = n + 1
     }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
    //Metodo para la datos de la calculadora

      async savePaymentTreasury() {
      try {
          console.log('entro a grabar tesoreria')
          let res = await axios.post(`loan_payment/${4}/voucher`,{
            payment_type_id:this.data_payment.pago,
            voucher_type_id:2,
            voucher_number:this.data_payment.comprobante,
            description:this.data_payment.glosa
          })
            this.$router.push('/payment')
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    async savePayment(){
       try {
 if(this.data_payment.amortization==1)
        {
          console.log('entro a grabar'+this.data_payment.payment_date+this.data_payment.pago_total)

          let res = await axios.post(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:false
          })
            printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            base64: true
          })
          this.$router.push('/payment')
           console.log('este es el resultado'+res.data)
           this.payment = res.data
        }
        else{
            let res = await axios.post(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:true
          })
             printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            base64: true
          })
           this.payment = res.data
            this.$router.push('/payment')
           }
    
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }

    },
    async Calcular() {
      try {
       if(this.data_payment.amortization==1)
        {
          console.log('entro por uno')
          let res = await axios.patch(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:false
          })
           this.payment = res.data
        }
        else{
            let res = await axios.patch(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:true
          })
           this.payment = res.data
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
  },
}
</script>