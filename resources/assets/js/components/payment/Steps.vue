<template>
  <div>
    <v-stepper v-model="e1" >
      <v-stepper-header class=" !pa-0 ml-0" >
        <template>
          <v-stepper-step
            :key="`${1}-step`"
            :complete="e1 > 1"
            :step="1">Modalidad
          </v-stepper-step>
          <v-divider v-if="1 !== steps" :key="1" ></v-divider>
          <v-stepper-step
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
            :data_payment.sync="data_payment"
            :loan_payment.sync="loan_payment"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer> <v-spacer></v-spacer> <v-spacer></v-spacer><v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn color="seccundary"
                    @click="atras()"  v-show="!isNew">
                    Atras
                  </v-btn>
                  <v-btn
                    color="primary"
                    @click="validatedStepOne()" v-show="!ver">
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
      estimated_days:{
        penal:null
      }
    },
    data_payment:{
      payment_date:null,
      pago_total: null
    },
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
  mounted() {
    if(this.$route.params.hash == 'edit')
    {
      this.getLoanPayment(this.$route.query.loan_payment)
    }
     if(this.$route.params.hash == 'view')
    {
      this.getLoanPayment(this.$route.query.loan_payment)
    }
  },
  methods: {
    atras(){
       try {
        this.loading = true
     //   let res = await axios.get(`loan_payment/${this.$route.query.loan_payment}`)
     //  this.loan_payment = res.data
     //   this.$router.push('/workflow/'+this.loan_payment.loan_id)
       this.$router.push('/loanPayment')
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
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
          }
          else{
            this.Calcular()
          }
        }
        this.e1 = n + 1
     }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
    //Metodo para el creado del voucher
      async savePaymentTreasury() {
      try {
          console.log('entro a grabar tesoreria')
               let res1 = await axios.patch(`loan_payment/${this.$route.query.loan_payment}`,{
            validated:true
          })
          let res = await axios.post(`loan_payment/${this.$route.query.loan_payment}/voucher`,{
            payment_type_id:this.data_payment.pago,
            voucher_type_id:2,
            voucher_number:this.data_payment.comprobante,
            description:this.data_payment.glosa
          })
            this.$router.push('/loanPayment')
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Metodo para crear el Pago
    async savePayment(){
      try {
        if(this.data_payment.amortization==1)
        {
          let res = await axios.post(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:false,
            voucher:this.data_payment.voucher,
            payment_type_id:this.data_payment.pago,
            affiliate_id:this.data_payment.affiliate_id_paid_by,
            paid_by:this.data_payment.affiliate_id,
            procedure_modality_id:this.data_payment.procedure_modality_id
          })
            printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            base64: true
          })
          this.$router.push('/loanPayment')
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
            this.$router.push('/loanPayment')
           }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }

    },
     //Metodo para sacar datos del pago
     async getLoanPayment(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan_payment/${id}`)
        this.loan_payment = res.data
console.log('este es el loan')
console.log(this.loan_payment)
        this.data_payment.code=this.loan_payment.code
        this.data_payment.payment_date= this.loan_payment.estimated_date
        this.data_payment.pago_total=this.loan_payment.estimated_quota
        this.data_payment.affiliate_id =this.loan_payment.paid_by
        this.data_payment.voucher=this.loan_payment.voucher
        this.data_payment.pago  =this.loan_payment.payment_type_id

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo calculo de siguiente cuota
    async Calcular() {
      try {
       if(this.data_payment.amortization==1)
        {
          let res = await axios.patch(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:false,
            voucher:this.data_payment.voucher,
            payment_type_id:this.data_payment.pago,
            affiliate_id:this.data_payment.affiliate_id_paid_by,
            paid_by:this.data_payment.affiliate_id,
            procedure_modality_id:this.data_payment.procedure_modality_id
          })
           this.payment = res.data
        }
        else{
            let res = await axios.patch(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:this.data_payment.payment_date,
            estimated_quota:this.data_payment.pago_total,
            liquidate:true,
            voucher:this.data_payment.voucher,
            payment_type_id:this.data_payment.pago ,
            affiliate_id:this.data_payment.affiliate_id_paid_by,
            paid_by:this.data_payment.affiliate_id,
            procedure_modality_id:this.data_payment.procedure_modality_id
          })
           this.payment = res.data
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    async validatedStepOne() {
      try {
           if(!this.isNew)
          {
            this.savePaymentTreasury()
            //this.toastr.error("Paso a tesoreria")
          }
          else{
           // this.toastr.error("Esta en cobranzas")
            this.Calcular()
            this.nextStep(1)
          }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    }
  },
}
</script>