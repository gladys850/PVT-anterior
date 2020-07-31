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
             <v-card class="ma-3">
            <AddAmortization
            :data_payment.sync="data_payment"/>
             </v-card>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer> <v-spacer></v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn
                    color="primary"
                    @click="nextStep(1)">
                    Siguiente
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content :key="`${2}-content`" :step="2" >
          <v-card color="grey lighten-1">
              <v-card class="ma-3">
              <AddPayment
                :payment.sync="payment"
                :data_payment.sync="data_payment"/>
              </v-card>
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
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    addresses: {
      type: Array,
      required: true
    }
  },
  components: {
    AddAmortization,
    AddPayment
  },
   data: () => ({
    bus: new Vue(),
    e1: 1,
    steps: 2,
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
        if(n==1)
        {
          this.Calcular()
               console.log('entro a calcular')
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
    async savePayment(){
       try {
          let res = await axios.post(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:'2020-10-15',
            estimated_quota:500,
            liquidate:false

          })
        this.payment = res.data
console.log("este son los datos del payment"+payment)
      this.$router.push('/workflow')
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }

    },
    async Calcular() {
      try {
          let res = await axios.patch(`loan/${this.$route.query.loan_id}/payment`,{
            estimated_date:'2020-10-15',
            estimated_quota:500,
            liquidate:false

          })
        this.payment = res.data
console.log("este son los datos del payment"+payment)
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
  },
}
</script>