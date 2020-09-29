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
              :contributions1.sync="contributions1"
              :modalidad.sync="modalidad"
              :prueba.sync="prueba"
              :procedure_type.sync="procedure_type"
              :contrib_codebtor="contrib_codebtor"
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
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content :key="`${2}-content`" :step="2" >
          <v-card color="grey lighten-1">
            <h3 class="text-uppercase text-center">{{modalidad.name}}</h3>
              <v-card class="ma-3">
                <BallotsResult
                  :lenders.sync="lenders"
                  :datos.sync="datos"
                  :intervalos.sync="intervalos"
                  :bonos.sync="bonos"
                  :payable_liquid.sync="payable_liquid"
                  :calculos.sync="calculos"
                  :modalidad.sync="modalidad"
                  :modalidad_id.sync="modalidad.id"
                  :modalities.sync="modalities"
                  :prueba.sync="prueba"
                  :procedure_type.sync="procedure_type"
                  :calculo123.sync="calculo123"
                  :liquid_calificated="liquid_calificated" >
                    <template v-slot:title>
                      <v-col cols="12" class="py-0">Resultado para el Préstamo</v-col>
                    </template>
                </BallotsResult>
              </v-card>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer><v-spacer> </v-spacer> <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                  @click="beforeStep(2)">Atras</v-btn>
                  <v-btn right
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
              :modalidad.sync="modalidad"
              :loan_property="loan_property"/>
            <Guarantor
              :datos.sync="datos"
              :modalidad_guarantors.sync="modalidad.guarantors"
               :modalidad.sync="modalidad"
              :prueba.sync="prueba"
              :calculos.sync="calculos"
              :garantes.sync="garantes"
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
              :formulario.sync="formulario"
              :modalidad_personal_reference.sync="modalidad.personal_reference"
              :personal_reference.sync="personal_reference"    
              :intervalos.sync="intervalos"
            />
            <CoDebtor
              v-show="modalidad.personal_reference"
              :personal_codebtor="personal_codebtor"/>
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
              :lenders.sync="lenders"
              :datos.sync="datos"
              :formulario.sync="formulario"
              :calculos.sync="calculos"
              :intervalos.sync="intervalos"
              :modalidad.sync="modalidad"
              :reference.sync="reference"
              :garantes.sync="garantes"
              :modalidad_id.sync="modalidad.id"
              :personal_codebtor="personal_codebtor"/>
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
    Guarantor,
    CoDebtor,
    HipotecaryData
  },
   data: () => ({
    bus: new Vue(),
    e1: 1,
    procedure_type:9,
    steps: 6,
    modalities: [],
    prueba: [],
    garantes: [],
    lenders:[],
    modalidad:{},
    datos:{},
    reference:{},
    intervalos:{},
    contributions1:[{}],
    payable_liquid:[0,0,0],
    bonos:[0,0,0,0],
    formulario:[],//TODO ESTA VARIABLE SE DEBE BORRAR YA QUE SOLO SIRVE PARA VERIFICAR LA INFORMACION DE CADA COMPONENTE
    personal_reference:{},
    calculo123:[],
    personal_codebtor:[],
    calculos:{
      promedio_liquido_pagable:0,
      total_bonos:0,
      liquido_para_calificacion:0,
      calculo_de_cuota:0,
      indice_endeudamiento:0,
      monto_maximo_sugerido:0
    },
      contrib_codebtor: [],
      contributions1_aux: [],
      liquid_calificated:[],
      editedIndex: -1,
      loan_property: {},
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
  beforeMount(){
    this.getProcedureType();
    this.bus.$on('beforeStepBus', (val) => {
      this.beforeStep(val)
    })
  },
  methods: {
    nextStep (n) {
      if (n == this.steps) {
        this.e1 = 1
      }
      else {
        if(n==1)
        {
          if(this.modalidad.procedure_type_id==12)
          { this.liquidCalificated()
            console.log('esta entro por verdad con la modalidad'+ this.modalidad.procedure_type_id)
          }
          else{
            this.Calculator()
            console.log('esta entro por false'+this.modalidad.procedure_type_id)
          }
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
             console.log('este es el formulario 0'+this.formulario[0])
             console.log('este es el formulario 1'+this.formulario[1])
             console.log('este es el formulario 2'+this.formulario[2])

        }
        if(n==5)
        {
          this.personal()
          console.log('segundo'+this.modalidad.personal_reference)
             console.log('este es el formulario 0'+this.formulario[0])
             console.log('este es el formulario 1'+this.formulario[1])
             console.log('este es el formulario 2'+this.formulario[2])
        }
        this.e1 = n + 1
     }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
    async personal()
    {
      try {
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
            this.reference=res.data
          }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
        console.log('entro por verdader'+this.modalidad.personal_reference)
      }
    },
        /*Metodo para identificar el modulo Ejemplo de respuesta:
        "id": 9,
        "module_id": 6,
        "name": "Préstamo Anticipo"
        "second_name": "Anticipo"*/
    async getProcedureType() {
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
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para la datos de la calculadora
    async Calculator() {
      try {
        if(this.modalidad.quantity_ballots>1)
        {
          let res = await axios.post(`calculator`, {
            procedure_modality_id:this.modalidad.id,
            months_term: this.intervalos.maximum_term,
            amount_requested:this.intervalos.maximun_amoun,
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
        })
        this.calculos= res.data
        this.calculos.plazo=this.intervalos.maximum_term
        if( this.calculos.amount_maximum_suggested<this.intervalos.maximun_amoun){
          this.calculos.montos=this.calculos.amount_maximum_suggested
        }else{
          this.calculos.montos=this.intervalos.maximun_amoun
        }
        this.datos =this.intervalos
        }else{
          let res = await axios.post(`calculator`, {
            procedure_modality_id:this.modalidad.id,
            months_term: this.intervalos.maximum_term,
            amount_requested:this.intervalos.maximun_amoun,
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
        })
        this.calculos= res.data
        this.calculos.plazo=this.intervalos.maximum_term
         if( this.calculos.amount_maximum_suggested<this.intervalos.maximun_amoun){
          this.calculos.montos=this.calculos.amount_maximum_suggested
        }else{
          this.calculos.montos=this.intervalos.maximun_amoun
        }
        this.datos =this.intervalos
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //metodo para armar las contribuciones del afiliado
    Contributios()
    {
      if(this.payable_liquid.length>1)
      {
        for (this.i = 0; this.i< this.payable_liquid.length; this.i++) {
          this.contributions1[this.i].payable_liquid=this.payable_liquid[this.i]
          if(this.i = 0){
            this.contributions1[this.i].border_bonus= this.bonos[0],
            this.contributions1[this.i].east_bonus= this.bonos[1],
            this.contributions1[this.i].seniority_bonus= this.bonos[2],
            this.contributions1[this.i].public_security_bonus= this.bonos[3]
          }
          else{
            this.contributions1[this.i].border_bonus= 0,
            this.contributions1[this.i].east_bonus= 0,
            this.contributions1[this.i].seniority_bonus= 0,
            this.contributions1[this.i].public_security_bonus= 0
          }
        }
      }
      else{
        this.contributions1[this.i].payable_liquid=this.payable_liquid[0]
        this.contributions1[this.i].border_bonus= this.bonos[0],
        this.contributions1[this.i].east_bonus= this.bonos[1],
        this.contributions1[this.i].seniority_bonus= this.bonos[2],
        this.contributions1[this.i].public_security_bonus= this.bonos[3]
      }
     /*for (this.i = 0; this.i< this.interval.length; this.i++) {
        if(this.loanTypeSelected==this.interval[this.i].procedure_type_id)
        {
          this.monto= this.interval[this.i].minimum_amount+' - '+this.interval[this.i].maximum_amount,
          this.plazo= this.interval[this.i].minimum_term+' - '+this.interval[this.i].maximum_term
          this.intervalos.maximun_amoun=this.interval[this.i].maximum_amount
          this.intervalos.maximum_term= this.interval[this.i].maximum_term
          this.intervalos.procedure_type_id= this.loanTypeSelected
          this.num_type=this.loanTypeSelected
        }
      }*/
      },
    async calculadora() {
      try {
        console.log("entro a liquido pagable")
          let res = await axios.post(`liquid_calificated`, {
            liquid_calification: [
              {
                affiliate_id: 51419,
                contributions: [
                {
                    payable_liquid: 2000,
                    seniority_bonus: 0,
                    border_bonus: 0,
                    public_security_bonus: 300,
                    east_bonus: 0
                },
                {
                    payable_liquid: 3000,
                    seniority_bonus: 0,
                    border_bonus: 0,
                    public_security_bonus: 300,
                    east_bonus: 0
                },
                {
                    payable_liquid: 3500,
                    seniority_bonus: 0,
                    border_bonus: 0,
                    public_security_bonus: 0,
                    east_bonus: 0
                }
              ]
            },
               {
            affiliate_id: 1,
            contributions: [
                {
                    payable_liquid: 2000,
                    seniority_bonus: 0,
                    border_bonus: 0,
                    public_security_bonus: 300,
                    east_bonus: 0
                }
            ]
        }
            ]
          })



          this.calculo123 = res.data

           for (this.i = 0; this.i< this.calculo123.length; this.i++) {

              let res5 = await axios.get(`affiliate/${this.calculo123[this.i].affiliate_id}`)



        this.afiliados = res5.data
        this.calculo123[this.i].affiliate_name=this.afiliados.full_name
      console.log("este es el nombre del usuario"+ this.calculo123[1].affiliate_name)
      }

        /*
            for (this.j = 0; this.j< this.calculo1234.length; this.j++) {

                      let res6 = await axios.get(`affiliate/${this.calculo1234[this.j].affiliate_id}`)



                this.affiliate_total = res6.data
                this.calculo1234[this.j].affiliate_name=this.affiliate_total.full_name
              console.log("este es el nombre del usuario"+ this.calculo1234[1].affiliate_name)
              }
        */

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
     //TAB1 Formatear datos obtenidos de las contribuciones, adecuandolo a formato para guardado y obtener liquido para calificación
    formatear() {    
      let contribuciones =[]
      contribuciones=this.contributions1.concat(this.contrib_codebtor)
      console.log("CONTRIBUCIONES")
      console.log(this.contribuciones)
      let nuevoArray = [];
      let i;
      for (i = 0; i < contribuciones.length; i++) {
        nuevoArray[i] = {
          affiliate_id: contribuciones[i].id_affiliate,
          contributions: [{
            payable_liquid: parseFloat(contribuciones[i].payable_liquid),
            border_bonus: parseFloat(contribuciones[i].border_bonus),
            east_bonus: parseFloat(contribuciones[i].east_bonus),
            seniority_bonus: parseFloat(contribuciones[i].seniority_bonus),
            public_security_bonus: parseFloat(contribuciones[i].public_security_bonus)
            }]
        };
        console.log("FORMATEAR");
        console.log(nuevoArray);
      }
      //this.contrib_codebtor_aux = { liquid_calification: nuevoArray };
      this.contributions1_aux = nuevoArray;
      
    },
    //TAB1 Obtener liquido para calificación
    async liquidCalificated(){
      this.formatear()
      try {
            let res = await axios.post(`liquid_calificated`,{liquid_calification:this.contributions1_aux})
            this.liquid_calificated =res.data
            console.log("RESULTADO")
             this.datos =this.intervalos
             this.lenders=res.data
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
            net_realizable_value: this.loan_property.net_realizable_value,
            real_city_id: this.loan_property.real_city_id
          });
          this.loan_property = res.data;
          this.editedIndex = this.loan_property.id;
        } else {
          let res = await axios.patch(
            `loan_property/${this.loan_property.id}`,
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
              net_realizable_value: this.loan_property.net_realizable_value,
              real_city_id: this.loan_property.real_city_id
            }
          );
          this.loan_property = res.data;
        }
      } catch (e) {
        console.log(e);
      }
    }
  },
}
</script>