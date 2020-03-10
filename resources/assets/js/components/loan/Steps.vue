<template>
  <div>
    <v-stepper v-model="e1" >
      <v-stepper-header class=" !pa-0 ml-0" >
        <template>
          <v-stepper-step
            :key="`${1}-step`"
            :complete="e1 > 1"
            :step="1"
            editable
          >Datos Prestamo
          </v-stepper-step>
          <v-divider
            v-if="1 !== steps"
            :key="1"
          ></v-divider>
          <v-stepper-step
            :key="`${2}-step`"
            :complete="e1 > 2"
            :step="2"
            editable
          >Calculo Boletas
          </v-stepper-step>
          <v-divider
            v-if="2 !== steps"
            :key="2"
          ></v-divider>
          <v-stepper-step
            :key="`${3}-step`"
            :complete="e1 > 3"
            :step="3"
            editable
          >Resultado Calculo
          </v-stepper-step>
          <v-divider
            v-if="3 !== steps"
            :key="3"
          ></v-divider>
          <v-stepper-step
            :key="`${4}-step`"
            :complete="e1 > 4"
            :step="4"
            editable
          >Datos Afiliado
          </v-stepper-step>
          <v-divider
            v-if="4 !== steps"
            :key="4"
          ></v-divider>
          <v-stepper-step
            :key="`${5}-step`"
            :complete="e1 > 5"
            :step="5"
            editable
          >Datos Formulario
          </v-stepper-step>
          <v-divider
            v-if="5 !== steps"
            :key="5"
          ></v-divider>
           <v-stepper-step
            :key="`${6}-step`"
            :complete="e1 > 6"
            :step="6"
            editable
          >Requisitos
          </v-stepper-step>
          <v-divider
            v-if="6 !== steps"
            :key="6"
          ></v-divider>
        </template>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content
          :key="`${1}-content`"
          :step="1"
        >
          <v-card color="grey lighten-1">
            <LoanInformation
              :modalities.sync="modalities"
              :datos.sync="datos"
            />
              <v-container class="py-0">
                <v-row>
                  <v-spacer></v-spacer>
                  <v-spacer></v-spacer>
                  <v-spacer></v-spacer>
                    <v-col class="py-0">
                      <v-btn text>Cancelar</v-btn>
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
        <v-stepper-content
          :key="`${2}-content`"
          :step="2"
        >
          <v-card color="grey lighten-1">
            <Ballots
              :modality.sync="modality"
              :bonos.sync="bonos"
              :payable_liquid="payable_liquid"
              :visible.sync="visible"
            />
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
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
        <v-stepper-content
          :key="`${3}-content`"
          :step="3"
        >
          <v-card color="grey lighten-1">
            <BallotsResult
              :bonos.sync="bonos"
              :payable_liquid.sync="payable_liquid"
              :modality.sync="modality"
              :datos.sync="datos"
              :calculos.sync="calculos"
            />
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
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
        <v-stepper-content
          :key="`${4}-content`"
          :step="4"
          >
          <v-card color="grey lighten-1">
            <PersonalInformation
              :affiliate.sync="affiliate"
              :addresses.sync="addresses"
            />
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                    @click="beforeStep(4)">Atras</v-btn>
                  <v-btn
                    color="primary"
                    @click="nextStep(4)">
                    Siguiente
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-stepper-content>
        <v-stepper-content
          :key="`${5}-content`"
          :step="5"
          >
          <v-card color="grey lighten-1">
            <FormInformation
            :formulario.sync="formulario"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
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
         <v-stepper-content
          :key="`${6}-content`"
          :step="6"
          >
          <v-card color="grey lighten-1">
            <Requirement
            :modality.sync="modality"
            :datos.sync="datos"
            :formulario.sync="formulario"
            :calculos.sync="calculos"/>
            <v-container class="py-0">
              <v-row>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-spacer></v-spacer>
                <v-col class="py-0">
                  <v-btn text
                    @click="beforeStep(6)">Atras</v-btn>
                  <v-btn
                    color="primary"
                    :to="{ name: 'affiliateIndex'}">
                    Guardar
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
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
import LoanInformation from '@/components/loan/LoanInformation'
import FormInformation from '@/components/loan/FormInformation'
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "loan-steps",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    addresses: {
      type: Array,
      required: true
    },
  },
  components: {
    Requirement,
    Ballots,
    PersonalInformation,
    LoanInformation,
    FormInformation,
    BallotsResult
  },
   data: () => ({
    e1: 1,
    steps: 6,
    modalities: [],
    datos:[],
    payable_liquid:[0,0,0],
    bonos:[0,0,0,0],
    visible:false,
    modality:{
      id: null,
      procedure_type_id: null,
      name: null,
      shortened: null,
      is_valid:null,
      loan_modality_parameter: {
        id: null,
        procedure_modality_id: null,
        debt_index:null,
        quantity_ballots: null,
        guarantors: null
      },
    },
    formulario:[],
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
  },
  methods: {
    nextStep (n) {
      if (n == this.steps) {
        this.e1 = 1
       
      } 
      else {
        if(n==1)
        {
    this.getLoanModality(this.$route.query.affiliate_id)
        console.log(this.getLoanModality(this.$route.query.affiliate_id)+'paso1')
     
        }
        if(n==2)
        {
        this.Calculator()
        }
        if(n==3)
        {
          this.Calculator()
        console.log(this.bonos[0]+'estos son los bonos')
        console.log(this.bonos[1]+'estos son los bonos')
        console.log(this.bonos[2]+'estos son los bonos')
        console.log(this.bonos[3]+'estos son los bonos')
        console.log(this.payable_liquid+'estos son los liquidos')
        }
         if(n==5)
        {
        console.log(this.formulario[0]+'estos son los bonos')
        console.log(this.formulario[1]+'estos son los bonos')
               console.log(this.formulario[2]+'estos son los bonos')
        }
      
        this.e1 = n + 1
     }
    },
    beforeStep (n) {
      this.e1 = n -1
    },
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
        console.log(this.modalities+'modalidad')
        this.modalities = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getLoanModality(id) {
    try {
      let resp = await axios.get(`affiliate/${id}/loan_modality`,{
        params: {
          procedure_type_id:this.datos[0],
          external_discount:0,
          }
      })
      this.modality= resp.data
      console.log('esta modalidad falta'+this.modality)
      if(this.modality.loan_modality_parameter.quantity_ballots>1)
      {
         this.visible=true
      }else{
         this.visible=false
      }
       console.log('este es el visible'+this.visible)
     } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
   async Calculator() {
    try {
      console.log('entro a calculadora')
      let res = await axios.post(`calculator`, {
        procedure_modality_id:this.modality.id,
        months_term: this.datos[2],
        amount_requested:this.datos[1],
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
 this.calculos.plazo=this.datos[2]
console.log('entro a calculadora'+this.calculos)
} catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  }
  },
}
</script>
