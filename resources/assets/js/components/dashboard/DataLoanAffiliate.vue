<template>
  <v-card flat>
    <v-card-text>
      <v-container class="py-0">
        <v-card color="grey lighten-1" class="ma-0 pa-3">
          <v-row>
            <v-col  cols="12" md="12" v-show="evaluacion">
              <v-row>
                <v-col cols="12" md="4">
                  <v-card>
                    <v-card-text>
                      <v-row>
                        <v-col   class="text-center" cols="12" md="12">
                          <h3 class="aling-text--center" style="color:teal">
                          {{anticipo.name }}
                          </h3>
                        </v-col>
                        <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ anticipo.liquid_calification | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Monto Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ anticipo.amount_max | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ anticipo.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Cuota Calculada:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ anticipo.quota_calculated | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes Mensual:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ (anticipo.annual_interest/12) | percentage}} %
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
                        {{corto_plazo.name}}
                        </h3>
                      </v-col>
                      <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ corto_plazo.liquid_calification | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Monto Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ corto_plazo.amount_max | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Maximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ corto_plazo.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Cuota Calculada:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ corto_plazo.quota_calculated | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes Mensual:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ (corto_plazo.annual_interest/12) | percentage}} %
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
                        {{largo_plazo.name}}
                        </h3>
                      </v-col>
                      <v-progress-linear></v-progress-linear>
                        <v-col cols="12" md="8" >
                            Liquido para Calificacion:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ largo_plazo.liquid_calification | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Monto Máximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ largo_plazo.amount_max | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Plazo Máximo:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ largo_plazo.maximum_term +" meses"}}
                        </v-col>
                        <v-col cols="12" md="8" >
                            Cuota Calculada:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{ largo_plazo.quota_calculated | money}} Bs.
                        </v-col>
                        <v-col cols="12" md="8" >
                            Interes Mensual:
                        </v-col>
                        <v-col cols="12" md="4" >
                            {{(largo_plazo.annual_interest/12) | percentage}} %
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
                      <h3 class=" aling-text--center" style="color:teal">
                      {{loans1.message.accomplished}}
                      </h3>
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
  name: "data-loan-affiliate",
   props: {
    loans1: {
      type: Object,
      required: true
    },
    largo_plazo: {
      type: Object,
      required: true
    },
    corto_plazo: {
      type: Object,
      required: true
    },
    anticipo: {
      type: Object,
      required: true
    },
     ver: {
      type: Boolean,
      required: true
    },
     evaluacion: {
      type: Boolean,
      required: true
    },
  },
};
</script>
