<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
        <div v-if="$route.params.workTray == 'received' || $route.params.workTray == 'my_received' || $route.params.workTray == 'validated'">
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                dark
                x-small
                :color="'error'"
                top
                right
                absolute
                v-on="on"
                style="margin-right: 45px;"
                @click.stop="resetForm()"
                v-show="editable"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
            <div>
              <span>Cancelar</span>
            </div>
          </v-tooltip>
        
          <v-tooltip top v-if="$store.getters.permissions.includes('disbursement-loan')">
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                dark
                x-small
                :color="editable ? 'danger' : 'success'"
                top
                right
                absolute
                v-on="on"
                style="margin-right: -9px;"
                @click.stop="editLoan()"
              >
                <v-icon v-if="editable">mdi-check</v-icon>
                <v-icon v-else>mdi-pencil</v-icon>
              </v-btn>
            </template>
            <div>
              <span v-if="editable">Guardar</span> 
              <span v-else>Editar</span>
            </div>
          </v-tooltip>
        </div>
        <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="4" color="orange">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3" max-width="100%">
                          <v-toolbar-title><b>PRÉSTAMO</b></v-toolbar-title>
                         <v-progress-linear></v-progress-linear>
                        
                          <br>
                          <p><b>PLAZO EN MESES:</b>{{' '+loan.loan_term}}</p>
                          <p><b>MONTO SOLICITADO:</b>{{' '+loan.amount_approved}} Bs.</p>
                          <p><b>PROMEDIO LIQUIDO PAGABLE</b>{{' '+loan.lenders[0].pivot.payable_liquid_calculated}} Bs.</p>
                          <p><b>TOTAL BONOS:</b>{{' '+loan.lenders[0].pivot.bonus_calculated}}</p>
                          <p><b>LIQUIDO PARA CALIFICACION:</b>{{' '+loan.liquid_qualification_calculated}} Bs.</p>
                          <p><b>CALCULO DE CUOTA:</b>{{' '+loan.estimated_quota}} Bs.</p>
                          <p><b>INDICE DE ENDEUDAMIENTO:</b>{{' '+loan.indebtedness_calculated}} Bs.</p>
                          <div v-for="procedure_type in procedure_types" :key="procedure_type.id">
                          <div v-if="procedure_type.name === 'Préstamo hipotecario'">
                          <v-progress-linear></v-progress-linear><br>
                           <p style="color:teal"><b>CODEUDOR</b></p>
                          <div v-for="(lenders,i) in loan.lenders" :key="i">
                          <div  v-if="(lenders,i)>0">
                          <p><b>PROMEDIO LIQUIDO PAGABLE:</b>{{' '+lenders.pivot.payable_liquid_calculated}}</p>
                          <p><b>TOTAL BONOS:</b>{{' '+lenders.pivot.bonus_calculated}}</p>
                          </div>
                          </div>
                          </div>
                          </div>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap >
                        <v-flex xs12 class="px-2" >
                          <fieldset class="pa-3" max-width="100%">
                            <v-toolbar-title><b>GARANTIA</b></v-toolbar-title>
                              <v-progress-linear></v-progress-linear>
                              <div v-for="procedure_type in procedure_types" :key="procedure_type.id" >
                              <ul style="list-style: none" class="pa-0" v-if="procedure_type.name == 'Préstamo a largo plazo' || procedure_type.name == 'Préstamo a corto plazo'">
                              <li v-for="guarantor in loan.guarantors" :key="guarantor.id">
                                <br>
                                <p><b>CÉDULA DE IDENTIDAD:</b> {{guarantor.identity_card +" "+ identityCardExt(guarantor.city_identity_card_id) }}</p>
                                <p><b>NOMBRE:</b> {{$options.filters.fullName(guarantor, true)}}</p>
                                <p><b>TELEFONO:</b> {{guarantor.cell_phone_number}}</p>
                                <p><b>PORCENTAJE DE PAGO:</b> {{guarantor.pivot.payment_percentage}} %</p>
                              </li>
                               <p v-if="loan.guarantors.length==0">NO TIENE GARANTES</p>
                            </ul>
                            <ul style="list-style: none" class="pa-0" v-if="procedure_type.name == 'Préstamo hipotecario'">                                
                                <br>
                                <p><b>CIUDAD: </b>{{ loan_properties.land_lot_number }}</p>
                                <p><b>UBICACION: </b>{{ loan_properties.location}} </p>
                                <p><b>NUMERO DE LOTE: </b>{{ loan_properties.land_lot_number }} </p>
                                <p><b>SUPERFICIE: </b>{{ loan_properties.surface }} - {{ loan_properties.measurement }}</p>
                                <p><b>CODIGO CATASTRAL: </b>{{ loan_properties.cadastral_code}}</p>
                                <p><b>NRO MATRICULA: </b>{{ loan_properties.registration_number}}</p>
                                <p><b>NRO FOLIO REAL: </b>{{ loan_properties.real_folio_number}} </p>
                                <p><b>VNR: </b>{{ loan_properties.net_realizable_value}} </p>

                            </ul>
                            <ul style="list-style: none" class="pa-0" v-if="procedure_type.name == 'Préstamo Anticipo'">
                              <p> NO TIENE GARANTES</p>
                            </ul>
                            </div>
                            <br>

                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="4" >
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3" mix-width="100%">
                            <v-toolbar-title><b>DESEMBOLSO</b></v-toolbar-title>
                              <v-progress-linear></v-progress-linear>
                            <br>
                              <p><b>ENTIDAD FINANCIERA:</b>{{' '+cuenta}}</p>
                              <p><b>NUMERO DE CUENTA:</b>{{' '+loan.lenders[0].account_number}}</p>
                              <p><b>CUENTA SIGEP:</b> {{' '+loan.lenders[0].sigep_status}}</p>
                            <v-progress-linear></v-progress-linear>
                            <br>
                            <v-text-field
                              dense
                              v-model="loan.disbursement_date"
                              label="FECHA DE DESEMBOLSO"
                              hint="Día/Mes/Año"
                              type="date"
                              :outlined="editable"
                              :readonly="!editable"
                            ></v-text-field>
                            <v-select
                              dense
                              :outlined="editable"
                              :readonly="!editable"
                              :items="payment_types"
                              item-text="name"
                              item-value="id"
                              label="TIPO"
                              v-model="loan.payment_type_id"
                             ></v-select>
                            
                            <div v-if="loan.payment_type_id=='1'">
                            <v-text-field
                              :outlined="editable"
                              :readonly="!editable"
                              :label="'NRO DE DEPOSITO'"
                              @click="desembolso()"
                              v-model="loan.number_payment_type"
                              
                            ></v-text-field>
                             </div>
                              <div v-if="loan.payment_type_id!='1'">
                            <v-text-field
                              :outlined="editable"
                              :readonly="!editable"
                                @click="desembolso()"
                              :label="loan.payment_type_id=='2'? 'NRO DE CHEQUE':loan.payment_type_id=='3'?'NRO DE RECIBO':'OTRO'"
                              v-model="loan.number_payment_type"
                            ></v-text-field>
                             </div>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "specific-data-loan",
  props: {
    loan: {
      type: Object,
      required: true
    },
    loan_properties: {
      type: Object,
      required: true
    },
    procedure_types: {
      type: Object,
      required: true
    },
    /*validate:{
      type: Object,
      required: false
    }*/
  },
   data: () => ({
    editable: false,
    reload: false,
    payment_types:[],
    city: [],
    entity: [],
    entities:null,
  }),
  beforeMount(){
    this.getPaymentTypes()
    this.getCity()                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
    this.getEntity()
  },
  computed: {
    cuenta() {
       for (this.i = 0; this.i< this.entity.length; this.i++) {
        if(this.loan.lenders[0].financial_entity_id==this.entity[this.i].id)
        {
          this.entities= this.entity[this.i].name
        }
      }
      return this.entities
    }
  },
  methods:{
    desembolso () {
      if(this.loan.payment_type_id=='1'){
      this.loan.number_payment_type = this.loan.lenders[0].account_number;
      }else{
      this.loan.number_payment_type = null
    }
    },
    async getEntity() {
      try {
        this.loading = true
        let res = await axios.get(`financial_entity`)
        this.entity = res.data
        console.log("ciudad "+ this.entity)

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getCity() {
      try {
        this.loading = true
        let res = await axios.get(`city`)
        this.city = res.data
        console.log("ciudad "+ this.city)

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    identityCardExt(id){
      let ext
      if(id != null){
        for(let i=0; i<this.city.length;i++){
          if(this.city[i].id == id){
            ext = this.city[i].first_shortened
          }  
        }
      return ext
      }else{
        return ''
      }
    },
    /*identityCardExt(id){
      let ext
      if(id != null){
      ext = this.city.find(o => o.id == id).first_shortened
      console.log( ext)
      return ext
      }else{
        return ''
      }
    },*/
    resetForm() {
      this.editable = false
      this.reload = true
      this.$nextTick(() => {
      this.reload = false
      })
    },
    async editLoan(){
      try {
        if (!this.editable) {
          this.editable = true
          console.log('entro al grabar por verdadero :)')
        } else {
          console.log('entro al grabar por falso :)')
          //Edit desembolso
            let res = await axios.patch(`loan/${this.loan.id}`, {
            disbursement_date:this.loan.disbursement_date,
            payment_type_id: this.loan.payment_type_id,
            number_payment_type: this.loan.number_payment_type
          })
            this.toastr.success('Se registró correctamente.')
            this.editable = false
            /* if((this.loan.disbursement_date != '' && this.loan.number_payment_type != '') && (this.loan.disbursement_date != null && this.loan.number_payment_type != null)){
               this.validate.valid_disbursement = true
             }else{
               this.validate.valid_disbursement = false
             }*/
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
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
  }
}
</script>