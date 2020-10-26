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
          <v-stepper-step editable
            :key="`${3}-step`"
            :complete="e1 > 3"
            :step="3">Garantía
          </v-stepper-step>
          <v-divider v-if="3 !== steps" :key="3" ></v-divider>
          <v-stepper-step editable
            :key="`${4}-step`"
            :complete="e1 > 4"
            :step="4"
            >Afiliado
          </v-stepper-step>
          <v-divider v-if="4 !== steps" :key="4" ></v-divider>
          <v-stepper-step  editable
            :key="`${5}-step`"
            :complete="e1 > 5"
            :step="5"
           >Formulario
          </v-stepper-step>
          <v-divider v-if="5 !== steps" :key="5" ></v-divider>
          <v-stepper-step editable
            :key="`${6}-step`"
            :complete="e1 > 6"
            :step="6"
            >Requisitos
          </v-stepper-step>
          <v-divider v-if="6 !== steps" :key="6" ></v-divider>
        </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content :key="`${1}-content`" :step="1">
          <v-card color="grey lighten-1">
            <Ballots
              :modalities.sync="modalities"
              :bonos.sync="bonos"
              :payable_liquid="payable_liquid"
              :intervalos.sync="intervalos"
              :modalidad.sync="modalidad"
              :contributions1.sync="contributions1"
              :affiliate.sync="affiliate"
              :contrib_codebtor="contrib_codebtor"
              :loan_detail.sync="loan_detail"
              :data_loan.sync="data_loan"
              :edit_refi_repro.sync="edit_refi_repro"
              :loanTypeSelected.sync="loanTypeSelected"
              :data_sismu.sync ="data_sismu"
            />
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
              {{this.contributions1_aux }}
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content :key="`${2}-content`" :step="2" >
          <v-card color="grey lighten-1">
            <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
            <v-card class="ma-3">
              <BallotsResult
                v-show="modalidad.procedure_type_id!=12"
                :calculator_result.sync="calculator_result"
                :loan_detail.sync="loan_detail"
                :data_loan.sync="data_loan"
                :modalidad.sync="modalidad"
                :modalidad_id.sync="modalidad.id"
                :liquid_calificated="liquid_calificated" >
                <template v-slot:title>
                  <v-col cols="12" class="py-0">Resultado para el Préstamo</v-col>
                </template>
              </BallotsResult>
              <BallotsResultHipotecary
                v-show="modalidad.procedure_type_id==12"
                :data_loan.sync="data_loan"
                :lenders.sync="lenders"
                :intervalos.sync="intervalos"
                :liquid_calificated.sync="liquid_calificated"
                :loan_detail.sync="loan_detail"
              />
            </v-card>
            <v-container class="py-0">
              <v-row>
              <v-spacer></v-spacer><v-spacer> </v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                    @click="beforeStep(2)">Atras</v-btn>
                  <v-btn
                    v-show="loan_detail.maximum_suggested_valid"
                    right
                    color="primary"
                    @click="nextStep(2)">
                    Siguiente
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content :key="`${3}-content`" :step="3" >
          <v-card color="grey lighten-1">
            <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
            <HipotecaryData
              v-show="modalidad.procedure_type_id==12"
              :loan_detail.sync="loan_detail"
              :loan_property="loan_property"
            />
            <Guarantor
            :modalidad_guarantors.sync="modalidad.guarantors"
            :modalidad.sync="modalidad"
            :loan_detail.sync="loan_detail"
            :guarantors.sync="guarantors"
            :affiliate.sync="affiliate"
            :modalidad_id.sync="modalidad.id"/>
          <v-container class="py-0">
            <v-row>
            <v-spacer></v-spacer><v-spacer></v-spacer> <v-spacer></v-spacer>
              <v-col class="py-0">
                <v-btn text
                @click="beforeStep(3)">Atras</v-btn>
                <v-btn right
                color="primary"
                @click="nextStep(3)">
                Siguiente
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-stepper-content>
      <v-stepper-content :key="`${4}-content`" :step="4" >
        <v-card color="grey lighten-1">
        <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
          <PersonalInformation
          :affiliate.sync="affiliate"
          :addresses.sync="addresses"
          />
        <v-container class="py-0">
          <v-row>
          <v-spacer></v-spacer><v-spacer></v-spacer><v-spacer></v-spacer>
            <v-col class="py-0">
              <v-btn text
              @click="beforeStep(4)">Atras</v-btn>
              <v-btn right
              color="primary"
              @click="nextStep(4)">
              Siguiente
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
        </v-card>
      </v-stepper-content>
      <v-stepper-content :key="`${5}-content`" :step="5">
        <v-card color="grey lighten-1">
          <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
          <FormInformation
            :loan_detail.sync="loan_detail"
            :modalidad_personal_reference.sync="modalidad.personal_reference"
            :personal_reference.sync="personal_reference"
            :intervalos.sync="intervalos"
            :destino.sync="destino"
          />
          <CoDebtor
            v-show="this.modalidad.max_cosigner > 0"
            :personal_codebtor="personal_codebtor"
            :modalidad.sync="modalidad"
          />
          <v-container class="py-0">
            <v-row>
            <v-spacer></v-spacer><v-spacer></v-spacer><v-spacer></v-spacer>
              <v-col class="py-0">
                <v-btn text
                @click="beforeStep(5)">Atras</v-btn>
                <v-btn
                color="primary"
                @click="nextStep(5)">
                Siguiente
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-stepper-content>
      <v-stepper-content :key="`${6}-content`" :step="6" >
        <v-card color="grey lighten-1">
          <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
          <Requirement
            :bus="bus"
            :loan_detail.sync="loan_detail"
            :lenders.sync="lenders"
            :modalidad.sync="modalidad"
            :reference.sync="reference"
            :modalidad_id.sync="modalidad.id"
            :cosigners="cosigners"
            :guarantors.sync="guarantors"
            :loan_property_id ="loan_property.id"/>
        </v-card>
      </v-stepper-content>
    </v-stepper-items>
  </v-stepper>
</div>
</template>
<style>
.v-expansion-panel-content__wrap {
  padding: 0 0px 0px;
}
.v-stepper__content {
  padding: 0px 0px 0px;
}
</style>
<script>
import Ballots from '@/components/loan/Ballots'
import Requirement from '@/components/loan/Requirement'
import BallotsResult from '@/components/loan/BallotsResult'
import PersonalInformation from '@/components/affiliate/PersonalInformation'
import FormInformation from '@/components/loan/FormInformation'
import Guarantor from '@/components/loan/Guarantor'
import CoDebtor from '@/components/loan/CoDebtor'
import HipotecaryData from '@/components/loan/HipotecaryData'
import BallotsResultHipotecary from '@/components/loan/BallotsResultHipotecary'
export default {
  name: "loan-steps",
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
    Requirement,
    Ballots,
    PersonalInformation,
    FormInformation,
    BallotsResult,
    BallotsResultHipotecary,
    Guarantor,
    CoDebtor,
    HipotecaryData
  },
  data: () => ({
    bus: new Vue(),
    e1: 1,
    loan_detail:{
    maximum_suggested_valid:true,
    net_realizable_value:0
    },
    data_loan:{},
    calculator_result:{},
    //procedure_type:9,
    steps: 6,
    modalities: [],
    guarantors: [],
    lenders:[],
    modalidad:{},
    reference:[],
    intervalos:{},
    contributions1:[{}],
    payable_liquid:[0,0,0],
    bonos:[0,0,0,0],
    personal_reference:{},
    personal_codebtor:[],
    cosigners:[],
    contrib_codebtor: [],
    contributions1_aux: [],
    liquid_calificated:[],
    editedIndex: -1,
    loan_property: {},
    cosigners:[],
    destino:[],
    //Variables reprogramacion y refinanciamiento
    data_loan: {},
    edit_refi_repro: false,
    loanTypeSelected: {},
    data_sismu:{
      type_sismu: false,
      quota_sismu: 0,
      cpop_sismu: false,
    }
  }),
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    },
    refinancing() {
      return this.$route.params.hash == 'refinancing'
    },
    reprogramming() {
      return this.$route.params.hash == 'reprogramming'
    },
    type_sismu() {
      if(this.$route.query.type_sismu){
        this.data_sismu.type_sismu = true
      }
      return this.data_sismu.type_sismu
    }
  },
  watch: {
    steps (val) {
      if (this.e1 > val) {
        this.e1 = val
      }
    },
  },
  beforeMount(){
    this.getProcedureType()
    this.bus.$on('beforeStepBus', (val) => {
      this.beforeStep(val)
    })
  },
  mounted(){
    if(!this.isNew && !this.type_sismu){
      this.getLoan(this.$route.query.loan_id)
    }else{
      //alert("Es nuevo")
    }    
  }, 
  methods: {
    nextStep (n) {
      if (n == this.steps) {
        this.e1 = 1
      }
      else {
        if(n==1)
        {
          this.liquidCalificated()
          this.getLoanDestiny()
        }
        if(n==2)
        {
          console.log('segundo'+this.modalidad.guarantors )
        }
        if(n==3){
          if(this.modalidad.procedure_type_id==12){
            this.saveLoanProperty()
            console.log('Es hipotecario')
          }
          else{
            console.log("No es hipotecario")
          }
        }
        if(n==4)
        {
          console.log('segundo'+this.modalidad.personal_reference)
        }
        if(n==5)
        {
          this.personal()
          this.savePersonalReference()
          console.log('segundo'+this.modalidad.personal_reference)
        }
        this.e1 = n + 1
      }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
    async personal()
    {
      try{
        if (this.modalidad.personal_reference) {
          let res = await axios.post(`personal_reference`, {
            city_identity_card_id:this.personal_reference.city_identity_card_id,
            identity_card:this.personal_reference.identity_card,
            last_name:this.personal_reference.last_name,
            mothers_last_name:this.personal_reference.mothers_last_name,
            first_name:this.personal_reference.first_name,
            second_name:this.personal_reference.second_name,
            phone_number:this.personal_reference.phone_number,
            cell_phone_number:this.personal_reference.cell_phone_number
          })
          this.reference.push(res.data.id)
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
        console.log('entro por verdader'+this.modalidad.personal_reference)
      }
    },
    async getProcedureType(){
      try {
        let resp = await axios.get(`module`,{
          params: {
            name: 'prestamos',
            sortBy: ['name'],
            sortDesc: ['false'],
            per_page: 10,
            page: 1
          }
        })
        this.modulo= resp.data.data[0].id
        let res = await axios.get(`module/${this.modulo}/modality_loan`)
        this.modalities = res.data
        //Verifica si es refinaciamiento o reprogramación para no mostrar Anticipo
        if(this.refinancing || this.reprogramming){  
          let modalities_aux=[]        
          for(let i = 0; i < this.modalities.length; i++ ){
            if(this.modalities[i].name != "Préstamo Anticipo"){
              modalities_aux.push(this.modalities[i])
            }
          }
          this.modalities = modalities_aux
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async simuladores() {
      this.liquidCalificated()
       console.log('este es el liquido para calificacion'+this.liquid_calificated)
      try {
        let res = await axios.post(`simulator`, {
          procedure_modality_id:this.modalidad.id,
          amount_requested: this.intervalos.maximun_amoun,
          months_term:  this.intervalos.maximum_term,
          guarantor: false,
          liquid_qualification_calculated_lender: 0,
          liquid_calculated:this.liquid_calificated
        })
        this.calculator_result = res.data
  /*      for (this.j = 0; this.j< this.simulator.length; this.j++){
this.simulator[this.j].affiliate_nombres=this.datos_calculadora_hipotecario[this.j].affiliate_name
console.log(""+this.simulator[this.j].affiliate_nombres)
}
*/
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async calculadoraModalidades(){
     console.log('entro a la calculadora de modalidades')
      this.simuladores()
    },
     //TAB1 formatContributions datos obtenidos de los campos liquido y bonos, adecuandolo a formato para guardado y obtener liquido para calificación
    formatContributions() {
      let nuevoArray = []
      let nuevoArrayCodebtor = []
      if(this.modalidad.quantity_ballots > 1 ){
        nuevoArray[0] = {
            affiliate_id:this.$route.query.affiliate_id,
            contributions: [
            {
              payable_liquid: this.payable_liquid[0],
              seniority_bonus:  this.bonos[2],
              border_bonus: this.bonos[0],
              public_security_bonus: this.bonos[3],
              east_bonus:this.bonos[1]
            },
            {
              payable_liquid: this.payable_liquid[1],
              seniority_bonus: 0,
              border_bonus: 0,
              public_security_bonus: 0,
              east_bonus:0
            },
            {
              payable_liquid: this.payable_liquid[2],
              seniority_bonus: 0,
              border_bonus:0,
              public_security_bonus: 0,
              east_bonus:0
            }
          ]
        }
        console.log("NUEVO ARRAY")
        console.log(nuevoArray)
      }else{
        nuevoArray[0] = {
            affiliate_id:this.$route.query.affiliate_id,
            contributions: [
            {
              payable_liquid: this.payable_liquid[0],
              seniority_bonus:  this.bonos[2],
              border_bonus: this.bonos[0],
              public_security_bonus: this.bonos[3],
              east_bonus:this.bonos[1]
            }
          ]
        }
      for (let i = 0; i < this.contrib_codebtor.length; i++) {
        nuevoArrayCodebtor[i] = {
          affiliate_id: this.contrib_codebtor[i].id_affiliate,
          sismu: this.data_sismu.type_sismu,
          quota_sismu: this.data_sismu.quota_sismu,
          contributions: [
            {
            payable_liquid: this.contrib_codebtor[i].payable_liquid,
            border_bonus: this.contrib_codebtor[i].border_bonus,
            east_bonus: this.contrib_codebtor[i].east_bonus,
            seniority_bonus: this.contrib_codebtor[i].seniority_bonus,
            public_security_bonus: this.contrib_codebtor[i].public_security_bonus
            }
          ]
        };
        console.log("NUEVO ARRAY")
        console.log(nuevoArray)
      }
      }
      this.contributions1_aux = nuevoArray.concat(nuevoArrayCodebtor)
    },
    //TAB1 Obtener liquido para calificación
    async liquidCalificated(){
      this.formatContributions()
      try {
        let res = await axios.post(`liquid_calificated`,{liquid_calification:this.contributions1_aux})
        this.liquid_calificated =res.data
        console.log("RESULTADO")
        let res1 = await axios.post(`simulator`, {
          procedure_modality_id:this.modalidad.id,
          amount_requested: this.intervalos.maximun_amoun,
          months_term:  this.intervalos.maximum_term,
          guarantor: false,
          liquid_qualification_calculated_lender: 0,
          liquid_calculated:this.liquid_calificated
        })
        this.calculator_result = res1.data
        this.lenders=res.data
        this.lenders[0].payment_percentage=this.calculator_result.affiliates[0].payment_percentage
        this.lenders[0].indebtedness_calculated=this.calculator_result.affiliates[0].indebtedness_calculated
        this.loan_detail.minimum_term=this.intervalos.minimum_term
        this.loan_detail.maximum_term=this.intervalos.maximum_term
        this.loan_detail.minimun_amoun=this.intervalos.minimun_amoun
        this.loan_detail.maximun_amoun=this.intervalos.maximun_amoun
        this.loan_detail.amount_requested=this.intervalos.maximun_amoun
        this.loan_detail.months_term=this.intervalos.maximum_term
        this.loan_detail.liquid_qualification_calculated=this.calculator_result.liquid_qualification_calculated_total
        this.loan_detail.indebtedness_calculated=this.calculator_result.indebtedness_calculated_total
        this.loan_detail.maximum_suggested_valid=this.calculator_result.maximum_suggested_valid
        this.loan_detail.quota_calculated_total_lender=this.calculator_result.quota_calculated_estimated_total
        if( this.calculator_result.amount_maximum_suggested<this.intervalos.maximun_amoun){
          this.calculator_result.amount_requested=this.calculator_result.amount_maximum_suggested
        }else{
          this.calculator_result.montos=this.intervalos.maximun_amoun
        }
        /* for (this.i = 0; this.i< this.datos_calculadora_hipotecario.length; this.i++) {
let res5 = await axios.get(`affiliate/${this.datos_calculadora_hipotecario[this.i].affiliate_id}`)
this.affiliates = res5.data
this.datos_calculadora_hipotecario[this.i].affiliate_name=this.affiliates.full_name
}*/
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
        console.log('entro por verdadero')
      }
    },
    //TAB 3 bien inmueble
    async saveLoanProperty() {
      try {
        if (this.editedIndex == -1) {
          let res = await axios.post("loan_property", {
            land_lot_number: this.loan_property.land_lot_number,
            neighborhood_unit: this.loan_property.neighborhood_unit,
            location: this.loan_property.location,
            surface: this.loan_property.surface,
            measurement: this.loan_property.measurement,
            cadastral_code: this.loan_property.cadastral_code,
            limit: this.loan_property.limit,
            public_deed_number: this.loan_property.public_deed_number,
            lawyer: this.loan_property.lawyer,
            registration_number: this.loan_property.registration_number,
            real_folio_number: this.loan_property.real_folio_number,
            public_deed_date: this.loan_property.public_deed_date,
            net_realizable_value: this.loan_detail.net_realizable_value,
            real_city_id: this.loan_property.real_city_id
          });
          this.loan_property = res.data
          this.editedIndex = this.loan_property.id
        } else {
           let res = await axios.patch(`loan_property/${this.loan_property.id}`,
           {
              land_lot_number: this.loan_property.land_lot_number,
              neighborhood_unit: this.loan_property.neighborhood_unit,
              location: this.loan_property.location,
              surface: this.loan_property.surface,
              measurement: this.loan_property.measurement,
              cadastral_code: this.loan_property.cadastral_code,
              limit: this.loan_property.limit,
              public_deed_number: this.loan_property.public_deed_number,
              lawyer: this.loan_property.lawyer,
              registration_number: this.loan_property.registration_number,
              real_folio_number: this.loan_property.real_folio_number,
              public_deed_date: this.loan_property.public_deed_date,
              net_realizable_value: this.loan_detail.net_realizable_value,
              real_city_id: this.loan_property.real_city_id
            }
          );
          this.loan_property = res.data
        }
      } catch (e) {
        console.log(e)
      }
    },
    async savePersonalReference() {
      try {
        let ids_codebtor=[]
        for (let i = 0; i < this.personal_codebtor.length; i++) {
          let res = await axios.post(`personal_reference`, {
            city_identity_card_id: this.personal_codebtor[i].city_identity_card_id,
            identity_card: this.personal_codebtor[i].identity_card,
            last_name: this.personal_codebtor[i].last_name,
            mothers_last_name: this.personal_codebtor[i].mothers_last_name,
            first_name: this.personal_codebtor[i].first_name,
            second_name: this.personal_codebtor[i].second_name,
            phone_number: this.personal_codebtor[i].phone_number,
            cell_phone_number: this.personal_codebtor[i].cell_phone_number,
            address: this.personal_codebtor[i].address,
            civil_status: this.personal_codebtor[i].civil_status,
            gender: this.personal_codebtor[i].gender,
            cosigner: true,
            city_birth_id: this.personal_codebtor[i].city_birth_id
          })
          ids_codebtor.push(res.data.id)
          console.log(this.personal_codebtor.length)
          console.log(ids_codebtor)
        }
        this.cosigners = ids_codebtor
        console.log(this.cosigners)
      } catch (e) {
        this.dialog = false
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getLoan(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}`)
        this.data_loan = res.data

        let res2 = await axios.get(`procedure_modality/${this.data_loan.procedure_modality_id}`)
        let mod_refi_repro=res2.data.procedure_type_id
        this.loanTypeSelected.id = res2.data.procedure_type_id
        this.edit_refi_repro = true
        
        if(this.data_loan.property_id != null){
          let res3 = await axios.get(`loan_property/${this.data_loan.property_id}`)
          this.loan_detail.net_realizable_value = res3.data.net_realizable_value
        }        
        console.log(this.data_loan)
      } catch (e) {
        console.log(e)
      } finally {
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
    }
    
  }
}
</script>