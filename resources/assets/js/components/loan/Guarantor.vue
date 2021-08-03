<template>
  <v-container fluid >
    <v-row justify="center"  v-show="modalidad.procedure_type_name!= 'Préstamo Hipotecario'">
      <!-- Vista cuando el prestamo no tiene garantes-->
         <v-col cols="12" class="py-0" v-if="modalidad_guarantors == 0">
          <v-card>
            <v-container class="py-0">
              <v-col class="text-center">
                <h2 class="success--text" >ESTA MODALIDAD NO NECESITA GARANTE </h2>
              </v-col>
            </v-container>
          </v-card>
         </v-col>
      <!-- Detalle del garante cuando es rehacer una modalidad con garante-->
      <v-col cols="12" v-if="modalidad_guarantors > 0" v-show="remake">
        <v-card>
          <v-container class="py-0">
            <v-col class="text-center">
              <h5>Informacion Garantes </h5>
            </v-col>
              <ul style="list-style: none" class="pa-0">
                <li v-for="(garantes_detalle_loan,i) in data_loan_parent_aux.guarantors" :key="i" >
                  <v-progress-linear></v-progress-linear>
                    <v-row>
                    <v-col cols="12" md="5" class="py-0">
                      Nombre del Afiliado: {{garantes_detalle_loan.first_name +' '+ garantes_detalle_loan.last_name }}
                    </v-col>
                    <v-col cols="12" md="2" class="py-0">
                      C.I.: {{garantes_detalle_loan.identity_card}}
                    </v-col>
                      <v-col cols="12" md="2" class="py-0">
                      Sigep: {{garantes_detalle_loan.sigep_status}}
                    </v-col>
                      <v-col cols="12" md="3" class="py-0">
                      Porcentaje de Pago: {{garantes_detalle_loan.pivot.payment_percentage}}
                    </v-col>
                  </v-row>
                  </li>
              </ul>
          </v-container>
        </v-card>
      </v-col>
        <!-- Vista cuando sea creacion de un nuevo tramite y tenga garante parte izquierda-->
      <v-col cols="4" class="py-0" v-if="modalidad_guarantors > 0" >
          <!-- Panel del buscador-->
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="2" ></v-col>
                <v-col cols="12" md="10" class="pb-0 ma-0">
                </v-col>
              <v-col cols="12" md="1"></v-col>
              <v-col cols="12" md="8" >
                <v-text-field
                  label="C.I. o Matricula"
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
                      @click.stop="searchGuarantor()">
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
                 <v-col cols="12" md="1" ></v-col>
                  <v-col cols="12" md="11" class="success--text pb-0 ma-0 py-0" v-show="double_perception">
                    <h6 class="caption">Afiliado de doble percepcion, escoger como evaluar.</h6>
                  </v-col>
                  <v-col cols="12" md="2" class="pb-0 ma-0 py-0" v-show="double_perception"></v-col>
                 <v-col cols="12" md="5" class="pb-0 ma-0 py-0" v-show="double_perception">
                  <v-radio-group
                    class="pb-0 ma-0 py-0"
                    v-model="type_affiliate"
                    row
                  >
                    <v-radio
                      label="Titular"
                      :value="false"
                      class="pb-0 ma-0 py-0"
                         @change="searchGuarantor()"
                     ></v-radio>
                    <v-radio
                      label="Viuda"
                      :value="true"
                      class="pb-0 ma-0 py-0"
                         @change="searchGuarantor()"
                    ></v-radio>
                  </v-radio-group>
                </v-col>
                <v-col cols="12" md="5" class="pb-0 ma-0 py-0" v-show="double_perception" >
                  <center>
                    <v-btn
                      class="py-0 text-right"
                      color="info"
                      rounded
                      x-small
                      @click="contributionChange()">Evaluar
                    </v-btn>
                  </center>
                </v-col>
            </v-row>
          </v-container>
        </v-card>
        <br>
        <!-- Panel del las boletas-->
        <v-card v-show="show_calculated">
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="12" class="pb-0">
                <v-layout row wrap>
                  <v-flex xs12 class="px-2">
                    <fieldset class="pa-3">
                      <v-row>
                        <v-col cols="12" md="10" class="py-0" style="color:teal">
                          <label><b>Boleta de Pago del mes de {{' '+period_ballots}} </b></label>
                        </v-col>
                        <v-col cols="12" md="5" class="pb-0">
                          <v-text-field
                            :outlined="edit_contributions"
                            :readonly="!edit_contributions"
                            dense
                            class="py-0"
                            v-model="payable_liquid[0]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="3" v-show="show_ajuste" >
                          <label><small  class=" caption" > Meses atras</small> </label>
                        </v-col>
                        <v-col cols="12" md="3" v-show="show_ajuste" >
                        <v-row class="py-0">
                            <v-text-field
                              dense
                              v-model="number_diff_month"
                              color="info"
                              append-icon="mdi-plus-box"
                              prepend-icon="mdi-minus-box"
                              @click:append="appendIconCallback"
                              @click:prepend="prependIconCallback"
                              readonly
                            ></v-text-field>
                        </v-row>
                      </v-col>
                      <v-col cols="12" md="4" v-show="show_ajuste">
                        <v-text-field
                          dense
                          v-model="amount_adjustable"
                          :outlined="show_ajuste"
                          class="mt-0 pt-0"
                          label="Monto Ajustable"
                          type="text"
                          style="width: 90px"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="8" v-show="show_ajuste">
                        <v-text-field
                          dense
                          v-model="amount_adjustable_description"
                          :outlined="show_ajuste"
                          class="mt-0 pt-0"
                          label="Descripcion Monto Ajustable"
                          type="text"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="pb-0" v-show="show_commission">
                        <v-text-field
                          :outlined="edit_commission"
                          :readonly="!edit_commission"
                          dense
                          class="py-0"
                          v-model="period"
                          label="Periodo"
                        ></v-text-field>
                      </v-col>
                      </v-row>
                      <v-row >
                        <v-col cols="12" md="6" class="pb-0" v-show="show_contributios">
                          <v-text-field
                            :outlined="edit_contributions"
                            :readonly="!edit_contributions"
                            dense
                            class="py-0"
                            v-model="bonos[0]"
                            label="Bono Frontera"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" class="pb-0" v-show="show_contributios" >
                          <v-text-field
                            :outlined="edit_contributions"
                            :readonly="!edit_contributions"
                            dense
                            class="py-0"
                            v-model="bonos[1]"
                            label="Bono Oriente"
                          ></v-text-field>
                        </v-col>
                      <v-col cols="12" md="6" class="pb-0" v-show="show_contributios" >
                        <v-text-field
                          :outlined="edit_contributions"
                          :readonly="!edit_contributions"
                          dense
                          class="py-0"
                          v-model="bonos[2]"
                          label="Bono Cargo"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="pb-0" v-show="show_contributios">
                        <v-text-field
                          :outlined="edit_contributions"
                          :readonly="!edit_contributions"
                          dense
                          class="py-0"
                          v-model="bonos[3]"
                          label="Bono Seguridad"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6" class="pb-0" v-show="show_passive">
                        <v-text-field
                          :outlined="edit_passive"
                          :readonly="!edit_passive"
                          dense
                          class="py-0"
                          v-model="bonos[0]"
                          label="Bono Renta Dignidad"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                      </fieldset>
                    </v-flex>
                  </v-layout>
                </v-col>
                <v-col cols="12" md="7" class="py-0" ></v-col>
                <v-col cols="12" md="4" class="py-0">
                  <v-btn
                  class="py-0"
                    color="info"
                    small
                       rounded
                    @click="calculator()">Calcular
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-col>
        <!-- Vista cuando sea creacion de un nuevo tramite y tenga garante parte derecha-->
        <v-col cols="8" class="py-0" v-if="modalidad_guarantors>0">
          <v-card v-show="show_garante">
            <v-container v-if="modalidad_guarantors>0">
              <v-row>
                <v-col class="text-center"  cols="12" md="12">
                    <h4 class="error--text" > CANTIDAD DE GARANTES QUE NECESITA ESTA MODALIDAD:{{modalidad_guarantors}}<br>
                  EL GARANTE DEBE ESTAR ENTRE UNA CATEGORIA DE {{loan_detail.min_guarantor_category * 100}}% A {{loan_detail.max_guarantor_category * 100}}% </h4>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
          <v-card v-show="!show_garante" >
            <v-container class="py-0">
              <v-row>
                <v-col cols="12" md="4"></v-col>
                <v-col cols="12" md="6"  class="py-0" >
                  <h3 class="red--text" v-show="!show_guarantor_false">NO PUEDE SER GARANTE</h3>
                  <h3 class="success--text" v-show="show_guarantor_true"> PUEDE SER GARANTE</h3>
                 </v-col>
                  <v-progress-linear></v-progress-linear>
                <v-col cols="12" md="8" class="font-weight-black caption ma-0 py-0 " >
                  DATOS DEL AFILIADO
                </v-col>
                <v-col cols="12" md="6" class="ma-0 py-0 font-weight-light caption">
                  AFILIADO :{{affiliate_garantor.affiliate.full_name}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 py-0 font-weight-light caption" >
                  C.I :{{affiliate_garantor.affiliate.identity_card_ext}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 py-0 font-weight-light caption" >
                  CATEGORIA:   {{affiliate_garantor.affiliate.category.name}}
                </v-col>
                <v-col cols="12" md="6" class="py-0 font-weight-light caption" >
                  MATRICULA:{{affiliate_garantor.affiliate.registration}}
                </v-col>
                <v-col cols="12" md="6" class="text-uppercase py-0 font-weight-light caption">
                  ESTADO:{{affiliate_garantor.affiliate.affiliate_state.name}}
                </v-col>
                <v-progress-linear v-show="spouse_view" ></v-progress-linear>
                <v-col cols="12" md="8" class="error--text font-weight-black caption py-0" v-show="spouse_view" v-if="affiliate_garantor.double_perception" >
                  AFILIADO CON DOBLE PERCEPCION
                </v-col>
                <v-col cols="12" md="8" class="font-weight-black caption py-0" v-show="spouse_view" >
                  DATOS DE LA CONGUYE
                </v-col>
                <v-col cols="12" md="6" class="text-uppercase py-0 font-weight-light caption" v-show="spouse_view">
                  CONGUYE:{{this.$options.filters.fullName(spouse, true)}}
                </v-col>
                 <v-col cols="12" md="6" class="text-uppercase py-0 font-weight-light caption" v-show="spouse_view">
                  C.I.:{{spouse.identity_card}}
                </v-col>
                <v-progress-linear></v-progress-linear>
                <v-col cols="12" md="8" class="font-weight-black caption py-0" >
                  DATOS DEL PRESTAMO
                </v-col>
                <v-progress-linear></v-progress-linear>
                <v-col cols="12" md="8" class="text-uppercase py-0 font-weight-light caption"  >
                  PRESTAMOS QUE ESTA GARANTIZANDO PVT:
                </v-col>
                <v-col cols="12" md="2" class="font-weight-black caption py-0" >
                  {{affiliate_garantor.active_guarantees_quantity}}
                </v-col>
                <v-col cols="12" md="8" class="text-uppercase py-0 font-weight-light caption" >
                  PRESTAMOS VIGENTES QUE TIENE EL AFILIADO EN EL PVT:
                </v-col>
                 <v-col cols="12" md="2" class="font-weight-black caption py-1">
                 {{loan.length}}
                </v-col>
                <v-col cols="12" md="8" class="text-uppercase py-0 font-weight-light caption"  >
                  PRESTAMOS QUE ESTA GARANTIZANDO EN EL SISMU:
                </v-col>
                <v-col cols="12" md="2" class="font-weight-black caption py-0" >
                  {{affiliate_garantor.guarantees_sismu.length}}
                </v-col>
                <v-col cols="12" md="8" class="text-uppercase py-0 font-weight-light caption" >
                  PRESTAMOS VIGENTES QUE TIENE EL AFILIADO EN EL SISMU:
                </v-col>
                 <v-col cols="12" md="2" class="font-weight-black caption py-1">
                 {{affiliate_garantor.loans_sismu.length}}
                </v-col>
                <v-col cols="12" class="py-0" v-show="loan.length>0">
                  <v-data-table
                    dense
                    :headers="headers"
                    :items="loan"
                    :items-per-page="4"
                    hide-default-footer
                  >
                  </v-data-table>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
          <br>
          <v-card v-show="show_result">
            <v-container >
              <v-row>
                <v-col cols="12" md="12" >
                  <v-card-text class="py-0">
                    <v-layout row wrap>
                      <v-flex xs6 class="px-2">
                        <fieldset class="pa-3" >
                          <h1 class="caption" >{{'Cantidad de garantes faltantes a añadir: '}}{{modalidad_guarantors-guarantor_detail.length}}</h1>
                          <v-progress-linear></v-progress-linear>
                          <h1 class="caption" v-show="cont=0">{{'Calcular el porcentaje de pago del garante'}}</h1>
                          <p class="py-0 mb-0 caption" v-show="show_data">Liquido Total:{{evaluate_garantor.payable_liquid_calculated | moneyString}}<br>
                           Total de Bonos:{{evaluate_garantor.bonus_calculated | moneyString}}<br>
                           Liquido para la Calificacion:{{evaluate_garantor.liquid_qualification_calculated | moneyString}}</p>
                          <p class="py-0 mb-0 caption" v-show="show_data">Indice de Endeudamiento: {{evaluate_garantor.indebtnes_calculated|percentage }}%<br>Monto Ajustable: {{ amount_adjustable | moneyString}}<br> Liquido Restante para garantias: {{evaluate_garantor.liquid_rest | moneyString}}<br><b>{{evaluate_garantor.is_valid?'Cubre la Cuota ':'No Cubre la Cuota'}}</b></p>
                          <div class="text-right"  v-show="evaluate_garantor.is_valid">
                            <v-btn
                              v-if="show_data"
                              v-show="guarantor_detail.length < modalidad_guarantors"
                              x-small
                              class="py-0"
                              color="info"
                              rounded
                              @click="addGuarantor()">Añadir Garante
                            </v-btn>
                          </div>
                        </fieldset>
                      </v-flex>
                      <v-flex xs6 class="px-2" >
                        <fieldset class="pa-3 caption">
                          {{'Cantidad de garantes a añadir: '+modalidad_guarantors}}
                              <v-progress-linear></v-progress-linear>
                          <div
                            class="align-end font-weight-light ma-0 pa-0"
                            v-for="(otherDoc, index) of guarantor_detail"
                            :key="index"
                          >
                            {{index+1 +". "}} {{otherDoc}}
                            <v-btn text icon color="error" @click.stop="deleteGuarantor(index)">X</v-btn>
                              <v-divider></v-divider>
                          </div>
                          <div class="text-right"  v-if="modalidad_guarantors==guarantor_detail.length">
                            <v-btn
                              class="py-0"
                              color="info"
                              rounded
                               x-small
                              @click="simulator()">Calculo de Cuota
                            </v-btn>
                          </div>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-card-text>
                </v-col>
                <v-col cols="12" md="12" v-show="show_simulador">
                  <v-card-text class="py-0">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <v-row class='py-0'>
                            <v-col cols="12" md="4" style="color:teal" class='py-0'>
                              Monto del Prestamo: {{loan_detail.amount_requested}}
                            </v-col>
                            <v-col cols="12" md="4" style="color:teal" class='py-0'>
                              Plazo del Prestamo:{{loan_detail.months_term}}
                            </v-col>
                            <v-col cols="12" md="4" style="color:teal" class='py-0'>
                              Cuota del Titular:{{simulator_guarantors.quota_calculated_estimated_total  }}
                            </v-col>
                          </v-row>
                            <ul style="list-style: none" class="pa-0">
                              <li v-for="(afiliados,i) in simulator_guarantors.affiliates" :key="i" >
                                <v-progress-linear></v-progress-linear>
                                 <v-row>
                                  <v-col cols="12" md="12" class="py-0">
                                    Nombre del Afiliado: {{ guarantor_detail[i]}}
                                  </v-col>
                                  <v-col cols="12" md="3" class="py-0">
                                    Cuota: {{afiliados.quota_calculated}}
                                  </v-col>
                                   <v-col cols="12" md="5" class="py-0">
                                    Indice de Endeudamiento: {{afiliados.indebtedness_calculated|percentage }}%
                                  </v-col>
                                   <v-col cols="12" md="4" class="py-0">
                                    Porcentaje de Pago: {{" "+afiliados.payment_percentage|percentage }}%
                                  </v-col>
                                </v-row>
                               </li>
                            </ul>
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
      </v-card>
    </v-container>
</template>
<script>

  export default {
  name: "loan-guarantor",
   props: {
    loan_detail: {
      type: Object,
      required: true
    },
    global_parameters:{
      type: Object,
      required:true
    },
    guarantors: {
      type: Array,
      required: true
    },
     affiliate: {
      type: Object,
      required: true
    },
    //Cantidad de garantes de acuerdo a la modalidad
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
    },
    data_loan_parent_aux: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    guarantor_ci:null,
    affiliate_garantor:{
      affiliate:{
        category:{},
        affiliate_state:{},
        full_name:null
      },
      guarantees_sismu:[],
      loans_sismu:[]
    },
    affiliate_id:null,
    spouse:{},
    name_guarantor:null,
    double_perception:false,
    type_affiliate:false,
    amount_adjustable_description:null,
    number_diff_month:1,
    period:null,
    back_months:false,
    data_ballots_state_affiliate:null,
    period_ballots:null,
    amount_adjustable:0,
    data_ballots_name:null,
    contributionable:[],
    loan_contributions_adjust_ids:[],
    guarantor_ballots:{},
    data_ballots:[],
    simulator_guarantors:{},
    loan:[],
    index: [],
    guarantor_objet:{},
    guarantor_detail:[],
    guarantor_simulator:[],
    payable_liquid:[0],
    bonos:[0,0,0,0],
    evaluate_garantor:{},
    headers: [
      {
        text: "Codigo",
        class: ["normal", "white--text"],
        align: "left",
        value: "code"
      },
      {
        text: "Monto Aprobado",
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
    //Variables que activan los imputs para editar
    edit_commission:false,
    edit_contributions:true,
    edit_passive:true,
    //Variables que sirven de visualizacion de los paneles y botones
    show_contributios:true,
    show_commission:false,
    show_passive:false,
    spouse_view:false,
    show_data:true,
    show_ajuste:true,
    show_simulador:false,
    show_garante:true,
    show_calculated:false,
    show_result:false,
    show_guarantor_false:true,
    show_guarantor_true:false,
  }),
 watch:{
ver()
{
  addGuarantor()
}
 },
  computed: {
    remake() {
      if(this.$route.params.hash == 'remake'){
        return true
      }else{
        return false
      }
    }
  },
  methods: {
    //Metodo para limpiar los imputs
    async clear()
    {
      this.show_garante=true
      this.show_calculated=false
      this.edit_contributions=true
      this.payable_liquid[0]=0,
      this.bonos[0]=0
      this.bonos[1]=0
      this.bonos[2]=0
      this.bonos[3]=0
      this.amount_adjustable=0
      this.amount_adjustable_description=null
      this.number_diff_month=1
    },
    //Metodo para borrar un garante adicionado
    deleteGuarantor(i) {
      this.show_simulador=false
      this.guarantor_detail.splice(i, 1)
      this.guarantor_simulator.splice(i, 1)
      this.guarantors.splice(i, 1)
      this.contributionable.splice(i, 1)
      this.loan_contributions_adjust_ids.splice(i, 1)

    },
    //Metodo para calcular las boletas
     async calculator() {
      try {
          if(this.amount_adjustable>0){
            if(this.amount_adjustable_description)
            {
              this.show_result=true
              this.show_data=true
                let res = await axios.post(`evaluate_garantor`, {
                procedure_modality_id:this.modalidad_id,
                affiliate_id:this.affiliate_garantor.affiliate.id,
                quota_calculated_total_lender:this.loan_detail.quota_calculated_total_lender,
                remake_evaluation:this.$route.params.hash == 'remake' ? true : false,
                remake_loan_id: this.$route.params.hash == 'remake' ? this.$route.query.loan_id : null,
                contributions: [
                {
                  payable_liquid: parseFloat(this.payable_liquid[0]) + parseFloat(this.amount_adjustable),
                  position_bonus:  this.bonos[2],
                  border_bonus: this.bonos[0],
                  public_security_bonus: this.bonos[3],
                  east_bonus:this.bonos[1]
                }
                ]
              })
              this.evaluate_garantor= res.data
              this.loan_detail.simulador=false
            }else{
              this.toastr.error("Tiene que ingresar la descripcion del ajuste.")
            }
          }else{
            this.show_result=true
            this.show_data=true
            let res = await axios.post(`evaluate_garantor`, {
              procedure_modality_id:this.modalidad_id,
              affiliate_id:this.affiliate_garantor.affiliate.id,
              quota_calculated_total_lender:this.loan_detail.quota_calculated_total_lender,
              contributions: [
              {
                payable_liquid: parseFloat(this.payable_liquid[0]) + parseFloat(this.amount_adjustable),
                position_bonus:  this.bonos[2],
                border_bonus: this.bonos[0],
                public_security_bonus: this.bonos[3],
                east_bonus:this.bonos[1]
              }
              ]
            })
            this.evaluate_garantor= res.data
            this.loan_detail.simulador=false
          }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Metodo para sacar el porcentage de pago segun la cantidad de garantes
    async simulator() {
      try {
        this.show_simulador=true
        let res = await axios.post(`simulator`, {
          procedure_modality_id: this.modalidad_id,
          amount_requested: this.loan_detail.amount_requested,
          months_term: this.loan_detail.months_term,
          guarantor: true,
          liquid_qualification_calculated_lender: this.loan_detail.liquid_qualification_calculated + this.amount_adjustable,
          liquid_calculated:this.guarantor_simulator
        })
        this.simulator_guarantors = res.data
        for (this.j = 0; this.j< this.guarantors.length; this.j++) {
          this.guarantors[this.j].payment_percentage=this.simulator_guarantors.affiliates[this.j].payment_percentage
          this.guarantors[this.j].indebtedness_calculated=this.simulator_guarantors.affiliates[this.j].indebtedness_calculated
          this.guarantors[this.j].liquid_qualification_calculated=this.simulator_guarantors.affiliates[this.j].liquid_qualification_calculated
          this.guarantors[this.j].quota_treat=this.simulator_guarantors.affiliates[this.j].quota_calculated
        }
      this.loan_detail.simulador=true
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para la busqueda de garante por ci y matricula
    async searchGuarantor()
    {
      this.clear()
      try {
        if(this.guarantor_ci==this.affiliate.identity_card)
        {
          this.toastr.error("El garante no puede tener el mismo carnet que el titular.")
        }
        else
        {
          let resp = await axios.post(`affiliate_guarantor`,{
            identity_card: this.guarantor_ci,
            procedure_modality_id:this.modalidad_id,
            type_guarantor_spouse:this.type_affiliate,
            remake_evaluation:this.$route.params.hash == 'remake' ? true : false,
            remake_loan_id: this.$route.params.hash == 'remake' ? this.$route.query.loan_id : 0
          })
             if(resp.data.validate)
            {
              this.toastr.error(resp.data.validate)
            }else{
              this.affiliate_garantor=resp.data
               this.double_perception= this.affiliate_garantor.double_perception
              if(!this.double_perception)
              {
               if(this.affiliate_garantor.affiliate.spouse == null  )
              {
                this.spouse_view = false
                this.name_guarantor=this.affiliate_garantor.affiliate.full_name
                this.affiliate_id=this.affiliate_garantor.affiliate.id
              }else{
                this.spouse_view = true
                this.spouse=this.affiliate_garantor.affiliate.spouse
                this.name_guarantor=this.$options.filters.fullName(this.affiliate_garantor.affiliate.spouse, true)

                  if(this.affiliate_garantor.double_perception)
                  {
                    if(this.type_affiliate == false)
                    {
                      this.affiliate_id=this.affiliate_garantor.affiliate.id
                    }else{
                      this.affiliate_id=this.spouse.affiliate_id
                    }
                  }else{
                      this.affiliate_id=this.affiliate_garantor.affiliate.id
                  }
                }
                 let res = await axios.get(`affiliate/${this.affiliate_id}/contribution`, {
                params:{
                  city_id: this.$store.getters.cityId,
                  choose_diff_month: 1,
                  number_diff_month:1,
                  sortBy: ['month_year'],
                  sortDesc: [1],
                  per_page: 1,
                  page: 1,
                  }
                })
              this.data_ballots=res.data.data
              this.data_ballots_name=res.data.name_table_contribution
              this.data_ballots_state_affiliate=res.data.state_affiliate
              this.period=this.$moment(res.data.current_tiket).format('YYYY-MM-DD')
              if(res.data.name_table_contribution=='contributions')
              {
                if(res.data.valid)
                {
                 this.period_ballots=this.$moment(this.data_ballots[0].month_year).format('MMMM')

                  this.edit_contributions=false
                  this.show_contributios=true
                  this.show_commission=false
                  this.show_passive=false
                  this.back_months=true
                  this.show_ajuste=true
                  this.payable_liquid[0] = this.data_ballots[0].payable_liquid,
                  this.bonos[0] = this.data_ballots[0].border_bonus,
                  this.bonos[1] = this.data_ballots[0].east_bonus,
                  this.bonos[2] = this.data_ballots[0].position_bonus,
                  this.bonos[3] = this.data_ballots[0].public_security_bonus
                } else{

                  this.period_ballots=this.$moment(res.data.current_tiket).format('MMMM')

                  this.edit_contributions=false
                  this.show_contributios=true
                  this.show_commission=false
                  this.show_ajuste=true
                  this.show_passive=false
                  this.back_months=true
                  this.payable_liquid[0] = this.payable_liquid[0]
                  this.bonos[0] = this.bonos[0]
                  this.bonos[1] =this.bonos[1]
                  this.bonos[2] = this.bonos[2]
                  this.bonos[3] =this.bonos[3]
                }
              }else{
                if(res.data.name_table_contribution=='aid_contributions')
                {
                  this.show_passive= true
                  this.show_contributios=false
                  this.back_months=false
                  this.show_commission=false
                  this.show_ajuste=true

                if(res.data.valid)
                {
                  this.period_ballots=this.$moment(this.data_ballots[0].month_year).format('MMMM')
                  if(this.data_ballots[0].rent==0 && this.data_ballots[0].dignity_rent==0){
                    this.edit_contributions=true
                    this.edit_passive= true
                  }else{
                    if(this.data_ballots[0].dignity_rent>0 && this.data_ballots[0].rent>0){
                      this.edit_contributions=false
                      this.edit_passive= false
                      this.payable_liquid[0] = this.data_ballots[0].rent
                      this.bonos[0] = this.data_ballots[0].dignity_rent
                    }else{
                      if(this.data_ballots[0].dignity_rent==0){
                        this.edit_passive= true
                        this.edit_contributions=false
                        this.payable_liquid[0] = this.data_ballots[0].rent
                      }else{
                        if(this.data_ballots[0].rent==0){
                          this.edit_contributions=true
                          this.edit_passive= false
                          this.bonos[0] = this.data_ballots[0].dignity_rent
                        }
                      }
                    }
                  }
                } else{
                  this.period_ballots=this.$moment(res.data.current_tiket).format('MMMM')
                  this.edit_contributions=true
                  this.show_ajuste=true
              }
            }
            else{
              if(res.data.name_table_contribution==null)
              {
                this.period_ballots=this.$moment(res.data.current_tiket).format('MMMM')
                this.show_commission=true
                this.show_contributios=false
                this.show_passive=false
                this.show_ajuste=false
                this.back_months=false
              }
            }
          }
            this.show_guarantor_false=this.affiliate_garantor.guarantor
            this.show_guarantor_true=this.affiliate_garantor.guarantor
            this.show_calculated=this.affiliate_garantor.guarantor
            this.loan=this.affiliate_garantor.affiliate.loans
            this.show_garante=false

            }
          }
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para añadir al garante
    async addGuarantor()
    {
       let resp = await axios.post(`affiliate_guarantor`,{
            identity_card: this.guarantor_ci,
            procedure_modality_id:this.modalidad_id,
            type_guarantor_spouse:this.type_affiliate,
            remake_evaluation:this.$route.params.hash == 'remake' ? true : false,
            remake_loan_id: this.$route.params.hash == 'remake' ? this.$route.query.loan_id : 0
          })
      this.affiliate_garantor.guarantor_information=resp.data.guarantor_information

      if(this.affiliate_garantor.guarantor_information==true)
      {
      this.guarantor_ci=null
      this.show_data=false
      this.guarantor_ballots.affiliate_id=this.affiliate_garantor.affiliate.id
      this.guarantor_ballots.liquid_qualification_calculated=this.evaluate_garantor.liquid_qualification_calculated
      this.guarantor_objet.affiliate_id=this.affiliate_garantor.affiliate.id
      this.guarantor_objet.bonus_calculated=this.evaluate_garantor.bonus_calculated
      this.guarantor_objet.payable_liquid_calculated=this.evaluate_garantor.payable_liquid_calculated

      if(this.guarantors.length>0){
        if(this.guarantor_ballots.affiliate_id == this.guarantors[0].affiliate_id){
          this.toastr.error("Este afiliado ya se encuentra registrado.")
        }else{
          if(this.data_ballots_name==null)
      {
          let res = await axios.post(`loan_contribution_adjust`, {
            affiliate_id: this.affiliate_garantor.affiliate.id,
            adjustable_id:this.affiliate_garantor.affiliate.id,
            adjustable_type: 'afilliates',
            type_affiliate:'guarantor',
            amount: this.payable_liquid[0],
            type_adjust: 'liquid',
            period_date: this.period,
            description: 'Liquido Pagable Comision',
          })
        this.guarantor_objet.contributionable_ids=this.contributionable
        this.guarantor_objet.contributionable_type='loan_contribution_adjusts'
        this.loan_contributions_adjust_ids.push(res.data.id)
        this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
      }else{
        if(this.data_ballots_state_affiliate=='Pasivo'){
          let res_aid_contribution = await axios.post(`aid_contribution/updateOrCreate`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              month_year: this.period,
              rent: this.payable_liquid[0],
              dignity_rent: this.bonos[0],
          })
          this.contributionable.push(res_aid_contribution.data.id)
          this.guarantor_objet.contributionable_ids=this.contributionable
          this.guarantor_objet.contributionable_type=this.data_ballots_name

          if(this.amount_adjustable>0){
            let res_adjust_pasivo= await axios.post(`loan_contribution_adjust`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              adjustable_id:res_aid_contribution.data.id,
              adjustable_type: this.data_ballots_name,
              type_affiliate:'guarantor',
              amount: this.amount_adjustable,
              type_adjust: 'adjust',
              period_date: this.period,
              description: this.amount_adjustable_description,
            })
            this.loan_contributions_adjust_ids.push(res_adjust_pasivo.data.id)
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }else{
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }
        }else{

          this.contributionable.push(this.data_ballots[0].id)
          this.guarantor_objet.contributionable_ids=this.contributionable
          this.guarantor_objet.contributionable_type=this.data_ballots_name

          if(this.amount_adjustable>0){
            let res_adjust_activo = await axios.post(`loan_contribution_adjust`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              adjustable_id:this.data_ballots[0].id,
              adjustable_type: this.data_ballots_name,
              type_affiliate:'guarantor',
              amount: this.amount_adjustable,
              type_adjust: 'adjust',
              period_date: this.period,
              description: this.amount_adjustable_description,
            })
            this.loan_contributions_adjust_ids.push(res_adjust_activo.data.id)
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }else{
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }
        }
      }
        this.guarantor_detail.push(this.name_guarantor);
        this.guarantor_simulator.push(this.guarantor_ballots);
        this.guarantors.push(this.guarantor_objet);

        }
      }else{
        if(this.data_ballots_name==null)
      {
          let res = await axios.post(`loan_contribution_adjust`, {
            affiliate_id: this.affiliate_garantor.affiliate.id,
            adjustable_id:this.affiliate_garantor.affiliate.id,
            adjustable_type: 'afilliates',
            type_affiliate:'guarantor',
            amount: this.payable_liquid[0],
            type_adjust: 'liquid',
            period_date: this.period,
            description: 'Liquido Pagable Comision',
          })
        this.guarantor_objet.contributionable_ids=this.contributionable
        this.guarantor_objet.contributionable_type='loan_contribution_adjusts'
        this.loan_contributions_adjust_ids.push(res.data.id)
        this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
      }else{
        if(this.data_ballots_state_affiliate=='Pasivo'){
          let res_aid_contribution = await axios.post(`aid_contribution/updateOrCreate`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              month_year: this.period,
              rent: this.payable_liquid[0],
              dignity_rent: this.bonos[0],
          })
          this.contributionable.push(res_aid_contribution.data.id)
          this.guarantor_objet.contributionable_ids=this.contributionable
          this.guarantor_objet.contributionable_type=this.data_ballots_name

          if(this.amount_adjustable>0){
            let res_adjust_pasivo= await axios.post(`loan_contribution_adjust`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              adjustable_id:res_aid_contribution.data.id,
              adjustable_type: this.data_ballots_name,
              type_affiliate:'guarantor',
              amount: this.amount_adjustable,
              type_adjust: 'adjust',
              period_date: this.period,
              description: this.amount_adjustable_description,
            })
            this.loan_contributions_adjust_ids.push(res_adjust_pasivo.data.id)
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }else{
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }
        }else{

          this.contributionable.push(this.data_ballots[0].id)
          this.guarantor_objet.contributionable_ids=this.contributionable
          this.guarantor_objet.contributionable_type=this.data_ballots_name

          if(this.amount_adjustable>0){
            let res_adjust_activo = await axios.post(`loan_contribution_adjust`, {
              affiliate_id: this.affiliate_garantor.affiliate.id,
              adjustable_id:this.data_ballots[0].id,
              adjustable_type: this.data_ballots_name,
              type_affiliate:'guarantor',
              amount: this.amount_adjustable,
              type_adjust: 'adjust',
              period_date: this.period,
              description: this.amount_adjustable_description,
            })
            this.loan_contributions_adjust_ids.push(res_adjust_activo.data.id)
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }else{
            this.guarantor_objet.loan_contributions_adjust_ids=this.loan_contributions_adjust_ids
          }
        }
      }
        this.guarantor_detail.push(this.name_guarantor);
        this.guarantor_simulator.push(this.guarantor_ballots);
        this.guarantors.push(this.guarantor_objet);
      }
        this.guarantor_ballots={}
        this.guarantor_objet={}
        this.loan_contributions_adjust_ids=[]
        this.contributionable=[]
        this.clear()
        console.log(this.guarantors)
    }else{
          this.toastr.error("Tiene que actualizar los datos personales del Garante.")
    }
    },
    //Metodo para retroceder las contribuciones del garante
    async backContributions(){
          let res = await axios.get(`affiliate/${this.affiliate_garantor.affiliate.id}/contribution`, {
          params:{
            city_id: this.$store.getters.cityId,
            choose_diff_month: 1,
            number_diff_month:this.number_diff_month,
            sortBy: ['month_year'],
            sortDesc: [1],
            per_page: 1,
            page: 1,
          }
          })
          this.data_ballots=res.data.data
           if(res.data.valid)
            {
              this.period_ballots = this.$moment(this.data_ballots[0].month_year).format('MMMM')
              this.edit_contributions=false
              this.payable_liquid[0] = this.data_ballots[0].payable_liquid,
              this.bonos[0] = this.data_ballots[0].border_bonus,
              this.bonos[1] = this.data_ballots[0].east_bonus,
              this.bonos[2] = this.data_ballots[0].position_bonus,
              this.bonos[3] = this.data_ballots[0].public_security_bonus
            } else{
              this.period_ballots = this.$moment(res.data.current_tiket).format('MMMM')
              this.edit_contributions=false
              this.back_months=true
              this.payable_liquid[0] = 0
              this.bonos[0] = 0
              this.bonos[1] = 0
              this.bonos[2] = 0
              this.bonos[3] = 0
            }
    },
  //Retrocede las contribuciones
  appendIconCallback () {
      if(this.number_diff_month < this.global_parameters.max_months_go_back){
      this.number_diff_month++
      this.choose_diff_month = 1
      this.backContributions()
    }
  },
  //Aumenta las contribuciones
  prependIconCallback () {

      if(this.number_diff_month > 1){
      this.number_diff_month--
      this.choose_diff_month = 1
      this.backContributions()
    }
  },
  //Metodo para sacar las contribusiones cuando es doble percepcion
  async contributionChange(){
  try {
    if(this.type_affiliate)
      {
        let resp = await axios.post(`affiliate_spouse_guarantor`,{
            affiliate_id:  this.affiliate_garantor.affiliate.id,
            procedure_modality_id:this.modalidad_id,
          })
        this.affiliate_garantor=resp.data
       }else{
        let resp = await axios.post(`affiliate_spouse_guarantor`,{
            affiliate_id:  this.affiliate_garantor.own_affiliate.id,
            procedure_modality_id:this.modalidad_id,
          })
        this.affiliate_garantor=resp.data
      }
      if(this.affiliate_garantor.affiliate.spouse == null  )
      {
        this.spouse_view = false
        this.name_guarantor=this.affiliate_garantor.affiliate.full_name
        this.affiliate_id=this.affiliate_garantor.affiliate.id
      }else{

        this.spouse_view = true
        this.spouse=this.affiliate_garantor.affiliate.spouse
        this.name_guarantor=this.$options.filters.fullName(this.affiliate_garantor.affiliate.spouse, true)

              if(this.affiliate_garantor.double_perception)
              {
                if(this.type_affiliate == false)
                {
                  this.affiliate_id=this.affiliate_garantor.affiliate.id
                }else{
                  this.affiliate_id=this.spouse.affiliate_id
                }
              }else{
                  this.affiliate_id=this.affiliate_garantor.affiliate.id
              }
          }
          let res = await axios.get(`affiliate/${this.affiliate_id}/contribution`, {
            params:{
              city_id: this.$store.getters.cityId,
              choose_diff_month: 1,
              number_diff_month:1,
              sortBy: ['month_year'],
              sortDesc: [1],
              per_page: 1,
              page: 1,
              }
          })
          this.data_ballots=res.data.data
          this.data_ballots_name=res.data.name_table_contribution
          this.data_ballots_state_affiliate=res.data.state_affiliate
          this.period=this.$moment(res.data.current_tiket).format('YYYY-MM-DD')
          if(res.data.name_table_contribution=='contributions')
          {
          // this.toastr.error("afiliado que pertenece a contribution")
            if(res.data.valid)
            {
              this.edit_contributions=false
              this.show_contributios=true
              this.show_commission=false
              this.show_passive=false
              this.back_months=true
              this.show_ajuste=true
              this.payable_liquid[0] = this.data_ballots[0].payable_liquid,
              this.bonos[0] = this.data_ballots[0].border_bonus,
              this.bonos[1] = this.data_ballots[0].east_bonus,
              this.bonos[2] = this.data_ballots[0].position_bonus,
              this.bonos[3] = this.data_ballots[0].public_security_bonus
            } else{
              this.edit_contributions=false
              this.show_contributios=true
              this.show_commission=false
              this.show_ajuste=true
              this.show_passive=false
              this.back_months=true
              this.payable_liquid[0] = this.payable_liquid[0]
              this.bonos[0] = this.bonos[0]
              this.bonos[1] =this.bonos[1]
              this.bonos[2] = this.bonos[2]
              this.bonos[3] =this.bonos[3]
            }
          }else{
            if(res.data.name_table_contribution=='aid_contributions')
            {
              this.show_passive= true
              this.show_contributios=false
              this.back_months=false
              this.show_commission=false
              this.show_ajuste=true

            if(res.data.valid)
            {
              if(this.data_ballots[0].rent==0 && this.data_ballots[0].dignity_rent==0){
                this.edit_contributions=true
                this.edit_passive= true
              }else{
                if(this.data_ballots[0].dignity_rent>0 && this.data_ballots[0].rent>0){
                  this.edit_contributions=false
                  this.edit_passive= false
                  this.payable_liquid[0] = this.data_ballots[0].rent
                  this.bonos[0] = this.data_ballots[0].dignity_rent
                }else{
                  if(this.data_ballots[0].dignity_rent==0){
                    this.edit_passive= true
                    this.edit_contributions=false
                    this.payable_liquid[0] = this.data_ballots[0].rent
                  }else{
                    if(this.data_ballots[0].rent==0){
                      this.edit_contributions=true
                      this.edit_passive= false
                      this.bonos[0] = this.data_ballots[0].dignity_rent
                    }
                  }
                }
              }
            } else{
                this.edit_contributions=true
                this.show_ajuste=true
              }
            }
            else{
              if(res.data.name_table_contribution==null)
              {
                this.show_commission=true
                this.show_contributios=false
                this.show_passive=false
                this.show_ajuste=false
                this.back_months=false
              }

            }
          }
            this.show_guarantor_false=this.affiliate_garantor.guarantor
            this.show_guarantor_true=this.affiliate_garantor.guarantor
            this.show_calculated=this.affiliate_garantor.guarantor
            this.loan=this.affiliate_garantor.affiliate.loans
            this.show_garante=false
            this.$forceUpdate();

          } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
      }
    }
  }
</script>