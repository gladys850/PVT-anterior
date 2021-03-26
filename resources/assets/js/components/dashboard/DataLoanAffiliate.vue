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
                        Nombre: {{loans.name}}
                    </v-col>
                    <v-col cols="12" md="6" >
                        C.I.: {{loans.ci}}
                    </v-col>
                    <v-col cols="12" md="6" >
                        Estado:
                    </v-col>
                    <v-col cols="12" md="6" >
                        Categoria:
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
               <v-col cols="12" md="4" v-show="ver">
              <v-card>
                <v-card-text>
                  <v-row>
                    <v-col   class="text-center" cols="12" md="12">
                      <h3 class="aling-text--center" style="color:teal">
                      ANTICIPO
                      </h3>
                    </v-col>
                     <v-progress-linear></v-progress-linear>
                    <v-col cols="12" md="8" >
                        Liquido para Calificacion:
                    </v-col>
                    <v-col cols="12" md="4" >
                        {{ loans.ci }}
                    </v-col>
                    <v-col cols="12" md="8" >
                        Interes:
                    </v-col>
                    <v-col cols="12" md="4" >
                        {{ loans.ci }}
                    </v-col>
                    <v-col cols="12" md="8" >
                        Monto Maximo:
                    </v-col>
                    <v-col cols="12" md="4" >
                        {{ loans.ci }}
                    </v-col>
                    <v-col cols="12" md="8" >
                        Plazo Maximo:
                    </v-col>
                    <v-col cols="12" md="4" >
                        {{ loans.ci }}
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
             <v-col cols="12" md="4" v-show="ver">
              <v-card>
                <v-card-text>
                  <v-row>
                  <v-col   class="text-center" cols="12" md="12">
                    <h3 class=" aling-text--center" style="color:teal">
                     CORTO PLAZO
                    </h3>
                  </v-col>
                   <v-progress-linear></v-progress-linear>
                   <v-col cols="12" md="8" >
                      Liquido para Calificacion:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                   <v-col cols="12" md="8" >
                      Interes:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                  <v-col cols="12" md="8" >
                      Monto Maximo:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                   <v-col cols="12" md="8" >
                      Plazo Maximo:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-col>
             <v-col cols="12" md="4" v-show="ver">
              <v-card>
                <v-card-text>
                <v-row>
                  <v-col   class="text-center" cols="12" md="12">
                    <h3 class=" aling-text--center" style="color:teal">
                     LARGO PLAZO
                    </h3>
                  </v-col>
                   <v-progress-linear></v-progress-linear>
                   <v-col cols="12" md="8" >
                      Liquido para Calificacion:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                   <v-col cols="12" md="8" >
                      Interes:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                  <v-col cols="12" md="8" >
                      Monto Maximo:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                   <v-col cols="12" md="8" >
                      Plazo Maximo:
                  </v-col>
                  <v-col cols="12" md="4" >
                      {{ loans.ci }}
                  </v-col>
                </v-row>
                </v-card-text>
              </v-card>
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
    loans: {
      name:'Mario',
      ci:'8566'
    },
    ver: false,
  }),

  methods: {
    async getCalculator() {
      try {
        this.loading = true;
         this.ver= true,
         this.toastr.error("Entro al metodo")
        
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },

  },
};
</script>
