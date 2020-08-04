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
          </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content :key="`${1}-content`" :step="1">
          <v-card color="grey lighten-1">
            <AddTreasury
            :data_payment.sync="data_payment"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer> <v-spacer></v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn
                    color="primary"
                    @click="savePaymentTreasury()">
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
import AddTreasury from '@/components/payment/AddTreasury'

export default {
  name: "treasury-steps",
  components: {
    AddTreasury
  },
   data: () => ({
    bus: new Vue(),
    e1: 1,
    steps: 1,
    payment:{},
    data_payment:{},
  }),
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
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
            voucher_type_id:this.data_payment.voucher,
            voucher_number:this.data_payment.comprobante,
            description:this.data_payment.glosa
          })
            this.$router.push('/payment')
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    }
  },
}
</script>