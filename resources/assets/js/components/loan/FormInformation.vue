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
              <label> Tipo de Depositos:</label>
            </v-col>
            <v-col cols="12" md="3" class="py-0">
              <ValidationProvider v-slot="{ errors }" name="Tipo Desembolso" rules="required">
              <v-select
                class="py-0"
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
            <v-col cols="12" md="4" class="py-0"  v-show="visible">
              <ValidationProvider v-slot="{ errors }" name="cuenta" rules="numeric|min:1|max:20"  mode="aggressive">
              <v-text-field
                class="py-0"
                :error-messages="errors"
                dense
                outlined
                v-model="cuenta"
              ></v-text-field>
              </ValidationProvider>
            </v-col>
            <v-col cols="12" md="6" v-show="espacio"></v-col>
            <v-col cols="12" md="2" class="py-1">
              <label> Destino del Prestamo:</label>
            </v-col>
            <v-col cols="12" md="6"  >
                <ValidationProvider v-slot="{ errors }" name="destino" rules="required">
               <v-select
                class="py-0"
                :error-messages="errors"
                v-model="loanTypeSelected2"
                dense
                :items="destino"
                item-text="name"
                item-value="id"
              ></v-select>
                </ValidationProvider>
            </v-col>
          </v-row>
        </v-container>


         <v-container class="py-0" v-show="modalidad_personal_reference">
          <v-row>
            <v-col cols="12" md="12">
             <v-toolbar-title> REFERENCIA PERSONAL</v-toolbar-title>
            </v-col>
             <v-col cols="12" md="3">
             <v-text-field
                  v-model="personal_reference.first_name"
                  dense
                  label="Primer Nombre"
             ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
             <v-text-field
                  v-model="personal_reference.second_name"
                  dense
                  label="Segundo Nombre"
             ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
             <v-text-field
                  v-model="personal_reference.last_name"
                  dense
                  label="Primer Apellido"
             ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
             <v-text-field
                  v-model="personal_reference.mothers_last_name"
                  dense
                  label="Segundo Apellido"
             ></v-text-field>
            </v-col>
             <v-col cols="12" md="3">
              <v-text-field
                v-model="personal_reference.identity_card"
                dense
                label="Cédula de Identidad"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                v-model="personal_reference.city_identity_card_id"
                dense
                :items="cities"
                item-text="name"
                item-value="id"
                label="Ciudad de Expedición"
              ></v-select>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                v-model="personal_reference.phone_number"
                dense
                label="Telefono"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
              <v-text-field
                v-model="personal_reference.cell_phone_number"
                dense
                label="Celular"
              ></v-text-field>
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
    props: {
    formulario: {
      type: Array,
      required: true
    },
    modalidad_personal_reference: {
      type: Boolean,
      required: true,
      default: false
    },
    personal_reference: {
      type: Object,
      required: true
    }, 
    prueba: {
      type: Array,
      required: true
    },
      calculos: {
      type: Object,
      required: true
    },
      intervalos: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    cuenta:null,
    destino:[],
    visible:false,
    espacio:true,
    loanTypeSelected:null,
    loanTypeSelected2:null,
    payment_types:[],
    cities:[]
  }),
   watch: {
  ver()
  {
    modalidad
  }
  },
  beforeMount(){
    this.getCities()
    this.getPaymentTypes()
   
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
        this.getLoanDestiny()
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
    async getCities() {
    try {
      this.loading = true
      let res = await axios.get(`city`)
      this.cities = res.data
    } catch (e) {
      this.dialog = false
      console.log(e)
    }finally {
        this.loading = false
      }
    },
     async getLoanDestiny() {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${this.intervalos.procedure_type_id}/loan_destiny`)
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