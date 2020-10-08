<template>
  <v-flex xs12 class="px-3">
      <v-form>
        <v-row justify="center">
          <v-col cols="12"  >
            <v-card>
              <v-container fluid >
                <v-row justify="center" class="py-0">
                  <v-col cols="12" class="py-0" >
                    <v-container class="py-0">
                      <v-row>
                        <v-col cols="12" :md="window_size" class="py-0 text-center">
                          MODALIDAD DEL PRÉSTAMO
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 text-center">
                          INTERVALO DE LOS MONTOS
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 text-center">
                          INTERVALO DEL PLAZO EN MESES
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 text-center" v-if="see_field">
                          VALOR NETO REALIZADO (VNR)
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0">
                          <v-select
                            dense
                            v-model="loanTypeSelected"
                            :onchange="Onchange()"
                            :items="modalities"
                            item-text="name"
                            item-value="id"
                            required
                          ></v-select>
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 text-center">
                          {{monto}}
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 text-center" >
                          {{plazo}}
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0" v-if="see_field">
                          <v-text-field
                            dense
                            v-model="net_realizable_value"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-col>
                </v-row>
              </v-container>
              <v-container class="py-0" >
                <v-row>
                  <v-col cols="12" md="12" class="text-center" >
                    BOLETAS DE PAGO
                  </v-col>

                  <v-col cols="12" md="4" class="py-0"  >
                    <v-text-field
                      dense
                      v-model="payable_liquid[0]"
                      label="1ra Boleta"
                      :readonly="!editar"
                      :outlined="editar"
                     ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="py-0" v-if="visible">
                    <v-text-field
                      dense
                      v-model="payable_liquid[1]"
                      label="2ra Boleta"
                      :readonly="!editar"
                      :outlined="editar"
                  ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4" class="py-0" v-if="visible" >
                    <v-text-field
                      dense
                      v-model="payable_liquid[2]"
                      label="3ra Boleta"
                      :readonly="!editar"
                      :outlined="editar"
                     ></v-text-field>
                  </v-col>
                  <v-col cols="12" class="py-0" >
                    BONOS
                  </v-col>
                  <v-col cols="12" md="3" >
                    <v-text-field
                      dense
                      v-model="bonos[0]"
                      label="Bono Frontera"
                      :readonly="!editar"
                      :outlined="editar"
                     ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      dense
                      v-model="bonos[1]"
                      label="Bono Oriente"
                      :readonly="!editar"
                      :outlined="editar"
                     ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3" >
                    <v-text-field
                      dense
                      v-model="bonos[2]"
                      label="Bono Cargo"
                      :readonly="!editar"
                      :outlined="editar"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      dense
                      v-model="bonos[3]"
                      label="Bono Seguridad Ciudadana"
                      :readonly="!editar"
                      :outlined="editar"
                     ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
              <BallotsHipotecary
                v-show="hipotecario"
                :contributions1.sync="contributions1"
                :contrib_codebtor="contrib_codebtor"/>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
  </v-flex>
</template>
<script>

import BallotsHipotecary from '@/components/loan/BallotsHipotecary'
export default { 
  name: "ballots",
  data: () => ({
    editar:true,
    monto:null,
    plazo:null,
    interval:[],
    loanTypeSelected:null,
    visible:false,
    num_type:9,
    hipotecario:false,
    //contrib_codebtor: [],
    //contributions1_aux: [],
    //datos:[],//TODO se declaro como variable local data_ballots, asi que se debe borrar 
    window_size:4,
    see_field:false,
    net_realizable_value: 0
  
  }),
   props: {
    procedure_type: {
      type: Number,
      required: true
    },
    contributions1: {
      type: Array,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
    bonos: {
      type: Array,
      required: true
    },
    payable_liquid: {
      type: Array,
      required: true
    },
    modalities: {
      type: Array,
      required: true
    },
     prueba: {
      type: Array,
      required: true
    },
    intervalos: {
      type: Object,
      required: true
    },
    contrib_codebtor: {
      type: Array,
      required:true
    },

  },
    components: {
    BallotsHipotecary
  },
   beforeMount() {
    this.getLoanIntervals()
  },
    mounted(){
    this.getLoanModality(this.$route.query.affiliate_id)
  },
  methods:
 {//caldula los intervalos deacuerdo a una modalidad
    Onchange(){
      let i
      for (i = 0; i< this.interval.length; i++) {
        if(this.loanTypeSelected==this.interval[i].procedure_type_id)
        {
          if(this.loanTypeSelected==12)
          {
            this.hipotecario=true
            this.window_size=3
            this.see_field=true
          }
          else{
            this.hipotecario=false
            this.window_size=4
            this.see_field=false
          }
          this.monto= this.interval[i].minimum_amount+' - '+this.interval[i].maximum_amount,
          this.plazo= this.interval[i].minimum_term+' - '+this.interval[i].maximum_term
          //intervalos es el monto, plazo y modalidad y id de una modalidad
          this.intervalos.maximun_amoun=this.interval[i].maximum_amount
          this.intervalos.maximum_term= this.interval[i].maximum_term
          this.intervalos.minimun_amoun=this.interval[i].minimum_amount
          this.intervalos.minimum_term= this.interval[i].minimum_term
          this.intervalos.procedure_type_id= this.loanTypeSelected
          this.num_type=this.loanTypeSelected
          this.procedure_type=this.loanTypeSelected
          
          this.getLoanModality(this.$route.query.affiliate_id)
          this.getBallots(this.$route.query.affiliate_id)
          console.log('este es la modalidad del intervalo'+this.num_type)

        }
      }
 
   
    },
    /*clearForm() //FIXME ver si la funcion sera necesaria
    {
      this.payable_liquid[0]=0
      this.payable_liquid[1]=0
      this.payable_liquid[2]=0
      this.bonos[0]=0
      this.bonos[1]=0
      this.bonos[2]=0
      this.bonos[3]=0
    },*/
    /*Medodo donde identifica la modalidad de acuerdo a las caracteristicas de un affiliado
      "id": 33,
      "procedure_type_id": 9,
      "name": "Anticipo sector pasivo",
      "shortened": "ANT-SP",
      "is_valid": true,
      "loan_modality_parameter": {
        "procedure_modality_id": 33,
        "debt_index": "90",
        "quantity_ballots": 1,
        "guarantors": 0,
        "min_guarantor_category": null,
        "max_guarantor_category": null,
        "personal_reference": false,
        "max_lenders": 1
    }*/
    async getLoanModality(id) {
      try {
        let resp = await axios.get(`affiliate/${id}/loan_modality`,{
          params: {
            procedure_type_id:this.num_type,
            external_discount:0,
          }
        })
        console.log('entro a get modality'+this.num_type)
          this.loan_modality= resp.data
          this.modalidad.id=this.loan_modality.id
          this.modalidad.procedure_type_id=this.loan_modality.procedure_type_id
          this.modalidad.name=this.loan_modality.name
          this.modalidad.quantity_ballots=this.loan_modality.loan_modality_parameter.quantity_ballots
          this.modalidad.guarantors=this.loan_modality.loan_modality_parameter.guarantors
          this.modalidad.min_guarantor_category=this.loan_modality.loan_modality_parameter.min_guarantor_category
          this.modalidad.max_guarantor_category=this.loan_modality.loan_modality_parameter.max_guarantor_category
         this.modalidad.personal_reference=this.loan_modality.loan_modality_parameter.personal_reference
         this.modalidad.max_cosigner=this.loan_modality.loan_modality_parameter.max_cosigner
         this.modalidad.net_realizable_value=this.net_realizable_value
         
         
    
    
    //this.modalidad.personal_reference=true 
        this.prueba[0]=this.loan_modality.loan_modality_parameter.guarantors //FIXME prueba, en este componente se recuperan algunos datos, verificar si se usa en otro componente
        this.prueba[1]=this.loan_modality.loan_modality_parameter.min_guarantor_category
        this.prueba[2]=this.loan_modality.loan_modality_parameter.max_guarantor_category
        this.prueba[3]=this.loan_modality.loan_modality_parameter.personal_reference
          if(this.loan_modality.loan_modality_parameter.quantity_ballots>1)
          {
          this.visible=true
          }
          else{
          this.visible=false
        }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Intervalos de Plazo y Meses de una modalidad
    async getLoanIntervals() {
      try {
        let res = await axios.get(`loan_interval`)
        this.interval = res.data
       }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }
    },
    //Metodo para sacar boleta de un afiliado
  async getBallots(id) {
    let data_ballots=[]
    let i, j
    try {
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          per_page: this.modalidad.quantity_ballots,
          page: 1,
        }
      })
      data_ballots=res.data.data  
      if(res.data.valid)
      {
        this.editar=false
             
        for (i = 0; i< 1; i++) {
          this.payable_liquid[i] = data_ballots[i].payable_liquid,
          this.bonos[0] = data_ballots[0].border_bonus,
          this.bonos[1] = data_ballots[0].east_bonus,
          this.bonos[2] = data_ballots[0].seniority_bonus,
          this.bonos[3] = data_ballots[0].public_security_bonus
        }
        for(j = 0; j< data_ballots.length; j++){
          this.contributions1[j].id_affiliate = data_ballots[j].affiliate_id
          this.contributions1[j].payable_liquid = data_ballots[j].payable_liquid
          
          if(j == 0){
            this.contributions1[j].border_bonus = data_ballots[j].border_bonus,
            this.contributions1[j].east_bonus = data_ballots[j].east_bonus,
            this.contributions1[j].seniority_bonus = data_ballots[j].seniority_bonus,
            this.contributions1[j].public_security_bonus = data_ballots[j].public_security_bonus
          }
          else{
            this.contributions1[j].border_bonus=0,
            this.contributions1[j].east_bonus= 0,
            this.contributions1[j].seniority_bonus=0,
            this.contributions1[j].public_security_bonus=0
          }
        }
      } else{
          this.contributions1[0].id_affiliate =  data_ballots[0].affiliate_id
          this.contributions1[0].payable_liquid = this.payable_liquid[0]
    
          this.contributions1[0].border_bonus = this.bonos[0]
          this.contributions1[0].east_bonus =this.bonos[1]
          this.contributions1[0].seniority_bonus = this.bonos[2]
          this.contributions1[0].public_security_bonus =this.bonos[3]
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },


    /*formatear() {    
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
    async liquidCalificated(){
      this.formatear()  
      let resultado
      try {
            let res = await axios.post(`liquid_calificated`,{liquid_calification:this.contributions1_aux})
            this.resultado=res.data
            console.log("RESULTADO")
            console.log(resultado)
          
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
        console.log('entro por verdadero')
      }
    },
*/

 }
};
</script>