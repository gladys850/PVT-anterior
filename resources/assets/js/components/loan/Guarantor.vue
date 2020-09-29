<template>
  <v-container fluid >
    <v-row justify="center"  v-show="modalidad.procedure_type_id!=12">
         <v-col cols="12" class="py-0" >
          <v-card v-show="show_garante">
            <v-container v-if="modalidad_guarantors==0">
              <v-row>
                {{datos}}
                <v-col class="text-center">
                  <h2 class="success--text" > {{modalidad.procedure_type_id}}{{modalidad_id}}ESTA MODALIDAD NO NECESITA GARANTE</h2>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
         </v-col>
      <v-col cols="4" class="py-0" v-if="modalidad_guarantors>0" >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="4"></v-col>
              <v-col cols="12" md="6" >
                Afiliado
              </v-col>
              <v-col cols="12" md="2"></v-col>
              <v-col cols="12" md="1"></v-col>
              <v-col cols="12" md="8" >
                <v-text-field
                  label="C.I."
                  v-model="guarantor_ci"
                  class="py-0"
                  single-line
                  hide-details
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="2">
                <v-tooltip>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      fab
                      dark
                      x-small
                      v-on="on"
                      color="info"
                      @click.stop="activar()">
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
        <br>
        <v-card v-show="show_calculated">
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="12">
                <v-layout row wrap>
                  <v-flex xs12 class="px-2">
                    <fieldset class="pa-3">
                      <v-text-field
                        class="py-0"
                        label="Boleta de Pago"
                        v-model="payable_liquid[0]"
                      ></v-text-field>
                      <v-text-field
                        class="py-0"
                        v-model="bonos[0]"
                        label="Bono Frontera"
                      ></v-text-field>
                      <v-text-field
                        class="py-0"
                        v-model="bonos[1]"
                        label="Bono Oriente"
                      ></v-text-field>
                      <v-text-field
                        class="py-0"
                        v-model="bonos[2]"
                        label="Bono Cargo"
                      ></v-text-field>
                      <v-text-field
                        class="py-0"
                        v-model="bonos[3]"
                        label="Bono Seguridad"
                      ></v-text-field>
                      </fieldset>
                    </v-flex>
                  </v-layout>
                </v-col>
                <v-col cols="12" md="7" class="py-0" ></v-col>
                <v-col cols="12" md="4" class="py-0">
                  <v-btn
                    color="info"
                       rounded
                    @click="Calculator1()">Calcular
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-col>
        <v-col cols="8" class="py-0" >
          <v-card v-show="show_garante">
            <v-container v-if="modalidad_guarantors>0">
              <v-row>
                <v-col class="text-center">
                    <h4 class="error--text" > CANTIDAD DE GARANTES QUE NECESITA ESTA MODALIDAD:{{modalidad_guarantors}}<br>
                  EL GARANTE DEBE ESTAR ENTRE UNA CATEGORIA DE {{prueba[1]}} A {{prueba[2]}} </h4>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
          <v-card v-show="!show_garante">
            <v-container class="py-0">
              <v-row>
                <v-col cols="12" md="4"></v-col>
                <v-col cols="12" md="6"  class="py-0" >
                  <h2 class="red--text" v-show="!validated">NO PUEDE SER GARANTE</h2>
                  <h2 class="success--text" v-show="validated1"> PUEDE SER GARANTE</h2>
                </v-col>
                <v-col cols="12" md="12" class="py-0" v-show="affiliate_garantor.affiliate.cpop">
                  <h5 class="success--text text-center">AFILIADO CPOP</h5>
                </v-col>
                <v-col cols="12" md="6" class="ma-0 pb-0 font-weight-light">
                  AFILIADO :{{affiliate_garantor.affiliate.full_name}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 pb-0 font-weight-light" >
                  C.I :{{affiliate_garantor.affiliate.identity_card_ext}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 pb-0 font-weight-light" >
                  CATEGORIA:   {{affiliate_garantor.affiliate.category.name}}
                </v-col>
                <v-col cols="12" md="6" class="py-0 font-weight-light" >
                  MATRICULA:{{affiliate_garantor.affiliate.registration}}
                </v-col>
                <v-col cols="12" md="6" class="text-uppercase py-0 font-weight-light">
                  ESTADO:{{affiliate_garantor.affiliate.affiliate_state.name}}
                </v-col>
                <v-col cols="12" md="12" class="font-weight-black ">
                  PRESTAMOS VIGENTES QUE TIENE EL AFILIADO
                </v-col>
                <v-col cols="12" class="py-0">
                  <v-data-table
                    :headers="headers"
                    :items="loan"
                    :items-per-page="4"
                    hide-default-footer
                  >
                  </v-data-table>
                </v-col>
                <v-col cols="12" md="8" class="font-weight-black" >
                  PRESTAMOS QUE ESTA GARANTIZANDO:
                </v-col>
                <v-col cols="12" md="2" class="font-weight-black" >
                  {{affiliate_garantor.active_guarantees_quantity}}
                </v-col>
              </v-row>
            </v-container>
          </v-card>
          <br>
          <v-card v-show="show_result">
            <v-container >
              <v-row>
                <v-col cols="12" md="12">
                  <v-card-text class="py-0">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <p class="py-0 mb-0">Liquido Total:{{evaluate_garantor.payable_liquid +' '}}<b>|</b> Total de Bonos:{{evaluate_garantor.bonus_calculated +' '}}<b>|</b> Liquido para la Calificacio:{{evaluate_garantor.payable_liquid_calculated }}</p>
                          <p class="py-0 mb-0">Indice de Endeudamineto: {{evaluate_garantor.indebtnes_calculated+'% '}}<b>|</b> <b>{{evaluate_garantor.is_valid?'Cubre la Cuota ':'No Cubre la Cuota'}}</b></p>
                          <div class="text-right"  v-show="evaluate_garantor.is_valid">
                            <v-btn
                              x-small
                              class="py-0"
                              color="info"
                              rounded
                              @click="añadir()">Añadir Garante
                            </v-btn>
                          </div>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-card-text>
                </v-col>
                <v-col cols="12" md="12">
                  <v-card-text class="py-0">
                    <v-layout row wrap class="py-0">
                      <v-flex xs12 class="px-2" >
                        <fieldset class="pa-3">
                          {{'Cantidad de garantes a añadir: '+modalidad_guarantors}}
                          <div
                            class="align-end font-weight-light ma-0 pa-0"
                            v-for="(otherDoc, index) of garantes_detalle"
                            :key="index"
                          >
                            {{index+1 +". "}} {{otherDoc}}
                            <v-btn text icon color="error" @click.stop="deleteOtherDocument(index)">X</v-btn>
                              <v-divider></v-divider>
                          </div>
                          <div class="text-right"  v-show="evaluate_garantor.is_valid">
                            <v-btn
                              class="py-0"
                              color="info"
                              rounded
                               x-small
                              @click="simulador()">Calculo de Cuota
                            </v-btn>
                          </div>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-card-text>
                </v-col>

     <v-col cols="12" md="12">
                  <v-card-text class="py-0">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <p class="py-0 mb-0">Monto del Prestamo: {{simulator_guarantors.amount_requested +' '}}<b>|</b> Plazo del Prestamo:{{simulator_guarantors.amount_requested +' '}}<b>|</b> Cuota del Titular:{{simulator_guarantors.quota_calculated_estimated_total  }}</p>
                          <v-progress-linear></v-progress-linear>

                            <ul style="list-style: none" class="pa-0">
                              <li v-for="(afiliados,i) in simulator_guarantors.affiliates" :key="i" >
                                <v-progress-linear></v-progress-linear>
                                <p class="py-0 mb-0">Nombre del Afiliado: {{ afiliados.affiliate_id}}</p>
                                <p class="py-0 mb-0">Cuota: {{afiliados.quota_calculated+"  "}}{{"  "+"Indice de Endeudamiento:"+afiliados.indebtedness_calculated}}{{" "}}Porcentaje de Pago: {{" "+afiliados.payment_percentage}}%</p>
                              </li>
                            </ul>
                          
                          
                          <div class="text-right"  v-show="evaluate_garantor.is_valid">
                            <v-btn
                              class="py-0"
                              color="info"
                              rounded
                               x-small
                              @click="simulador()">Calculo de Cuota
                            </v-btn>
                          </div>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-card-text>
                </v-col>

              </v-row>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
      <v-card>
        <HipotecaryData v-show="modalidad.procedure_type_id==12"/>
      </v-card>
    </v-container>
</template>
<script>

import HipotecaryData from '@/components/loan/HipotecaryData'
  export default {
  name: "loan-guarantor",
   props: {
    garantes: {
      type: Array,
      required: true
    },
     affiliate: {
      type: Object,
      required: true
    },
    prueba: {
      type: Array,
      required: true
    },
    datos: {
      type: Object,
      required: true
    },
    calculos: {
      type: Object,
      required: true
    },
    modalidad_guarantors: {
      type: Number,
      required: true,
      default: 0
    },
    modalidad_id: {
      type: Number,
      required: true,
      default: 0
    },
    modalidad: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    guarantor_ci:null,
    affiliate_garantor:{
      affiliate:{
        category:{},
        affiliate_state:{}
      },
    },
    show_garante:true,
    show_calculated:false,
    show_result:false,
    simulator_guarantors:{},
    loan:[],
    index: [],
    garantes_detalle:[],
    guarantor:[null],
    validated:true,
    validated1:false,
    payable_liquid:[0],
    bonos:[0,0,0,0],
    calculos1:{},
    evaluate_garantor:{},
    headers: [
      {
        text: "Codigo",
        class: ["normal", "white--text"],
        align: "left",
        value: "code"
      },
      {
        text: "Monto Aprovado",
        class: ["normal", "white--text"],
        align: "left",
        value: "amount_approved"
      },
      {
        text: "Plazo",
        class: ["normal", "white--text"],
        align: "left",
        value: "loan_term"
      },
       {
        text: "Cuota",
        class: ["normal", "white--text"],
        align: "left",
        value: "estimated_quota"
      }
    ],
    
 
  }),
     components: {
   HipotecaryData
  },
 
  computed: {
 
  
 },
 watch:{
ver()
{
  añadir()
}
 },
  beforeMount() {
  },
  mounted() {
  },
  methods: {
    async clear()
    {

         this.payable_liquid[0]=0,
    this.bonos[0]=0
    this.bonos[1]=0
    this.bonos[2]=0
    this.bonos[3]=0
    this.calculos1.bonus_calculated=null
    this.calculos1.liquid_qualification_calculated=null
    this.calculos1.quota_calculated=null
    this.calculos1.indebtedness_calculated=null
    this.calculos1.amount_maximum_suggested=null
    this.guarantor_ci=null
    },
 async añadir()
    {
      this.garantes.push(this.affiliate_garantor,'hola');
      this.garantes_detalle.push(this.affiliate_garantor.affiliate.full_name);
    console.log('entro a garantes ==> '+this.garantes)
     console.log('entro a garantes ==> '+this.garantes_detalle)

this.clear()
    },
    deleteOtherDocument(i) {
      this.garantes.splice(i, 1);
      this.garantes_detalle.splice(i, 1);
      console.log("other1 " + this.garantes);
  
      console.log("other2 " + this.garantes_detalle);
    },
   /* async añadir()
    {
      console.log('entro a garantes')
this.garantes[0]=this.affiliate_garantor.affiliate.id
    this.toastr.success("Se añadio satisfactoriamente al garante.")
      
console.log('este es el garante'+this.garantes[0])
    },*/
    async Calculator() {
      try {
        this.show_result=true  
         let res = await axios.post(`calculator`, {
            procedure_modality_id:this.modalidad_id,
            months_term:  this.calculos.plazo,
            amount_requested: this.calculos.montos,
            affiliate_id:this.affiliate_garantor.affiliate.id,
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
        this.calculos1= res.data     
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
     async Calculator1() {
      try {
        this.show_result=true  
         let res = await axios.post(`evaluate_garantor`, {
            procedure_modality_id:this.modalidad_id,
            affiliate_id:this.affiliate_garantor.affiliate.id,
            quota_calculated_total_lender:900,
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
        this.evaluate_garantor= res.data
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    async simulador() {
      try {
      let res = await axios.post(`simulator`, {
              procedure_modality_id: this.modalidad_id,
              amount_requested: 68000,
              months_term: 96,
              guarantor: true,
              liquid_qualification_calculated_lender: 2000,
              liquid_calculated:[
                {affiliate_id:1,
                liquid_qualification_calculated:2000
                },
                {affiliate_id:37973,
                liquid_qualification_calculated:2000
                }
                ]
          })
          this.simulator_guarantors = res.data
          console.log('entro al simulador'+this.simulator_guarantors)

/*      for (this.j = 0; this.j< this.simulator.length; this.j++) {

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
   async activar()
    {
      try {
        if(this.guarantor_ci==this.affiliate.identity_card)
        {
          this.toastr.error("El garante no puede tener el mismo carnet que el titular.")
        }
        else{
          let resp = await axios.post(`affiliate_guarantor`,{
            identity_card: this.guarantor_ci,
            procedure_modality_id:this.modalidad_id,
          })
          this.affiliate_garantor=resp.data
          this.validated=this.affiliate_garantor.guarantor
          this.validated1=this.affiliate_garantor.guarantor
          this.show_calculated=this.affiliate_garantor.guarantor
          this.loan=this.affiliate_garantor.affiliate.loans
          this.guarantor=this.affiliate_garantor.affiliate.guarantor
          this.show_garante=false
          console.log('Entro al metodo de garanyes'+this.affiliate_garantor+'==>'+this.guarantor_ci) 
          console.log('prestamos'+this.loan)  
          console.log('guarantor'+this.guarantor)
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    
    }
  }
  }
</script>