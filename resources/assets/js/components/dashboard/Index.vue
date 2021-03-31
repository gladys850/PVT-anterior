<template>
<v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Préstamos</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
      <v-card-text>
  <v-container  class="py-0 px-0">
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
        <v-row justify="center" >
          <v-col cols="12" md="4">
              <v-card>
                <v-container class="py-0">
                  <v-row>
                    <v-col cols="12" md="4"></v-col>
                    <v-col cols="12" md="6"> Afiliado </v-col>
                    <v-col cols="12" md="2"></v-col>
                    <v-col cols="12" md="1"></v-col>
                    <v-col cols="12" md="8">
                      <v-text-field
                        dense
                        label="CI ó Matrícula"
                        v-model="affiliate_ci"
                        class="py-0"
                        single-line
                        hide-details
                        clearable
                       
                       v-on:keyup.enter="getLoansHistory()"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            fab
                            dark
                            x-small
                            v-on="on"
                            color="info"
                            @click.stop="getLoansHistory()"
                          >
                            <v-icon>mdi-magnify</v-icon>
                          </v-btn>
                        </template>
                        <span>Buscar afiliado</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-col>
              <v-col cols="12" md="8">
              <v-card>
                <v-row class="ma-0 pb-0 text-uppercase">
                  <v-col
                    class="text-center"
                    cols="12"
                    md="8"
                    v-show="!exist_affiliate"
                    v-if="ver"
                  >
                    <h3 class="error--text aling-text--center">
                      <ul style="list-style: none" class="pa-0">
                        <li
                          v-for="(message, index) in loans.message"
                          :key="index"
                          class="pb-2"
                        >
                          {{ message }}
                        </li>
                      </ul>
                    </h3>
                  </v-col>
                  <template v-if="ver && exist_affiliate">
                    <v-col cols="12" md="2" class="ma-0 pb-0 text-left">
                      
                    </v-col>
                    <v-col cols="12" md="6" class="ma-0 pb-0 text-center">
                      <h2 style="color:teal">
                        {{ $options.filters.fullName(loans, true) }}
                      </h2></v-col>
                      <v-col cols="12" md="4" class="ma-0 py-0 text-left">
             
                      <v-tooltip
                        left              
                      >
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            dark
                            x-large
                            color="warning"
                            bottom
                            right                        
                            v-on="on" 
                            :to="{ name: 'affiliateAdd', params: { id: affiliate.id, workTray: 'received' }}" 
                          >
                            <v-icon>mdi-eye</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver datos del afiliado</span>
                      </v-tooltip>            
                  
                    </v-col>

                    <v-progress-linear></v-progress-linear>
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      C.I: {{ loans.identity_card }}</v-col
                    >
                    <v-col cols="12" md="6" class="ma-0 pb-0">
                      MATRÍCULA: {{ loans.registration }}
                    </v-col>
                    <template v-if="loans.tit_pvt">
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        CATEGORÍA: {{ category_name }}
                      </v-col>
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        ESTADO: {{ state_name_status }}</v-col>
                      <v-col cols="12" md="6" class="ma-0 pb-0">
                        GRADO: {{ degree_name }}
                      </v-col>
                      <v-col cols="12" md="6" class="ma-0 pb-2">
                        UNIDAD: {{ unit_name }}
                      </v-col>
                    </template>
                    <template v-if="loans.spouse_pvt || loans.spouse_sismu">
                      <v-col cols="12" md="6" class="ma-0 pb-2">
                        CONYUGUE
                      </v-col>
                    </template>

                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 py-0"
                    >
                      NRO DE PRÉSTAMOS SOLICITADOS:
                      {{ loans.loans.length }}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 pt-0 pb-1"
                    >
                      NRO DE PRÉSTAMOS GARANTIZADOS:
                      {{ loans.guarantees.length }}
                    </v-col>
                    <v-col
                      cols="12"
                      md="8"
                      class="font-weight-black caption ma-0 pt-0 pb-1 red--text"
                      v-if="loans.observables.length > 0"
                    >
                      <ul style="list-style: none" class="pa-0">
                        <li
                          v-for="(message, index) in loans.message"
                          :key="index"
                          class="pb-2"
                        >
                          {{ message }}
                        </li>
                      </ul>
                    </v-col>
                  </template>
                </v-row>
              </v-card>
            </v-col>

            <v-col cols="12" class="py-0 px-0 ">
              <v-container fluid class="py-0 px-0 ">
                <v-row class="py-0" v-if="ver && exist_affiliate">
                  <v-col cols="12" class="py-0">
                    <v-tabs dark active-class="secondary">
                      <v-tab>Información de Préstamos</v-tab>
                        <v-tab-item>
                          <DataLoanInformation
                            :loans.sync="loans"
                            :exist_affiliate.sync="exist_affiliate"
                            :ver.sync="ver"/>
                        </v-tab-item>
                      <v-tab>CALCULADORA</v-tab>
                        <v-tab-item >
                          <DataLoanAffiliate
                            :loans1.sync="loans1"
                            :largo_plazo.sync="largo_plazo"
                            :corto_plazo.sync="corto_plazo"
                            :anticipo.sync="anticipo"
                            :ver.sync="ver"
                            :evaluacion.sync="evaluacion"/>
                      </v-tab-item>
                    </v-tabs>
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
    </v-card-text>
  </v-card>
</template>
<script>
import DataLoanInformation from '@/components/dashboard/DataLoanInformation'
import DataLoanAffiliate from '@/components/dashboard/DataLoanAffiliate'

export default {
  name: "index",
  components: {
    DataLoanInformation,
    DataLoanAffiliate
  },

   data: () => ({
     civil_statuses: [
      { name: "Soltero", value: "S" },
      { name: "Casado", value: "C" },
      { name: "Viudo", value: "V" },
      { name: "Divorciado", value: "D" }
    ],
    items_measurement: [
      { name: "Metros cuadrados", value: "METROS CUADRADOS" },
      { name: "Hectáreas", value: "HECTÁREAS" }
    ],
    genders: [
      {
        name: "Femenino",
        value: "F"
      },
      {
        name: "Masculino",
        value: "M"
      }
    ],

loans:{},
affiliate_ci:null,
loading:false,
ver:false,
affiliate: {},
  degree_name: null,
    category_name: null,
    unit_name: null,
    state_name_status: null,
     exist_affiliate: false,

loans1:{
   message:{
        accomplished:null
      },
      state_affiliate:{
        name:null,
}},
       largo_plazo:{},
    corto_plazo:{},
    anticipo:{},
    evaluacion:false,



      dialog: false,
      dialog1: false,
      editedIndex: -1,
      editedItem: {},
      defaultItem: {},
      editedItem1: {},
      defaultItem1: {},
      headers: [
        {
          text: 'PRIMER NOMBRE',
          align: 'start',
          sortable: false,
          value: 'first_name',
        },
        { text: 'SEGUNDO NOMBRE',  value: 'second_name' },
        { text: 'PRIMER APELLIDO ', value: 'last_name' },
        { text: 'SEGUNDO APELLIDO ', value: 'mothers_last_name' },
        { text: 'TELEFONO', value: 'phone_number' },
        { text: 'CELULAR', value: 'cell_phone_number' },
        { text: 'DIRECCION ', value: 'address' },
        {
      text: "Actions",
      value: "actions",
      sortable: false
    }
      ],

    editable1: false,
    editable: false,
    reload: false,
    payment_types:[],
    city: [],
    entity: [],
    entities:null,
  }),

  watch: {
    affiliate_ci() {
      this.ver = false;
    },
  },
 
  methods:{

   async getLoansHistory() {
      try {
        this.getCalculator();
        this.loading = true;
        let message = [];
        let res = await axios.get(`affiliate_record`, {
          params: {
            ci: this.affiliate_ci,
          },
        });
        this.loans = res.data;
        message = this.loans.message[0];
        if (message != "afiliado-inexistente") {
          this.exist_affiliate = true;
          this.ver = true;
          if (this.loans.tit_pvt) {
            this.getAffiliate(this.loans.id);
          }
        } else {
          this.exist_affiliate = false;
          this.ver = true;
          console.log("no coincide");
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getCalculator() {
      try {
        this.loading = false;
         this.ver= true
          let res = await axios.post(`search_loan`, {
            identity_card : this.affiliate_ci });
            this.loans1=res.data
            this.cont=this.loans1.modalities.length
            for (let i = 0; i < this.loans1.modalities.length; i++)
             {
               if(this.loans1.modalities[i].modality_affiliate.procedure_type_id==9)
               {
                  this.anticipo.name=this.loans1.modalities[i].name_procedure_modality
                  this.anticipo.amount_max=this.loans1.modalities[i].amount_max
                  this.anticipo.maximum_term=this.loans1.modalities[i].modality_affiliate.procedure_type.interval.maximum_term
                  this.anticipo.liquid_calification=this.loans1.modalities[i].liquid_calification
                  this.anticipo.annual_interest=this.loans1.modalities[i].interest.annual_interest
               }
               if(this.loans1.modalities[i].modality_affiliate.procedure_type_id==10)
               {
                  this.corto_plazo.name=this.loans1.modalities[i].name_procedure_modality
                  this.corto_plazo.amount_max=this.loans1.modalities[i].amount_max
                  this.corto_plazo.maximum_term=this.loans1.modalities[i].modality_affiliate.procedure_type.interval.maximum_term
                  this.corto_plazo.liquid_calification=this.loans1.modalities[i].liquid_calification
                  this.corto_plazo.annual_interest=this.loans1.modalities[i].interest.annual_interest
               }
               if(this.loans1.modalities[i].modality_affiliate.procedure_type_id==11)
               {
                  this.largo_plazo.name=this.loans1.modalities[i].name_procedure_modality
                  this.largo_plazo.amount_max=this.loans1.modalities[i].amount_max
                  this.largo_plazo.maximum_term=this.loans1.modalities[i].modality_affiliate.procedure_type.interval.maximum_term
                  this.largo_plazo.liquid_calification=this.loans1.modalities[i].liquid_calification
                  this.largo_plazo.annual_interest=this.loans1.modalities[i].interest.annual_interest
               }

             }
            
            this.evaluacion=res.data.evaluate
              this.$forceUpdate()
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getAffiliate(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}`);
        this.affiliate = res.data;
        this.getDegree_name(this.affiliate.degree_id);
        this.getCategory_name(this.affiliate.category_id);
        this.getUnit_name(this.affiliate.unit_id);
        this.getState_name(id);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
      async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`degree/${id}`);
        this.degree_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`category/${id}`);
        this.category_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getUnit_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`unit/${id}`);
        this.unit_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getState_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/state`);
        this.state_name = res.data;
        this.state_name_type = this.state_name.affiliate_state_type.name;
        this.state_name_status = this.state_name.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },


  }
}
</script>