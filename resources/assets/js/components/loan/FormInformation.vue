<template>
  <v-container fluid>
    <v-card>
      <v-row justify="center">
        <v-col cols="12">
          <ValidationObserver ref="observerDestiny">
            <v-form>
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12" md="2" class="py-0">
                    <label>Tipo de Depósitos:</label>
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
                  <v-col cols="12" md="3" class="py-0" v-show="visible">
                      <v-text-field
                        label="Entidad Financiera"
                        class="py-0"
                        dense
                        v-model="entity"
                        readonly
                      ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3" class="py-0" v-show="visible">
                      <v-text-field
                        label="Cuenta"
                        class="py-0"
                        dense
                        v-model="affiliate.account_number"
                        readonly
                      ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6" v-show="espacio"></v-col>
                  <v-col cols="12" md="2" class="py-1">
                    <label>Destino del Préstamo:</label>
                  </v-col>
                  <v-col cols="12" md="6">
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
            </v-form>
          </ValidationObserver>

          <!--Referencia personal-->
          <ValidationObserver ref="observerPerRef">
            <v-form>
              <v-container class="py-0" v-show="modalidad_personal_reference">
                <v-row>
                  <v-col cols="12" md="12">
                    <v-toolbar-title>REFERENCIA PERSONAL</v-toolbar-title>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      vid="first_name"
                      name="Primer Nombre"
                      rules="required|alpha_spaces|min:3|max:20"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.first_name"
                        dense
                        label="Primer Nombre"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      vid="second_name"
                      name="Primer Nombre"
                      rules="alpha_spaces|min:3|max:20"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.second_name"
                        dense
                        label="Segundo Nombre"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="Primer Apellido"
                      rules="alpha_spaces|min:3|max:20"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.last_name"
                        dense
                        label="Primer Apellido"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="Segundo Apellido"
                      :rules="(personal_reference.last_name == null || personal_reference.last_name == '')? 'required' : ''+'alpha_spaces|min:3|max:20'"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.mothers_last_name"
                        dense
                        label="Segundo Apellido"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider v-slot="{ errors }" name="CI" rules="required|min:3">
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.identity_card"
                        dense
                        label="Cédula de Identidad"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="Ciudad de Expedición"
                      rules="required"
                    >
                      <v-select
                        :error-messages="errors"
                        v-model="personal_reference.city_identity_card_id"
                        dense
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        label="Ciudad de Expedición"
                      ></v-select>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="Teléfono"
                      rules="integer|min:7|max:12"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.phone_number"
                        dense
                        label="Telefono"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="Celular"
                      rules="integer|min:7|max:12"
                    >
                      <v-text-field
                        :error-messages="errors"
                        v-model="personal_reference.cell_phone_number"
                        dense
                        label="Celular"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </ValidationObserver>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>
<script>
import CoDebtor from "@/components/loan/CoDebtor";
export default {
  name: "loan-information",
  components: {
    CoDebtor
  },
  props: {
    loan_detail: {
      type: Object,
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
    intervalos: {
      type: Object,
      required: true
    },
    bus: {
      type: Object,
      required: true
    },
    modalidad_id: {
      type: Number,
      required: true,
      default: 0
    },
    affiliate: {
      type: Object,
      required: true
    }
  },
  data: () => ({  
    cuenta: null,
    destino:[],
    visible: false,
    espacio: true,
    loanTypeSelected: null,
    loanTypeSelected2: null,
    payment_types: [],
    cities: [],
    entity: null
  }),
  watch: {
    modalidad_id(newVal, oldVal){
      this.getLoanDestiny()
    },
    modalidad_personal_reference() {
      if (this.modalidad_personal_reference == false) {
        this.personal_reference = {};
      }
    }
  },
  beforeMount() {
    this.getCities();
    this.getPaymentTypes();
  },
  methods: {
    Onchange() {
      for (this.i = 0; this.i < this.payment_types.length; this.i++) {
        if (this.loanTypeSelected == this.payment_types[this.i].id) {
          if (this.payment_types[this.i].name == "Depósito Bancario") {
            this.visible = true
            this.espacio = false
            this.getEntity()
          } else {
            this.visible = false
            this.espacio = true
            
          }
        }
      }
      if(this.visible){
        this.loan_detail.payment_type_id = this.loanTypeSelected
        this.loan_detail.financial_entity_id = this.affiliate.financial_entity_id
        this.loan_detail.number_payment_type = this.affiliate.account_number
        this.loan_detail.destiny_id = this.loanTypeSelected2
      }else{
        this.loan_detail.payment_type_id = this.loanTypeSelected
        this.loan_detail.financial_entity_id = null
        this.loan_detail.number_payment_type = null
        this.loan_detail.destiny_id = this.loanTypeSelected2
      }
    },
    async getPaymentTypes() {
      try {
        this.loading = true;
        let res = await axios.get(`payment_type`);
        this.payment_types = res.data;
        console.log(this.payment_types + "este es el tipo de desembolso");
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getCities() {
      try {
        this.loading = true;
        let res = await axios.get(`city`);
        this.cities = res.data;
      } catch (e) {
        this.dialog = false;
        console.log(e);
      } finally {
        this.loading = false;
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
    async getEntity() {
      try {
        this.loading = true
        let res = await axios.get(`financial_entity/${this.affiliate.financial_entity_id}`)
        this.entity = res.data.name
        console.log("XXXXXXXXXXXXXXXXX")
        console.log(this.entity)
      } catch (e) {
        console.log(e)
      }finally {
          this.loading = false
        }
    },
    async validateDestiny() {
      try {
        let estado = false;
        estado = await this.$refs.observerDestiny.validate();
        if (estado) {
          this.bus.$emit("validDestiny", estado);
        } else {
          this.bus.$emit("validDestiny", estado);
        }
        console.log(" estado " + estado);
      } catch (e) {
        this.$refs.observerPerRef.setErrors(e);
      }
    },
    async validatePerRef() {
      try {
        let estado = false;
        estado = await this.$refs.observerPerRef.validate();
        if (estado) {
          this.bus.$emit("validPerRef", estado);
        } else {
          this.bus.$emit("validPerRef", estado);
        }
        console.log(" estado " + estado);
      } catch (e) {
        this.$refs.observerPerRef.setErrors(e);
      }
    },
  }
};
</script>