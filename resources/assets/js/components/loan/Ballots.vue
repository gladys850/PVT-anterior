<template>
  <v-flex xs12 class="px-3">
    <v-row justify="center">
      <v-col cols="12"  >
         <v-card>
          <v-container fluid >
    <v-row justify="center" class="py-0">
      <v-col cols="12" class="py-0" >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="4" class="py-0 text-center">
              MODALIDAD DEL PRESTAMO
            </v-col>
            <v-col cols="12" md="4" class="py-0 text-center">
              INTERVALO DE LOS MONTOS
            </v-col>
            <v-col cols="12" md="4" class="py-0 text-center">
              INTERVALO DEL PLAZO EN MESES
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              <v-select
                dense
                v-validate="'required'"
                v-model="loanTypeSelected"
                data-vv-name="modalities"
                :onchange="Onchange()"
                :items="modalities"
                item-text="name"
                item-value="id"
              ></v-select>
            </v-col>
            <v-col cols="12" md="4" class="py-0 text-center">
              {{monto}}
            </v-col>
            <v-col cols="12" md="4" class="py-0 text-center" >
              {{plazo}}
            </v-col>
          </v-row>
        </v-container>
      </v-col>
    </v-row>
  </v-container>
          <v-container class="py-0">
            <v-row>
              <v-col cols="12" md="12" class="text-center" >
                BOLETAS DE PAGO
              </v-col>
              <v-col cols="12" md="4" class="py-0"  >
                <v-text-field
                  dense
                  v-model="payable_liquid[0]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1raBoleta')"
                  data-vv-name="1raBoleta"
                  label="1ra Boleta"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible">
                <v-text-field
                  dense
                  v-model="payable_liquid[1]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2daBoleta')"
                  data-vv-name="2daBoleta"
                  label="2ra Boleta"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4" class="py-0" v-if="visible" >
                <v-text-field
                  dense
                  v-model="payable_liquid[2]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3raBoleta')"
                  data-vv-name="3raBoleta"
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
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('1erBono')"
                  data-vv-name="1erBono"
                  label="Bono Frontera"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  dense
                  v-model="bonos[1]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('2doBono')"
                  data-vv-name="2doBono"
                  label="Bono Oriente"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3" >
                <v-text-field
                  dense
                  v-model="bonos[2]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('3erBono')"
                  data-vv-name="3erBono"
                  label="Bono Cargo"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  dense
                  v-model="bonos[3]"
                  v-validate.initial="'min:1|max:20'"
                  :error-messages="errors.collect('4toBono')"
                  data-vv-name="4toBono"
                  label="Bono Seguridad Ciudadana"
                  :readonly="!editar"
                  :outlined="editar"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </v-flex>
</template>
<script>
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "dashboard-index",
  data: () => ({
    editar:true,
    monto:null,
    plazo:null,
    interval:[],
    loanTypeSelected:null,
    visible:false,
    num_type:9,
  }),
   props: {
    contributions1: {
      type: Array,
      required: true
    },
    modality: {
      type: Object,
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
    datos: {
      type: Array,
      required: true
    },
    intervalos: {
      type: Object,
      required: true
    },
  },
   beforeMount() {
    this.getLoanIntervals()
  },
  mounted(){
    this.getBallots(this.$route.query.affiliate_id);
    this.getLoanModality(this.$route.query.affiliate_id)
  },
  methods:
 {
    Onchange(){
      for (this.i = 0; this.i< this.interval.length; this.i++) {
        if(this.loanTypeSelected==this.interval[this.i].procedure_type_id)
        {
          this.monto= this.interval[this.i].minimum_amount+' - '+this.interval[this.i].maximum_amount,
          this.plazo= this.interval[this.i].minimum_term+' - '+this.interval[this.i].maximum_term
          //intervalos es el monto, plazo y modalidad y id de una modalidad
          this.intervalos.maximun_amoun=this.interval[this.i].maximum_amount
          this.intervalos.maximum_term= this.interval[this.i].maximum_term
          this.intervalos.procedure_type_id= this.loanTypeSelected
          this.num_type=this.loanTypeSelected
        }
      }

      this.getLoanModality(this.$route.query.affiliate_id)
      this.getBallots(this.$route.query.affiliate_id)
    },
    async getLoanModality(id) {
      try {
        let resp = await axios.get(`affiliate/${id}/loan_modality`,{
        params: {
          procedure_type_id:this.num_type,
          external_discount:0,
          }
      })
      this.loan_modality= resp.data
      this.modalidad.id=this.loan_modality.id
      this.modalidad.name=this.loan_modality.name
      this.modalidad.quantity_ballots=this.loan_modality.loan_modality_parameter.quantity_ballots
      if(this.loan_modality.loan_modality_parameter.quantity_ballots>1)
      {
         this.visible=true
      }else{
         this.visible=false
      }

     } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
    async getLoanIntervals() {
      try {
        let res = await axios.get(`loan_interval`)
        this.interval = res.data
       } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getBallots(id) {
    try {
      console.log('entro a getballots')
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          per_page: this.modality.loan_modality_parameter.quantity_ballots,
          page: 1,
        }
      })
      if(res.data.valid)
      {
        this.editar=false
       this.datos=res.data.data
        for (this.i = 0; this.i< this.datos.length; this.i++) {
          this.payable_liquid[this.i]= this.datos[this.i].payable_liquid,
          this.bonos[0]= this.datos[0].border_bonus,
          this.bonos[1]= this.datos[0].east_bonus,
          this.bonos[2]= this.datos[0].seniority_bonus,
          this.bonos[3]= this.datos[0].public_security_bonus
        }
   
         for(this.j = 0; this.j< this.datos.length; this.j++)
        {
           this.contributions1[this.j].payable_liquid=this.datos[this.j].payable_liquid
           if(this.j==0){
                      this.contributions1[this.j].border_bonus= this.datos[this.j].border_bonus,
                      this.contributions1[this.j].east_bonus= this.datos[this.j].east_bonus,
                      this.contributions1[this.j].seniority_bonus= this.datos[this.j].seniority_bonus,
                      this.contributions1[this.j].public_security_bonus= this.datos[this.j].public_security_bonus
                    }
                    else{
                      this.contributions1[this.j].border_bonus=0,
                      this.contributions1[this.j].east_bonus= 0,
                      this.contributions1[this.j].seniority_bonus=0,
                      this.contributions1[this.j].public_security_bonus=0
                    }
        } 
        console.log(this.contributions1+'las contribuciones')
        
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  }
 }
};
</script>