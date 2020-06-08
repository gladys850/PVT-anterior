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
            :step="3">Garantes 
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
                  :datos.sync="datos"
                  :bonos.sync="bonos"
                  :payable_liquid.sync="payable_liquid"
                  :calculos.sync="calculos"
                  :modalidad.sync="modalidad">
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
            <Guarantor
              :modalidad_guarantors.sync="modalidad.guarantors"
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
              :prueba.sync="prueba"
              :calculos.sync="calculos"
              :intervalos.sync="intervalos"
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
              :datos.sync="datos"
              :formulario.sync="formulario"
              :calculos.sync="calculos"
              :intervalos.sync="intervalos"
              :modalidad.sync="modalidad"
              :reference.sync="reference"
              :garantes.sync="garantes"
              :modalidad_id.sync="modalidad.id"/>
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
import Ballots from '@/components/loan/Ballots'
import Requirement from '@/components/loan/Requirement'
import BallotsResult from '@/components/loan/BallotsResult'
import PersonalInformation from '@/components/affiliate/PersonalInformation'
import FormInformation from '@/components/loan/FormInformation'
import Guarantor from '@/components/loan/Guarantor'
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
    Guarantor
  },
   data: () => ({
    bus: new Vue(),
    e1: 1,
    steps: 6,
    modalities: [],
    prueba: [],
    garantes: [],
    modalidad:{},
    datos:{},
    reference:{},
    intervalos:{},
    contributions1:[{},{},{} ],
    payable_liquid:[0,0,0],
    bonos:[0,0,0,0],
    formulario:[],
    personal_reference:{},
    calculos:{
      promedio_liquido_pagable:0,
      total_bonos:0,
      liquido_para_calificacion:0,
      calculo_de_cuota:0,
      indice_endeudamiento:0,
      monto_maximo_sugerido:0
    }
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
          this.Calculator()
               console.log('esta es la modalidad ultima prueba'+this.modalidad.id)
     
        }
        if(n==2)
        {
          console.log('segundo'+this.modalidad.guarantors )
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
    //Metodo para identificar el modulo
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
        let res = await axios.get(`module/${this.modulo}/procedure_type`)
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
    }
  },
}
</script>