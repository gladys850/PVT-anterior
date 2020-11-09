<template>
  <v-container fluid >
    <v-row justify="center"  v-show="modalidad.procedure_type_id!=12">
         <v-col cols="12" class="py-0" >
          <v-card v-show="show_garante">
            <v-container v-if="modalidad_guarantors==0">
              <v-row>
                <v-col class="text-center">
                  <h2 class="success--text" >ESTA MODALIDAD NO NECESITA GARANTE</h2>
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
              <v-col cols="12" md="12" class="pb-0">
                <v-layout row wrap>
                  <v-flex xs12 class="px-2">
                    <fieldset class="pa-3">
                      <v-text-field
                        :outlined="editar"
                        :readonly="!editar"
                        dense
                        class="py-0"
                        label="Boleta de Pago"
                        v-model="payable_liquid[0]"
                      ></v-text-field>
                      <v-text-field
                      :outlined="editar"
                        :readonly="!editar"
                         dense
                        class="py-0"
                        v-model="bonos[0]"
                        label="Bono Frontera"
                      ></v-text-field>
                      <v-text-field
                      :outlined="editar"
                        :readonly="!editar"
                         dense
                        class="py-0"
                        v-model="bonos[1]"
                        label="Bono Oriente"
                      ></v-text-field>
                      <v-text-field
                      :outlined="editar"
                        :readonly="!editar"
                         dense
                        class="py-0"
                        v-model="bonos[2]"
                        label="Bono Cargo"
                      ></v-text-field>
                      <v-text-field
                      :outlined="editar"
                        :readonly="!editar"
                         dense
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
        <v-col cols="8" class="py-0" >
          <v-card v-show="show_garante">
            <v-container v-if="modalidad_guarantors>0">
              <v-row>
                <v-col class="text-center">
                    <h4 class="error--text" > CANTIDAD DE GARANTES QUE NECESITA ESTA MODALIDAD:{{modalidad_guarantors}}<br>
                  EL GARANTE DEBE ESTAR ENTRE UNA CATEGORIA DE {{loan_detail.min_guarantor_category}} A {{loan_detail.max_guarantor_category}} </h4>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
          <v-card v-show="!show_garante" >
            <v-container class="py-0">
              <v-row>
                <v-col cols="12" md="4"></v-col>
                <v-col cols="12" md="6"  class="py-0" >
                  <h3 class="red--text" v-show="!validated">NO PUEDE SER GARANTE</h3>
                  <h3 class="success--text" v-show="validated1"> PUEDE SER GARANTE</h3>
                  <!--{{affiliate_garantor.guarantor_information}}-->
                </v-col>
                <v-col cols="12" md="12" class="py-0" v-show="affiliate_garantor.affiliate.cpop">
                  <h5 class="success--text text-center">AFILIADO CPOP</h5>
                </v-col>
                <v-col cols="12" md="6" class="ma-0 pb-0 font-weight-light caption">
                  AFILIADO :{{affiliate_garantor.affiliate.full_name}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 pb-0 font-weight-light caption" >
                  C.I :{{affiliate_garantor.affiliate.identity_card_ext}}
                </v-col>
                <v-col cols="12" md="3" class="ma-0 pb-0 font-weight-light caption" >
                  CATEGORIA:   {{affiliate_garantor.affiliate.category.name}}
                </v-col>
                <v-col cols="12" md="6" class="py-0 font-weight-light caption" >
                  MATRICULA:{{affiliate_garantor.affiliate.registration}}
                </v-col>
                <v-col cols="12" md="6" class="text-uppercase py-0 font-weight-light caption">
                  ESTADO:{{affiliate_garantor.affiliate.affiliate_state.name}}
                </v-col>
                <v-col cols="12" md="8" class="font-weight-black caption py-0" >
                  PRESTAMOS QUE ESTA GARANTIZANDO:
                </v-col>
                <v-col cols="12" md="2" class="font-weight-black caption py-0" >
                  {{affiliate_garantor.active_guarantees_quantity}}
                </v-col>
                <v-col cols="12" md="8" class="font-weight-black caption py-1">
                  PRESTAMOS VIGENTES QUE TIENE EL AFILIADO:
                </v-col>
                 <v-col cols="12" md="2" class="font-weight-black caption py-1">
                 {{loan.length}}
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
                          <h1 class="caption" >{{'Cantidad de garantes faltantes a añadir: '}}{{modalidad_guarantors-garantes_detalle.length}}</h1>
                          <v-progress-linear></v-progress-linear>
                          <h1 class="caption" v-show="cont=0">{{'Calcular el porcentaje de pago del garante'}}</h1>
                          <p class="py-0 mb-0 caption" v-show="show_data">Liquido Total:{{evaluate_garantor.payable_liquid_calculated +' '}}<br>
                           Total de Bonos:{{evaluate_garantor.bonus_calculated +' '}}<br>
                           Liquido para la Calificacio:{{evaluate_garantor.liquid_qualification_calculated }}</p>
                          <p class="py-0 mb-0 caption" v-show="show_data">Indice de Endeudamineto: {{evaluate_garantor.indebtnes_calculated+'% '}}<br> <b>{{evaluate_garantor.is_valid?'Cubre la Cuota ':'No Cubre la Cuota'}}</b></p>
                          <div class="text-right"  v-show="evaluate_garantor.is_valid">
                            <v-btn
                              v-if="show_data"
                              v-show="garantes_detalle.length < modalidad_guarantors"
                              x-small
                              class="py-0"
                              color="info"
                              rounded
                              @click="añadir()">Añadir Garante
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
                            v-for="(otherDoc, index) of garantes_detalle"
                            :key="index"
                          >
                            {{index+1 +". "}} {{otherDoc}}
                            <v-btn text icon color="error" @click.stop="deleteOtherDocument(index)">X</v-btn>
                              <v-divider></v-divider>
                          </div>
                          <div class="text-right"  v-if="modalidad_guarantors==garantes_detalle.length">
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
                <v-col cols="12" md="12" v-show="show_simulador">
                  <v-card-text class="py-0">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <p class="py-0 mb-0 caption">Monto del Prestamo: {{loan_detail.amount_requested +' '}}<b>|</b> Plazo del Prestamo:{{loan_detail.months_term+' '}}<b>|</b> Cuota del Titular:{{simulator_guarantors.quota_calculated_estimated_total  }}</p>
                            <ul style="list-style: none" class="pa-0">
                              <li v-for="(afiliados,i) in simulator_guarantors.affiliates" :key="i" >
                                <v-progress-linear></v-progress-linear>
                                <p class="py-0 mb-0 caption">Nombre del Afiliado: {{ garantes_detalle[i]}}</p>
                                <p class="py-0 mb-0 caption">Cuota: {{afiliados.quota_calculated+"  "}}{{"  "+"Indice de Endeudamiento:"+afiliados.indebtedness_calculated}}{{" "}}Porcentaje de Pago: {{" "+afiliados.payment_percentage}}%</p>
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
    guarantors: {
      type: Array,
      required: true
    },
     affiliate: {
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
    affiliate_garantor_aux:{
      affiliate:{
        category:{},
        affiliate_state:{}
      },
    },
    editar:true,
    show_data:true,
    show_simulador:false,
    garante_boletas:{},
    data_ballots:[],
    show_garante:true,
    show_calculated:false,
    show_result:false,
    show_evaluated:false,
    simulator_guarantors:{},
    loan:[],
    index: [],
    guarantor_objeto:{},
    garantes_detalle:[],
    garantes_simulador:[],
    guarantor:[null],
    validated:true,
    validated1:false,
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
 watch:{
ver()
{
  añadir()
}
 },
  methods: {
    async clear()
    {
    this.editar=true
    this.payable_liquid[0]=0,
    this.bonos[0]=0
    this.bonos[1]=0
    this.bonos[2]=0
    this.bonos[3]=0
    },
    async añadir()
    {
      this.guarantor_ci=null
      this.show_evaluated=false
      this.show_data=false

      this.garante_boletas.affiliate_id=this.affiliate_garantor.affiliate.id
      this.garante_boletas.liquid_qualification_calculated=this.evaluate_garantor.liquid_qualification_calculated
      this.guarantor_objeto.affiliate_id=this.affiliate_garantor.affiliate.id
      this.guarantor_objeto.bonus_calculated=this.evaluate_garantor.bonus_calculated
      this.guarantor_objeto.payable_liquid_calculated=this.evaluate_garantor.payable_liquid_calculated
      this.garantes_detalle.push(this.affiliate_garantor.affiliate.full_name);
      this.garantes_simulador.push(this.garante_boletas);
      this.guarantors.push(this.guarantor_objeto);
      this.garante_boletas={}
      this.guarantor_objeto={}
     console.log('entro a garantes ==> '+this.garantes_detalle)
    this.clear()
    },
    deleteOtherDocument(i) {
      this.garantes_detalle.splice(i, 1)
      this.garantes_simulador.splice(i, 1)
      this.guarantors.splice(i, 1)

      console.log("other2 " + this.garantes_detalle);
    },
   /* async añadir()
    {
      console.log('entro a garantes')
this.garantes[0]=this.affiliate_garantor.affiliate.id
    this.toastr.success("Se añadio satisfactoriamente al garante.")
console.log('este es el garante'+this.garantes[0])
    },*/
     async calculator() {
      try {
         if(this.affiliate_garantor.guarantor_information==true)
        {
            this.show_result=true
            this.show_evaluated=true
            this.show_data=true
            let res = await axios.post(`evaluate_garantor`, {
                procedure_modality_id:this.modalidad_id,
                affiliate_id:this.affiliate_garantor.affiliate.id,
                quota_calculated_total_lender:this.loan_detail.quota_calculated_total_lender,
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
            this.loan_detail.simulador=false
        }else{
          this.toastr.error("Tiene que actualizar los datos personales del afiliado.")
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    async simulador() {
      try {
      this.show_simulador=true
      let res = await axios.post(`simulator`, {
              procedure_modality_id: this.modalidad_id,
              amount_requested: this.loan_detail.amount_requested,
              months_term: this.loan_detail.months_term,
              guarantor: true,
              liquid_qualification_calculated_lender: this.loan_detail.liquid_qualification_calculated,
              liquid_calculated:this.garantes_simulador
          })
          this.simulator_guarantors = res.data
    for (this.j = 0; this.j< this.guarantors.length; this.j++) {
      this.guarantors[this.j].payment_percentage=this.simulator_guarantors.affiliates[this.j].payment_percentage
      this.guarantors[this.j].indebtedness_calculated=this.simulator_guarantors.affiliates[this.j].indebtedness_calculated
      this.guarantors[this.j].liquid_qualification_calculated=this.simulator_guarantors.affiliates[this.j].liquid_qualification_calculated
      }
      this.loan_detail.simulador=true
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
   async activar()
    {
      this.clear()
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
          this.affiliate_garantor_aux=this.affiliate_garantor

        console.log("esta son boletas del garante"+ this.affiliate_garantor.affiliate.id)
        //let data_ballots=[]
         let res = await axios.get(`affiliate/${this.affiliate_garantor.affiliate.id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          per_page: 1,
          page: 1,
        }
      })
        this.data_ballots=res.data.data

      if(res.data.valid)
      {
        this.editar=false
        this.payable_liquid[0] = this.data_ballots[0].payable_liquid,
        this.bonos[0] = this.data_ballots[0].border_bonus,
        this.bonos[1] = this.data_ballots[0].east_bonus,
        this.bonos[2] = this.data_ballots[0].seniority_bonus,
        this.bonos[3] = this.data_ballots[0].public_security_bonus
      } else{
        this.payable_liquid[0] = this.payable_liquid[0]
        this.bonos[0] = this.bonos[0]
        this.bonos[1] =this.bonos[1]
        this.bonos[2] = this.bonos[2]
        this.bonos[3] =this.bonos[3]
      }
        console.log("esta son boletas del garante"+this.data_ballots)
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