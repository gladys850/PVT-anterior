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
        <v-row justify="center" class="py-0">
            <v-col cols="12" class="py-0">
              <v-container class="py-0">
                <v-row class="py-0">
                  <v-col cols="12" class="py-0">
                    <v-tabs dark active-class="secondary">
                      <v-tab>DATOS DEL PRESTAMO</v-tab>
                        <v-tab-item>
                          <v-card flat tile class="py-0">
                            <v-card-text class="py-0">
                              <v-col cols="12" md="12" color="orange">
                                <v-row>
                                  <v-col cols="12" md="12">
                                    <p style="color:teal"><b>TITULAR</b></p>
                                  </v-col>
                                  <v-col cols="12" md="4">
                                    <p><b>PLAZO EN MESES:</b>{{' '+loan.loan_term}}</p>
                                  </v-col>
                                  <v-col cols="12" md="4">
                                    <p><b>MONTO SOLICITADO:</b>{{' '+loan.amount_approved}} Bs.</p>
                                  </v-col>
                                  <v-col cols="12" md="4">
                                    <p><b>PROMEDIO LIQUIDO PAGABLE</b>{{' '+loan.lenders[0].pivot.payable_liquid_calculated}} Bs.</p>
                                  </v-col>
                                  <v-col cols="12" md="2">
                                    <p><b>TOTAL BONOS:</b>{{' '+loan.lenders[0].pivot.bonus_calculated}}</p>
                                  </v-col>
                                  <v-col cols="12" md="4">
                                    <p><b>LIQUIDO PARA CALIFICACION:</b>{{' '+loan.liquid_qualification_calculated}} Bs.</p>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <p><b>CALCULO DE CUOTA:</b>{{' '+loan.estimated_quota}} Bs.</p>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <p><b>INDICE DE ENDEUDAMIENTO:</b>{{' '+loan.indebtedness_calculated}} Bs.</p>
                                  </v-col>
                                  <v-col cols="12" md="12">
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
                                  </v-col>
                              </v-row>
                            </v-col>
                          </v-card-text>
                        </v-card>
                      </v-tab-item>
                    <v-tab>GARANTIA</v-tab>
                      <v-tab-item >
                        <v-card flat tile>
                          <v-card-text class="pa-0 py-0">
                            <v-col cols="12" class="mb-0 py-0">
                              <v-col cols="12" md="12" class="mb-0 py-0">
                                <v-card-text class="pa-0 mb-0">
                                  <div v-for="procedure_type in procedure_types" :key="procedure_type.id" class="pa-0 py-0" >
                                    <ul style="list-style: none" class="pa-0" v-if="procedure_type.name == 'Préstamo a largo plazo' || procedure_type.name == 'Préstamo a corto plazo'">
                                      <li v-for="guarantor in loan.guarantors" :key="guarantor.id">
                                        <v-col cols="12" md="12" class="pa-0">
                                          <v-row class="pa-0">
                                            <v-progress-linear></v-progress-linear><br>
                                            <v-col cols="12" md="12" class="py-0">
                                              <p style="color:teal"><b>GARANTE </b></p>
                                            </v-col>
                                            <v-col cols="12" md="3">
                                              <p><b>NOMBRE:</b> {{$options.filters.fullName(guarantor, true)}}</p>
                                            </v-col>
                                            <v-col cols="12" md="3">
                                              <p><b>CÉDULA DE IDENTIDAD:</b> {{guarantor.identity_card +" "+ identityCardExt(guarantor.city_identity_card_id) }}</p>
                                            </v-col>
                                            <v-col cols="12" md="3">
                                              <p><b>TELEFONO:</b> {{guarantor.cell_phone_number}}</p>
                                            </v-col>
                                            <v-col cols="12" md="3">
                                              <p><b>PORCENTAJE DE PAGO:</b> {{guarantor.pivot.payment_percentage}} %</p>
                                            </v-col>
                                          </v-row>
                                        </v-col>
                                      </li>
                                      <p v-if="loan.guarantors.length==0" style="color:teal"><b> NO TIENE GARANTES </b></p>
                                    </ul>
                                    <v-col cols="12" md="12" v-if="procedure_type.name == 'Préstamo hipotecario'">
                                      <v-row>
                                        <v-col cols="12" md="4">
                                          <v-text-field
                                            :outlined="editable"
                                            :readonly="!editable"
                                            :label="'CIUDAD'"
                                            dense
                                            v-model="loan_properties.land_lot_number"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'UBICACION'"
                                          dense
                                          v-model="loan_properties.location"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'NUMERO DE LOTE'"
                                          dense
                                          v-model="loan_properties.land_lot_number"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="1">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'SUPERFICIE'"
                                          dense
                                          v-model="loan_properties.surface"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="3">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          dense
                                          v-model="loan_properties.measurement "
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'CODIGO CATASTRAL'"
                                          dense
                                          v-model="loan_properties.cadastral_code"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'NRO MATRICULA'"
                                          dense
                                          v-model="loan_properties.registration_number"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          :outlined="editable"
                                          :readonly="!editable"
                                          :label="'NRO FOLIO REAL'"
                                          dense
                                          v-model="loan_properties.real_folio_number"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <p><b>VNR: </b>{{ loan_properties.net_realizable_value}} </p>
                                      </v-col>
                                    </v-row>
                                  </v-col>
                                  <ul style="list-style: none" class="pa-0" v-if="procedure_type.name == 'Préstamo Anticipo'">
                                    <p style="color:teal"> <b>NO TIENE GARANTES</b></p>
                                  </ul>
                                </div>
                              </v-card-text>
                            </v-col>
                          </v-col>
                          </v-card-text>
                        </v-card>
                      </v-tab-item>
                      <v-tab>DATOS PERSONA DE REFERENCIA</v-tab>
                        <v-tab-item >
                          <v-card flat tile>
                            <v-card-text>
                              <ul style="list-style: none" class="pa-0" >
                                <li v-for="personal_references in loan.personal_references" :key="personal_references.id">
                                  <v-col cols="12" md="12" class="pa-0">
                                    <p style="color:teal"><b>PERSONA DE REFERENCIA </b></p>
                                    <v-row class="pa-0">
                                      <v-progress-linear></v-progress-linear><br>
                                          <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'PRIMER NOMBRE'"
                                      dense
                                      v-model="personal_references.first_name"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'SEGUNDO NOMBRE'"
                                      dense
                                      v-model="personal_references.second_name"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'PRIMER APELLIDO'"
                                      dense
                                      v-model="personal_references.last_name"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'SEGUNDO APELLIDO'"
                                      dense
                                      v-model="personal_references.mothers_last_name"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'TELEFONO'"
                                      dense
                                      v-model="personal_references.phone_number"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="3">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'CELULAR'"
                                      dense
                                      v-model="personal_references.cell_phone_number"
                                    ></v-text-field>
                                  </v-col>
                                  <v-col cols="12" md="6">
                                    <v-text-field
                                      :outlined="editable"
                                      :readonly="!editable"
                                      :label="'DIRECCION'"
                                      dense
                                      v-model="personal_references.address"
                                    ></v-text-field>
                                  </v-col>
                                          </v-row>
                                        </v-col>
                                      </li>
                                  <p v-if="loan.personal_references.length==0" style="color:teal"> <b>NO TIENE CODEUDORES</b></p>
                                    </ul>
                              
                            
                            
                            </v-card-text>
                          </v-card>
                        </v-tab-item>
                        <v-tab>DATOS CODEUDOR</v-tab>
                          <v-tab-item >
                            <v-card flat tile>
                              <v-card-text>
                                <v-col cols="12" class="mb-0">
                                  <ul style="list-style: none" class="pa-0" >
                                      <li v-for="cosigners in loan.cosigners" :key="cosigners.id">
                                        <v-col cols="12" md="12" class="pa-0">
                                          <v-row class="pa-0">
                                            <v-progress-linear></v-progress-linear><br>
                                            <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'PRIMER NOMBRE'"
                                        dense
                                        v-model="loan.cosigners.first_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'SEGUNDO NOMBRE'"
                                        dense
                                        v-model="loan.cosigners.second_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'PRIMER APELLIDO'"
                                        dense
                                        v-model="loan.cosigners.last_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'SEGUNDO APELLIDO'"
                                        dense
                                        v-model="loan.cosigners.mothers_last_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'TELEFONO'"
                                        dense
                                        v-model="loan.cosigners.phone_number"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'CELULAR'"
                                        dense
                                        v-model="loan.cosigners.cell_phone_number"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'DIRECCION'"
                                        dense
                                        v-model="loan.cosigners.address"
                                      ></v-text-field>
                                    </v-col>
                                          </v-row>
                                        </v-col>
                                      </li>
                                      <p v-if="loan.cosigners.length==0" style="color:teal"> <b>NO TIENE CODEUDORES</b></p>
                                    </ul>
                                  <!--v-row>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'PRIMER NOMBRE'"
                                        dense
                                        v-model="loan.cosigners[0].first_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'SEGUNDO NOMBRE'"
                                        dense
                                        v-model="loan.cosigners[0].second_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'PRIMER APELLIDO'"
                                        dense
                                        v-model="loan.cosigners[0].last_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'SEGUNDO APELLIDO'"
                                        dense
                                        v-model="loan.cosigners[0].mothers_last_name"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'TELEFONO'"
                                        dense
                                        v-model="loan.cosigners[0].phone_number"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'CELULAR'"
                                        dense
                                        v-model="loan.cosigners[0].cell_phone_number"
                                      ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                      <v-text-field
                                        :outlined="editable"
                                        :readonly="!editable"
                                        :label="'DIRECCION'"
                                        dense
                                        v-model="loan.cosigners[0].address"
                                      ></v-text-field>
                                    </v-col>
                                  </!--v-row-->
                                </v-col>
                              </v-card-text>
                            </v-card>
                          </v-tab-item>
                          <v-tab>DESEMBOLSO</v-tab>
                            <v-tab-item >
                              <v-card flat tile>
                                <v-card-text>
                                  <v-col cols="12" class="mb-0">
                                    <v-row>
                                      <v-col cols="12" md="4">
                                        <p><b>ENTIDAD FINANCIERA:</b>{{' '+cuenta}}</p>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <p><b>NUMERO DE CUENTA:</b>{{' '+loan.lenders[0].account_number}}</p>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <p><b>CUENTA SIGEP:</b> {{' '+loan.lenders[0].sigep_status}}</p>
                                      </v-col>
                                      <v-col cols="12" md="4">
                                        <v-text-field
                                          dense
                                          v-model="loan.disbursement_date"
                                          label="FECHA DE DESEMBOLSO"
                                          hint="Día/Mes/Año"
                                          type="date"
                                          :outlined="editable"
                                          :readonly="!editable"
                                        ></v-text-field>
                                      </v-col>
                                      <v-col cols="12" md="4">
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
                                      </v-col>
                                      <v-col cols="12" md="4"  class="py-0">
                                        <div v-if="loan.payment_type_id=='1'"  class="py-0">
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
                                      </v-col>
                                    </v-row>
                                  </v-col>
                                </v-card-text>
                              </v-card>
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