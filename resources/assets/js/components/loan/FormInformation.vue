<template>
  <v-container fluid >
    <v-card>
    <ValidationObserver ref="observer">
    <v-form>
    <v-row justify="center">
      <v-col cols="12"  >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="2" class="py-0">
              <label> Tipo de Deposito:</label>
            </v-col>
            <v-col cols="12" md="3">
              <ValidationProvider v-slot="{ errors }" name="Tipo Desembolso" rules="required">
              <v-select
                :error-messages="errors"
                dense
                v-model="loanTypeSelected"
                :onchange="Onchange()"
                :items="payment_types"
                item-text="name"
                item-value="id"
              ></v-select>
              </ValidationProvider>
            </v-col>
             <v-col cols="12" md="2" class="py-0" v-show="visible">
              <label> Nro de Cuenta:</label>
            </v-col>
            <v-col cols="12" md="4"  v-show="visible">
              <ValidationProvider v-slot="{ errors }" name="cuenta" rules="numeric|min:1|max:20"  mode="aggressive">
              <v-text-field
                :error-messages="errors"
                dense
                outlined
                v-model="cuenta"
              ></v-text-field>
              </ValidationProvider>
            </v-col>
            <v-col cols="12" md="6" v-show="espacio"></v-col>
            <v-col cols="12" md="2" class="py-0">
              <label> Destino del Prestamo:</label>
            </v-col>
            <v-col cols="12" md="6"  >
                <ValidationProvider v-slot="{ errors }" name="destino" rules="required">
               <v-select
                :error-messages="errors"
                dense
                :items="destino"
                item-text="name"
                item-value="id"
              ></v-select>
                </ValidationProvider>
            </v-col>
          </v-row>
        </v-container>
      </v-col>
    </v-row>
    </v-form>
    </ValidationObserver>
    </v-card>
  </v-container>
</template>
<script>
  export default {
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
  },
  beforeMount(){
    this.getPaymentTypes()
     this.getLoanDestiny()
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