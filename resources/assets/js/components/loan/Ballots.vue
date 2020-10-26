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
                            v-model="loanTypeSelected.id"
                            :Onchange="Onchange()"
                            :items="modalities"
                            item-text="name"
                            item-value="id"
                            required
                            :disabled="edit_refi_repro"
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
                            v-model="loan_detail.net_realizable_value"
                            editable
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-col>
                </v-row>
              </v-container>
              <v-container class="py-0" >
                <v-row>
                  <v-col cols="12" md="12">
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
                    BONOS {{data_sismu.type_sismu}}
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
                  <template v-if="type_sismu">             
                  <v-col cols="12" class="py-0">
                    DATOS SISMU
                  </v-col>
                  <v-col cols="12" md="3" >
                    <v-text-field
                      dense
                      v-model="data_sismu.quota_sismu"
                      label="Cuota"          
                     ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3" v-if="this.loanTypeSelected.id==11 || this.loanTypeSelected.id==12">
                      <v-checkbox
                        v-model="data_sismu.cpop_sismu"
                        label="Afiliado CPOP"                   
                      ></v-checkbox>
                  </v-col>       
                  </template>
                </v-row>
              </v-container>
              <BallotsHipotecary
                v-show="hipotecario"
                :contrib_codebtor="contrib_codebtor"
                :modalidad.sync="modalidad"
                :affiliate.sync="affiliate"/>
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
    //loanTypeSelected:null,
    visible:false,
    hipotecario:false,
    window_size:4,
    see_field:false
  
  }),
   props: {
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
    intervalos: {
      type: Object,
      required: true
    },
    contrib_codebtor: {
      type: Array,
      required:true
    },
    loan_detail: {
      type: Object,
      required: true
    },
      affiliate: {
      type: Object,
      required: true
    },    
    data_loan: {
      type: Object,
      required: true
    },
    edit_refi_repro: {
      type: Boolean,
      required: true
    },
    loanTypeSelected:{
      type: Object,
      required: true,
      
    },
    data_sismu:{
      type: Object,
      required: true
    }
  },
    components: {
    BallotsHipotecary
  },
  mounted() {
    this.getLoanIntervals()
  },
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    },
    refinancing() {
      return this.$route.params.hash == 'refinancing'
    },
    reprogramming() {
      return this.$route.params.hash == 'reprogramming'
    },
    type_sismu() {
      if(this.$route.query.type_sismu){
        this.data_sismu.type_sismu = true
      }
      return this.data_sismu.type_sismu
    }
  },
  /*watch: {
    loanTypeSelected(newVal, oldVal){
    if(newVal!=oldVal){
      this.Onchange()
    }
  }
},*/
  methods:
 {//muestra los intervalos de acuerdo a una modalidad
    Onchange(){
      for (let i = 0; i< this.interval.length; i++) {
        if(this.loanTypeSelected.id==this.interval[i].procedure_type_id){
          if(this.loanTypeSelected.id==12){
            this.hipotecario=true
            this.window_size=3
            this.see_field=true
          }else{
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
          this.intervalos.procedure_type_id= this.loanTypeSelected.id
          //debugger
          this.getLoanModality(this.$route.query.affiliate_id)
          //this.getBallots(this.$route.query.affiliate_id)
        }else{
        console.log('NO ES IGUAL A MODALIDAD INTERVALS'+this.interval[i].procedure_type_id +"=="+this.loanTypeSelected.id )
      }
      }
 
   
    },
    clearForm() //FIXME ver si la funcion sera necesaria
    {
      this.payable_liquid[0]=0
      this.payable_liquid[1]=0
      this.payable_liquid[2]=0
      this.bonos[0]=0
      this.bonos[1]=0
      this.bonos[2]=0
      this.bonos[3]=0
    },
    //Obtiene los parametros de la modalidad
    async getLoanModality(id) {
      try {
        let resp = await axios.get(`affiliate/${id}/loan_modality`,{
          params: {
            procedure_type_id:this.loanTypeSelected.id,
            type_sismu: this.data_sismu.type_sismu,
            cpop_sismu: this.data_sismu.cpop_sismu
            //external_discount:0, //FIXME revisar si este paramtro no tiene uso, en otro caso borrar
          }
        })
        
          let loan_modality = resp.data
          this.modalidad.id = loan_modality.id
          this.modalidad.procedure_type_id = loan_modality.procedure_type_id
          this.modalidad.name = loan_modality.name
          this.modalidad.quantity_ballots = loan_modality.loan_modality_parameter.quantity_ballots
          this.modalidad.guarantors = loan_modality.loan_modality_parameter.guarantors
          this.modalidad.min_guarantor_category = loan_modality.loan_modality_parameter.min_guarantor_category
          this.modalidad.max_guarantor_category = loan_modality.loan_modality_parameter.max_guarantor_category
          this.modalidad.personal_reference = loan_modality.loan_modality_parameter.personal_reference
          this.modalidad.max_cosigner = loan_modality.loan_modality_parameter.max_cosigner
            //debugger
       
          console.log("MODALIDAD")
          console.log(this.modalidad)
    
          this.loan_detail.min_guarantor_category= loan_modality.loan_modality_parameter.min_guarantor_category
          this.loan_detail.max_guarantor_category= loan_modality.loan_modality_parameter.max_guarantor_category
          if(loan_modality.loan_modality_parameter.quantity_ballots>1){
          this.visible = true
          //debugger
          }else{
          this.visible = false
        }
           this.getBallots(id)
        
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
    try {
      let data_ballots=[]
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          per_page: this.modalidad.quantity_ballots,
          page: 1,
        }
      })
      
      console.log("-------")
      console.log(this.modalidad.quantity_ballots)
      data_ballots = res.data.data  
      console.log(data_ballots)
    
      if(res.data.valid){
        this.editar=false
         //Carga los datos en los campos para ser visualizados en la interfaz    
        for (let i = 0; i < data_ballots.length; i++) {//colocar 1
          this.payable_liquid[i] = data_ballots[i].payable_liquid
          if(i==0){//solo se llena los bonos de la ultima boleta de pago
            this.bonos[0] = data_ballots[0].border_bonus
            this.bonos[1] = data_ballots[0].east_bonus
            this.bonos[2] = data_ballots[0].seniority_bonus
            this.bonos[3] = data_ballots[0].public_security_bonus
          }
        }
      } else{
          console.log("No se tienen boletas del ultimo mes")
          //this.clearForm()//TODO ver si es necesario, ya que sin la funcion igual se carga los datos declarados por defecto de las variables
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
 }
};
</script>