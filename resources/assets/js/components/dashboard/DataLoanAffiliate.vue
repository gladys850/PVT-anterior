<template>
  <v-card flat>
    <v-card-text>
      <v-container class="py-0">
        <v-card color="grey lighten-1" class="ma-0 pa-3">
          <v-row>
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
                        :loading="loading"
                        @keyup.enter="getCalculator()"
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
                            @click.stop="getCalculator()"
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
            <v-col cols="12" md="8" v-show="ver">
              <v-card>
                <v-card-text>
                  <v-row>
                    <v-col   class="text-center" cols="12" md="12" >
                      <h3 class="info--text aling-text--center " >
                      DATOS DEL AFILIADO
                      </h3>
                    </v-col>
                     <v-progress-linear></v-progress-linear>
                    <v-col cols="12" md="6" >
                        Nombre: {{loans.affiliate}}
                    </v-col>
                    <v-col cols="12" md="6" >
                        C.I.: {{loans.affiliate_identity_card}}
                    </v-col>
                    <v-col cols="12" md="6" >
                     Estado: {{loans.state_affiliate}}
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col  cols="12" md="12" v-show="evaluacion">
              <v-row>
                <v-col cols="12" md="4">
                  <v-card>
                    <v-card-text>
                      <v-row>
                        <v-col   class="text-center" cols="12" md="12">
                          <h3 class="aling-text--center" style="color:teal">
                          {{loans.modalities[0].name_procedure_modality }}
                          </h3>
                        </v-col>
                        <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Monto Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[0].amount_max +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[0].modality_affiliate.procedure_type.interval.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[0].liquid_calification +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[0].interest.annual_interest/12 +" %"}}
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card>
                    <v-card-text>
                      <v-row>
                      <v-col   class="text-center" cols="12" md="12">
                        <h3 class=" aling-text--center" style="color:teal">
                        {{loans.modalities[1].name_procedure_modality}}
                        </h3>
                      </v-col>
                      <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Monto Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[1].amount_max +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[1].modality_affiliate.procedure_type.interval.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[1].liquid_calification +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[1].interest.annual_interest/12 +" %"}}
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card>
                    <v-card-text>
                    <v-row>
                      <v-col   class="text-center" cols="12" md="12">
                        <h3 class=" aling-text--center" style="color:teal">
                        {{loans.modalities[2].name_procedure_modality}}
                        </h3>
                      </v-col>
                      <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Monto Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[2].amount_max +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[2].modality_affiliate.procedure_type.interval.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[2].liquid_calification +" Bs."}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ loans.modalities[2].interest.annual_interest/12 +" %"}}
                        </v-col>
                    </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-col>
            <v-col v-show="ver">
               <v-col cols="12" md="12" v-show="!evaluacion">
                <v-card>
                  <v-card-text>
                  <v-row>
                    <v-col   class="text-center" cols="12" md="12">
                      <!--h3 class=" aling-text--center" style="color:teal">
                      {{loans.message.accomplished}}
                      </!--h3-->
                    </v-col>
                  </v-row>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-col>
          </v-row>
        </v-card>
      </v-container>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "dashboard-index",
  data: () => ({
    affiliate_ci:null,
    loading: false,
    largo_plazo:{},
    corto_plazo:{},
    anticipo:{},
    cont:0,
    loans: {
      modalities:[
        {
          name_procedure_modality:{},
          interest:{},
          modality_affiliate:{
            procedure_type:{
              interval:{}
            }
          }
        },
        {
          name_procedure_modality:{},
          interest:{},
          modality_affiliate:{
            procedure_type:{
              interval:{}
            }
          }
        },
        {
          name_procedure_modality:{},
          interest:{},
          modality_affiliate:{
            procedure_type:{
              interval:{}
            }
          }
        }
      ],
      message:{
        accomplished:null
      },
      state_affiliate:{
        name:null,
  
      }
   
    },
    ver: false,
    evaluacion:false
  }),

  methods: {
    async getCalculator() {
      try {
        this.loading = false;
         this.ver= true
          this.toastr.error("Entro al metodoooo")
          let res = await axios.post(`search_loan`, {
            identity_card : this.affiliate_ci });
            this.loans=res.data
            this.cont=this.loans.modalities.length
            this.evaluacion=res.data.evaluate
              this.$forceUpdate()
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },

  },
};
</script>
