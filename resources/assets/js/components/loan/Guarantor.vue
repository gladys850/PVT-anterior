<template>
  <v-container fluid >
    <v-row justify="center">
      <v-col cols="4" class="py-0" >
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
                      @click.stop="activar()"
                    >
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
      <v-col cols="8" class="py-0" >
        <v-card>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="4"></v-col>
              <v-col cols="12" md="6"  class="py-0" >
                <h2 class="red--text" v-show="!validated">NO PUEDE SER GARANTE</h2>
                <h2 class="success--text" v-show="validated1"> PUEDE SER GARANTE</h2>
              </v-col>
               <v-col cols="12" md="12" class="py-0" v-show="affiliate_garantor.affiliate.cpop">
                <h5 class="success--text text-center">AFILIADO CPOP {{affiliate_garantor.affiliate.cpop}}</h5>
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
                      >
                      </v-data-table>
                    </v-col>
                     <v-col cols="12" md="6" class="font-weight-black" >
       PRESTAMOS QUE ESTA GARANTISANDO:      
              </v-col>
                <v-col cols="12" md="2" class="font-weight-black" >
       {{affiliate_garantor.active_guarantees_quantity}}       
              </v-col>
                     <v-col cols="12" class="py-0" >
                      <v-data-table
                        :headers="headers1"
                        :items="guarantor"
                        :items-per-page="4"
                      >
                      </v-data-table>
                    </v-col>    
            </v-row>
          </v-container>
        </v-card>
<br>
               </v-col>
         <v-col cols="12" class="py-0" >
           <v-card>
          <v-container >
            <v-row>
              <v-col cols="12" md="3">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                      <v-text-field
                       class="py-0"
                        label="Boleta de Pago"
                      ></v-text-field>
                       <v-text-field
                       class="py-0"
                        label="Bono Frontera"
                      ></v-text-field>
                       <v-text-field
                       class="py-0"
                        label="Bono Oriente"
                      ></v-text-field>
                       <v-text-field
                       class="py-0"
                        label="Bono Cargo"
                      ></v-text-field>
                      <v-text-field
                      class="py-0"
                        label="Bono Seguridad"
                      ></v-text-field>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <p>PROMEDIO LIQUIDO PAGABLE:</p>
                            <p>TOTAL BONOS:</p>
                            <p>LIQUIDO PARA CALIFICACION: </p>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <p>CALCULO DE CUOTA: </p>
                            <p>INDICE DE ENDEUDAMIENTO:</p>
                            <p>MONTO MAXIMO SUGERIDO : </p>
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
  </v-container>
</template>
<script>
  export default {
  name: "loan-information",
  data: () => ({
    guarantor_ci:null,
    affiliate_garantor:{
      affiliate:{
        category:{},
        affiliate_state:{}
      },
      
    },
    loan:[],
    guarantor:[],
    validated:true,
    validated1:false,
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
    headers1: [
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
    modalities: [
      { name:"Anticipo",
        value:"1"
      },
      { name:"Corto Plazo",
        value:"2"
      },
      { name:"Largo Plazo",
        value:"3"
      },
      { name:"Hipotecario",
        value:"4"
      }
    ],
  }),
 
  computed: {
 
  
 },
  beforeMount() {
  },
  mounted() {
  },
  methods: {
   async activar()
    {

      try {
          let resp = await axios.patch(`affiliate_guarantor`,{
          
             identity_card: this.guarantor_ci,
              procedure_modality_id: 32,
          
        })
        this.affiliate_garantor=resp.data
        this.validated=this.affiliate_garantor.guarantor
        this.validated1=this.affiliate_garantor.guarantor
        this.loan=this.affiliate_garantor.affiliate.loans
        this.guarantor=this.affiliate_garantor.affiliate.guarantor
      console.log('Entro al metodo de garanyes'+this.affiliate_garantor+'==>'+this.guarantor_ci) 
        console.log('prestamos'+this.loan)  
        console.log('guarantor'+this.guarantor)       
          
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    
    }
  }
  }
</script>