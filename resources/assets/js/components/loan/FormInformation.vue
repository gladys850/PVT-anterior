<template>
  <v-container fluid >
    <v-card>
    <v-row justify="center">
      <v-col cols="12"  >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="2" class="py-0">
              <label> Tipo de Deposito:</label>
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                dense
                v-validate="'required'"
                v-model="loanTypeSelected"
                data-vv-name="payment_types"
                :onchange="Onchange()"
                :items="payment_types"
                item-text="name"
                item-value="id"
              ></v-select>
            </v-col>
             <v-col cols="12" md="2" class="py-0" v-show="visible">
              <label> Nro de Cuenta:</label>
            </v-col>
            <v-col cols="12" md="4"  v-show="visible">
              <v-text-field
                dense
                outlined
                v-validate.initial="'numeric|min:1|max:20'"
                :error-messages="errors.collect('cuenta')"
                data-vv-name="cuenta"
                v-model="cuenta"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" v-show="espacio"></v-col>
            <v-col cols="12" md="2" class="py-0">
              <label> Destino del Prestamo:</label>
            </v-col>
            <v-col cols="12" md="6"  >
               <v-select
                dense
                v-validate="'required'"
                data-vv-name="destino"
                v-model="loanTypeSelected2"
                :items="destino"
                item-text="name"
                item-value="id"
              ></v-select>
            </v-col>
          </v-row>
        </v-container>
      </v-col>
    </v-row>
    </v-card>
  </v-container>
</template>
<script>
import { Validator } from 'vee-validate'
  export default {
  inject: ['$validator'],
  name: "loan-information",
  data: () => ({
    cuenta:null,
    destino:[],
    visible:false,
    espacio:true,
    loanTypeSelected:null,
    loanTypeSelected2:null,
    payment_types:[]
  }),
  props: {
    formulario: {
      type: Array,
      required: true
    },
    datos: {
      type: Array,
      required: true
    },
  },
  beforeMount(){
    this.getPaymentTypes()
  },
  mounted(){
    this.getLoanDestiny()
  },
  methods: {
   Onchange(){
      for (this.i = 0; this.i< this.payment_types.length; this.i++) {
        if(this.loanTypeSelected==this.payment_types[this.i].id)
          {
            if(this.payment_types[this.i].name=='Deposito Bancario')
              {
                this.visible=true,
                this.espacio=false
              }else{
                this.visible=false,
                this.espacio=true
              }
          }
      }
        this.formulario[0]=this.loanTypeSelected,
        this.formulario[1]=this.cuenta,
        this.formulario[2]=this.loanTypeSelected2
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
     async getLoanDestiny() {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${9}/loan_destiny`)
        this.destino = res.data
        console.log(this.destino+'estos son los destinos');
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>