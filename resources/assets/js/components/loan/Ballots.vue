<template>
  <v-flex xs12 class="px-3">
      <v-form>
        <v-row justify="center">
          <v-col cols="12"  >
            <v-card>
              <ValidationObserver ref="observer" >
              <v-container fluid >
                <v-row justify="center" class="py-0 my-0">
                  <v-col cols="12" class="py-0 -my-0" >
                    <v-container class="py-0 my-0">
                      <v-row>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center">
                          MODALIDAD DEL PRÉSTAMO <!--{{loanTypeSelected.id}}-->
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center">
                          INTERVALO DE LOS MONTOS
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center">
                          INTERVALO DEL PLAZO EN MESES 
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center" v-if="see_field">
                          VALOR NETO REALIZADO (VNR)
                        </v-col>
                        <!--{{contribution}}-->
                        <v-col cols="12" :md="window_size" class="py-0 my-0">
                          <v-select
                            dense
                            v-model="loanTypeSelected.id"
                            @change="Onchange()"
                            :items="modalities"
                            item-text="name"
                            item-value="id"
                            required
                            :disabled="edit_refi_repro"
                          ></v-select>
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center">
                          {{monto}}
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0 text-center" >
                          {{plazo}}
                        </v-col>
                        <v-col cols="12" :md="window_size" class="py-0 my-0" v-if="see_field">
                          <ValidationProvider v-slot="{ errors }" name="VNR" :rules="'required|min_value:'+intervalos.minimun_amoun"  mode="aggressive">
                          <v-text-field
                            :error-messages="errors"
                            dense
                            v-model="loan_detail.net_realizable_value"
                            label="VNR"
                            outlined
                            editable
                          ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-col>
                </v-row>
              </v-container>
              <v-container cols="12" md="12" class="py-0 my-0">
                <v-row class="py-0 my-0">
                  <!--<v-col cols="12" md="12" style="margin-top: -20px">
                    <v-checkbox
                      v-model="enabled"
                      hide-details
                      label="Habilitar Edicion"
                    ></v-checkbox>
                  </v-col>  -->                
                  <v-col cols="12" md="2" class="py-0 my-0">
                    <v-text-field
                      dense
                      v-model="number_diff_month"
                      label="Número de meses"                    
                      color="info"
                      append-icon="mdi-plus-box"
                      prepend-icon="mdi-minus-box"
                      @click:append="appendIconCallback"
                      @click:prepend="prependIconCallback"
                      readonly
                    ></v-text-field>
                  </v-col>           
                </v-row>
                <!--boleta 1--->

                <v-row v-for="(contrib,i) in contribution" :key="i" class="py-0 my-0">
               
                  <v-col cols="12" md="7" class="py-0 my-0">
                    <v-row>
                      <v-col cols="12" md="12" class="py-0 my-0 uppercase"> BOLETAS DE PAGO {{contribution[i].period}} <b>{{contribution[i].month}}</b></v-col>
                      <v-col cols="12" md="3" class="py-0 my-0" v-if="lender_contribution.state_affiliate != 'Comisión'">
                        <ValidationProvider
                          v-slot="{ errors }"
                          name="Boleta de pago"
                          :rules="'required|min_value:' + livelihood_amount"
                          mode="aggressive"
                        >
                          <b style="text-align: center"></b>
                          <v-text-field
                            :error-messages="errors"
                            dense
                            v-model="contribution[i].payable_liquid"
                            label="Liquido pagable"
                            :disabled="!enabled"
                            :outlined="editar"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <!--<v-col cols="12" md="1" class="py-0">
                        <span>
                          <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                icon
                                dark
                                small
                                color="success"
                                bottom
                                right
                                v-on="on"
                                @click="saveAdjustment(i)"
                              >
                                <v-icon>mdi-calculator</v-icon>
                              </v-btn>
                            </template>
                            <span>Ajuste</span>
                          </v-tooltip>
                        </span>                        
                      </v-col>-->
                      <v-col cols="12" class="py-0 my-0"  :md="lender_contribution.state_affiliate == 'Comisión' ? 4 : 2">
                        <ValidationProvider
                          v-slot="{ errors }"
                          name="Monto ajuste"
                          :rules="''"
                          mode="aggressive"
                        >
                          <b style="text-align: center"></b>
                          <v-text-field
                            :error-messages="errors"
                            dense
                            v-model="contribution[i].adjustment_amount"
                            :label= "lender_contribution.state_affiliate == 'Comisión' ? 'Liquido pagable' :  'Monto ajuste'"
                            outlined
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                      <template v-if="lender_contribution.state_affiliate != 'Comisión'">
                        <v-col cols="12" md="2" class="py-0 my-0" >
                          <b style="text-align: center">= {{(parseFloat(contribution[i].adjustment_amount) + parseFloat(contribution[i].payable_liquid)).toFixed(2)}}</b>
                        </v-col>
                        <v-col cols="12" md="5" class="py-0 my-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Descripción"
                            :rules="''"
                            mode="aggressive"
                          >
                            <b style="text-align: center"></b>
                            <v-textarea
                              :error-messages="errors"
                              dense
                              v-model="contribution[i].adjustment_description"
                              label="Descripción ajuste"
                              outlined
                              rows="1"                              
                            ></v-textarea>
                          </ValidationProvider>
                        </v-col>
                      </template>
                    </v-row>
                  </v-col>

                  <v-col cols="12" md="5" class="py-0 my-0">
                    <v-row class="py-0 my-0">
                      <v-col cols="12" md="12" class="py-0 my-0" v-if="lender_contribution.state_affiliate != 'Comisión'"> BONOS </v-col>
                      <template v-if="lender_contribution.state_affiliate == 'Activo'">
                        <v-col cols="12" md="3" class="py-0 my-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Bono Frontera"
                            :rules="''"
                            mode="aggressive"
                          >
                            <v-text-field
                              :error-messages="errors"
                              dense
                              v-model="contribution[i].border_bonus"
                              label="Bono Frontera"
                              :disabled="!enabled"
                              :outlined="editar"
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" md="3" class="py-0 my-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Bono Oriente"
                             :rules="''"
                            mode="aggressive"
                          >
                            <v-text-field
                              :error-messages="errors"
                              dense
                              v-model="contribution[i].east_bonus"
                              label="Bono Oriente"
                              :disabled="!enabled"
                              :outlined="editar"
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" md="3" class="py-0 my-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Bono Cargo"
                            :rules="''"
                            mode="aggressive"
                          >
                            <v-text-field
                              :error-messages="errors"
                              dense
                              v-model="contribution[i].position_bonus"
                              label="Bono Cargo"
                              :disabled="!enabled"
                              :outlined="editar"
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                        <v-col cols="12" md="3" class="py-0 my-0">
                          <ValidationProvider
                            v-slot="{ errors }"
                            name="Bono Seguridad Ciudadana"
                             :rules="''"
                            mode="aggressive"
                          >
                            <v-text-field
                              :error-messages="errors"
                              dense
                              v-model="contribution[i].public_security_bonus"
                              label="Bono Seguridad Ciudadana"
                              :disabled="!enabled"
                              :outlined="editar"
                            ></v-text-field>
                          </ValidationProvider>
                        </v-col>
                     </template> 
                      <v-col cols="12" :md="lender_contribution.state_affiliate == 'Pasivo' ? 4 : 3" class="py-0 my-0" v-if="lender_contribution.state_affiliate == 'Pasivo'">
                        <ValidationProvider
                          v-slot="{ errors }"
                          name="Renta dignidad"
                          :rules="''"
                          mode="aggressive"
                        >
                          <v-text-field
                            :error-messages="errors"
                            dense
                            v-model="contribution[i].dignity_rent"
                            label="Renta dignidad"
                            :disabled="!enabled"
                            :outlined="editar"
                          ></v-text-field>
                        </ValidationProvider>
                      </v-col>
                    </v-row>
                  </v-col>
        
                </v-row>

                <template v-if="type_sismu">
                  <v-col cols="12" class="py-0 my-0"> DATOS SISMU </v-col>
                  <v-col cols="12" md="3" class="py0 my-0">
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="cuota"
                      :rules="'min_value:1'"
                      mode="aggressive"
                    >
                      <v-text-field
                        :error-messages="errors"
                        dense
                        v-model="data_sismu.quota_sismu"
                        outlined
                        label="Cuota"
                      ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col
                    cols="12"
                    md="3"
                    class="py-0 my-0"
                    v-if="
                      this.loanTypeSelected.id == 11 ||
                      this.loanTypeSelected.id == 12
                    "
                  >
                    <v-checkbox
                      v-model="data_sismu.cpop_sismu"
                      label="Afiliado CPOP"
                    ></v-checkbox>
                  </v-col>
                </template>
              </v-container>
              <!--<Adjustment :bus="bus"/>-->
              <BallotsHipotecary
                v-show="hipotecario"
                :contrib_codebtor="contrib_codebtor"
                :modalidad.sync="modalidad"
                :affiliate.sync="affiliate"
                :data_loan.sync="data_loan"
                :livelihood_amount="livelihood_amount"/>
             </ValidationObserver>
            </v-card>
          </v-col>
        </v-row>
       
      </v-form>
  </v-flex>
</template>
<script>
import BallotsHipotecary from '@/components/loan/BallotsHipotecary'
//import Adjustment from "@/components/loan/Adjustment"
export default {
  name: "ballots",
  data: () => ({
    bus: new Vue(),
    enabled: false,
    editar:true,
    monto:null,
    plazo:null,
    interval:[],
    //loanTypeSelected:null,
    visible:false,
    hipotecario:false,
    window_size:4,
    see_field:false,
    loan_modality: {},
    data_ballots: [],
    contribution: [],
     /*contribution: [{},{},{}
     {
        payable_liquid: null,
        adjustment: null,
        position_bonus: null,
        border_bonus: 0,
        public_security_bonus: null,
        east_bonus: null,
        dignity_rent: null,
        period: null
      }, {
        payable_liquid: null,
        adjustment: null,
        position_bonus: null,
        border_bonus: 0,
        public_security_bonus: null,
        east_bonus: null,
        dignity_rent: null,
        period: null
      },{
        payable_liquid: null,
        adjustment: null,
        position_bonus: null,
        border_bonus: 0,
        public_security_bonus: null,
        east_bonus: null,
        dignity_rent: null,
        period: null
      }],
    affiliate_contribution: [],
    contribution_passive: {},*/
    choose_diff_month: false,
    number_diff_month: 1,
    lender_contribution: {}

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
    period: {
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
    },
    livelihood_amount:{
      type: Number,
      required:true,
      default:0
    },
    affiliate_contribution:{
      type: Object,
      required: true
    },
    contribution_passive:{
      type: Object,
      required: true
    }
  },
    components: {
    BallotsHipotecary,
    //Adjustment
  },
  mounted() {
    this.getLoanIntervals()
  },
  watch: {
    'loanTypeSelected.id': function(newVal, oldVal){
      if(newVal!= oldVal)
        this.Onchange()
        //alert ('ballot' + this.loanTypeSelected.id)
    }
  },
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    },
    refinancing() {
      return this.$route.params.hash == 'refinancing'
    },
    reprogramming() {
      if(this.$route.params.hash == 'reprogramming'){
        return true
      }else{
        return false
      }
    },
    remake() {
      if(this.$route.params.hash == 'remake'){
        return true
      }else{
        return false
      }
    },
    type_sismu() {
      if(this.$route.query.type_sismu){
        return true
      }
      return false
    }
  },
  methods:
 {
    //Intervalos de Plazo y Meses de una modalidad
    async getLoanIntervals() {
      try {
        let res = await axios.get(`loan_interval`)
        this.interval = res.data
        console.log(this.interval)
        if(this.reprogramming){
          this.Onchange()
        }
       }catch (e) {
        console.log(e)
      }
    },
   //muestra los intervalos de acuerdo a una modalidad
    async Onchange(){
      this.choose_diff_month = false
      this.number_diff_month = 1
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
          this.getLoanModality(this.$route.query.affiliate_id)
        } /*else{
        console.log('NO ES IGUAL A MODALIDAD INTERVALS'+this.interval[i].procedure_type_id +"=="+this.loanTypeSelected.id )
        }*/
      }
    },

    //Obtiene los parametros de la modalidad
    async getLoanModality(id) {
      try {
        let resp = await axios.post(`affiliate/${id}/loan_modality?procedure_type_id=${this.loanTypeSelected.id}`,{
          type_sismu: this.data_sismu.type_sismu,
          cpop_sismu: this.data_sismu.cpop_sismu,
          reprogramming: this.reprogramming || this.remake
        })
        if(resp.data ==''){
          this.loan_detail.not_exist_modality = true
          this.toastr.error("El afiliado no puede ser evaluado en esta modalidad")
        }else{
          this.loan_modality = resp.data
          this.modalidad.id = this.loan_modality.id
          this.modalidad.procedure_type_id = this.loan_modality.procedure_type_id
          this.modalidad.name = this.loan_modality.name
          this.modalidad.quantity_ballots = this.loan_modality.loan_modality_parameter.quantity_ballots
          this.modalidad.guarantors = this.loan_modality.loan_modality_parameter.guarantors
          this.modalidad.min_guarantor_category = this.loan_modality.loan_modality_parameter.min_guarantor_category
          this.modalidad.max_guarantor_category = this.loan_modality.loan_modality_parameter.max_guarantor_category
          this.modalidad.personal_reference = this.loan_modality.loan_modality_parameter.personal_reference
          this.modalidad.max_cosigner = this.loan_modality.loan_modality_parameter.max_cosigner
          this.modalidad.max_lenders = this.loan_modality.loan_modality_parameter.max_lenders

          this.loan_detail.min_guarantor_category = this.loan_modality.loan_modality_parameter.min_guarantor_category
          this.loan_detail.max_guarantor_category = this.loan_modality.loan_modality_parameter.max_guarantor_category
          if(this.loan_modality.loan_modality_parameter.quantity_ballots > 1){
            this.visible = true
          }else{
            this.visible = false
          }
          this.getBallots(id)
          this.generateContributions()
        }
      }catch (e) {
        console.log(e)
        this.toastr.error(e.type)
      }finally {
        this.loading = false
      }
    },

    //Metodo para sacar boleta de un afiliado
  async getBallots(id) {
    try {
      this.data_ballots=[]
      let res = await axios.get(`affiliate/${id}/contribution`, {
         params:{
           city_id: this.$store.getters.cityId,
           choose_diff_month: this.choose_diff_month,
           number_diff_month: this.number_diff_month,
           sortBy: ['month_year'],
           sortDesc: [1],
           per_page: this.modalidad.quantity_ballots,
           page: 1,
         }
        })
      this.lender_contribution = res.data
      this.affiliate_contribution.valid = this.lender_contribution.valid
      this.affiliate_contribution.state_affiliate = this.lender_contribution.state_affiliate
      this.affiliate_contribution.name_table_contribution = this.lender_contribution.name_table_contribution
      this.data_ballots = res.data.data
      console.log(this.affiliate_contribution)
      this.fecha= new Date();

      for (let i = 0; i < this.modalidad.quantity_ballots; i++) {//colocar 1
        if(this.lender_contribution.valid && this.lender_contribution.state_affiliate =='Activo'){
          this.enabled = false
          this.editar=false
           //Carga los datos en los campos para ser visualizados en la interfaz
         // console.log(this.period[i]);
            /*this.payable_liquid[i] = data_ballots[i].payable_liquid
            if(i==0){//solo se llena los bonos de la ultima boleta de pago
              this.bonos[0] = data_ballots[0].border_bonus
              this.bonos[1] = data_ballots[0].east_bonus
              this.bonos[2] = data_ballots[0].position_bonus
              this.bonos[3] = data_ballots[0].public_security_bonus
            }*/
            this.contribution[i].contributionable_id = this.data_ballots[i].id
            this.contribution[i].payable_liquid = this.data_ballots[i].payable_liquid != null ? this.data_ballots[i].payable_liquid : 0
            this.contribution[i].border_bonus = this.data_ballots[i].border_bonus != null ? this.data_ballots[i].border_bonus : 0
            this.contribution[i].east_bonus = this.data_ballots[i].east_bonus != null ? this.data_ballots[i].east_bonus : 0
            this.contribution[i].position_bonus = this.data_ballots[i].position_bonus != null ? this.data_ballots[i].position_bonus : 0
            this.contribution[i].public_security_bonus = this.data_ballots[i].public_security_bonus != null ? this.data_ballots[i].public_security_bonus : 0
            this.contribution[i].period = this.$moment(this.data_ballots[i].month_year).format('YYYY-MM-DD')
            this.contribution[i].month = this.$moment(this.data_ballots[i].month_year).format('MMMM')
          
        } else if(!this.lender_contribution.valid && this.lender_contribution.state_affiliate =='Activo'){
            this.enabled = false
            this.editar=false
            /*this.enabled=true
            this.editar=true
            this.contribution[0].period = this.$moment(this.fecha).subtract(1,'months').format('MMMM')
            this.contribution[1].period = this.$moment(this.fecha).subtract(2,'months').format('MMMM')
            this.contribution[2].period = this.$moment(this.fecha).subtract(3,'months').format('MMMM')
            console.log("No se tienen boletas del ultimo mes")
            this.contribution[0].payable_liquid=0
            this.contribution[1].payable_liquid=this.payable_liquid[1]
            this.contribution[2].payable_liquid=this.payable_liquid[2]
            this.bonos[0]= this.bonos[0]
            this.bonos[1]= this.bonos[1]
            this.bonos[2]= this.bonos[2]
            this.bonos[3]= this.bonos[3]
            //this.clearForm()//TODO ver si es necesario, ya que sin la funcion igual se carga los datos declarados por defecto de las variables
            */
            this.contribution[i].contributionable_id = 0
            this.contribution[i].payable_liquid = 0
            this.contribution[i].border_bonus = 0
            this.contribution[i].east_bonus = 0
            this.contribution[i].position_bonus = 0
            this.contribution[i].public_security_bonus = 0
            this.contribution[i].period = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('YYYY-MM-DD')
            this.contribution[i].month = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('MMMM')
        } else if(this.lender_contribution.valid && this.lender_contribution.state_affiliate =='Pasivo'){
            this.enabled = true
            if(this.data_ballots[i].rent > 0 && this.data_ballots[i].dignity_rent > 0){
              this.editar = false
            }else {
              this.editar = false
            }
            this.contribution[i].contributionable_id = this.data_ballots[i].id
            this.contribution[i].payable_liquid = this.data_ballots[i].rent != null ? this.data_ballots[i].rent : 0
            this.contribution[i].dignity_rent = this.data_ballots[i].dignity_rent != null ? this.data_ballots[i].dignity_rent : 0
            this.contribution[i].period = this.$moment(this.data_ballots[i].month_year).format('YYYY-MM-DD')
            this.contribution[i].month = this.$moment(this.data_ballots[i].month_year).format('MMMM')
        }
        else if(!this.lender_contribution.valid && this.lender_contribution.state_affiliate =='Pasivo'){
            this.enabled = true
            this.contribution[i].contributionable_id = 0
            this.contribution[i].payable_liquid = this.contribution[i].payable_liquid
            this.contribution[i].dignity_rent = 0
            this.contribution[i].period = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('YYYY-MM-DD')
            this.contribution[i].month = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('MMMM')
        }
        else if(!this.lender_contribution.valid && this.lender_contribution.state_affiliate =='Comisión'){
            this.enabled = true
            this.contribution[i].contributionable_id = 0
            this.contribution[i].payable_liquid = 0
            this.contribution[i].period = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('YYYY-MM-DD')
            this.contribution[i].month = this.$moment(this.lender_contribution.current_tiket).subtract(i,'months').format('MMMM')
        }
        
        else {
          this.toastr.error("Ocurrio caso especial")}
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
  generateContributions () {
    this.contribution = []
    for (let i = 0; i < this.modalidad.quantity_ballots; i++) {
      this.contribution.push({
        contributionable_id: null,
        payable_liquid: 0,
        position_bonus: 0,
        border_bonus: 0,
        public_security_bonus: 0,
        east_bonus: 0,
        dignity_rent: 0,
        period: null,
        adjustment_amount: 0,
        adjustment_description: null,
        loan_contributions_adjust_id: null,
      })
    }
  },
  getContributions() {
    return this.contribution
  },
  appendIconCallback () {
      if(this.number_diff_month < 10){
      this.number_diff_month++
      this.choose_diff_month = true
      this.getBallots(this.$route.query.affiliate_id)
    }
  },
  prependIconCallback () {

      if(this.number_diff_month > 1){
      this.number_diff_month--
      this.choose_diff_month = true
      this.getBallots(this.$route.query.affiliate_id)
    }
  },
  /*async saveAdjustment(i){
    try {
      //Verificar si el afiliado es pasivo para introducir su contribución
      if(this.affiliate_contribution.state_affiliate == 'Pasivo'){  
      let res = await axios.post(`aid_contribution/updateOrCreate`,{
        affiliate_id: this.$route.query.affiliate_id,
        month_year: this.contribution[i].period,
        rent: this.contribution[i].payable_liquid,
        dignity_rent: this.contribution[i].dignity_rent,
      })
      this.contribution_passive = this.lender_contribution  
        this.contribution[i].contributionable_id = this.contribution_passive.id
        alert(this.contribution_passive.id)
      }
      //guardar el ajuste
        let res = await axios.post(`loan_contribution_adjust`, {
        affiliate_id: this.$route.query.affiliate_id,
        adjustable_id: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.contribution[i].contributionable_id : this.$route.query.affiliate_id,
        adjustable_type: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.affiliate_contribution.name_table_contribution : 'affiliate',
        type_affiliate: 'lender',
        amount: this.contribution[i].adjustment_amount,
        type_adjust: this.affiliate_contribution.state_affiliate != 'Comisión' ? 'adjust' : 'liquid',
        period_date: this.$moment(this.fecha).format('YYYY-MM-DD'),
        description: this.contribution[i].adjustment_description
      })
      let ajuste = res.data
      console.log(ajuste)
    } catch (e) {
      console.log(e)
    }
  },*/
   /*async saveAdjustment() {
    try {
        let res = await axios.post(`loan_contribution_adjust`, {
        affiliate_id: this.$route.query.affiliate_id,
        adjustable_id: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.contribution[i].contributionable_id : this.$route.query.affiliate_id,
        adjustable_type: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.affiliate_contribution.name_table_contribution : 'affiliate',
        type_affiliate: 'lender',
        amount: this.contribution[i].adjustment_amount,
        type_adjust: this.affiliate_contribution.state_affiliate != 'Comisión' ? 'adjust' : 'liquid',
        period_date: '2021-03-15',
        description: this.contribution[i].adjustment_description
      })
      let ajuste = res.data
    } catch (e) {
     console.log(e)   
    }
    },
 async dialogAdjustment(i){
    try {
      //Verificar si el afiliado es pasivo para introducir su contribución
      if(this.affiliate_contribution.state_affiliate == 'Pasivo'){  
      let res = await axios.post(`aid_contribution/updateOrCreate`,{
        affiliate_id: this.$route.query.affiliate_id,
        month_year: "2021-02-01",
        rent: this.contribution[i].payable_liquid,
        dignity_rent: this.contribution[i].dignity_rent,
      })
      this.contribution_passive = res.data  
        this.contribution[i].contributionable_id = this.contribution_passive.id
        alert(this.contribution_passive.id)
      }
      //Envio de informacion al Dailog
      this.bus.$emit('openDialog', {
        accion: 'new',
        affiliate_id: this.$route.query.affiliate_id,
        adjustable_id: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.contribution[i].contributionable_id : this.$route.query.affiliate_id,
        adjustable_type: this.affiliate_contribution.state_affiliate != 'Comisión' ? this.affiliate_contribution.name_table_contribution : 'affiliate',
        type_affiliate: 'lender',
        type_adjust: this.affiliate_contribution.state_affiliate != 'Comisión' ? 'adjust' : 'liquid',
        period_date: '2021-03-15'
      })
    } catch (e) {
      console.log(e)
    }
  },*/

 }
};
</script>
<style scoped>
.v-textarea--outlined >>> fieldset {
  border-color: rgba(192, 0, 250, 0.986);
}
/*.v-list-item__title {
  font-weight: 400 !important;
}
.v-input {
  font-size: 14px !important;
}
.v-input .v-label {
  font-size: 14px !important;
}
.v-text-field {
  padding: 0 !important;
  /*line-height: 1em !important;
  background: royalblue;
}
 .v-textarea input {
  padding: 4px 0 2px 0 !important;
  line-height: 1em !important;
} 
.v-input__slot {
  margin-bottom: 4px !important;
}
.v-input--hide-details {
  margin-bottom: 0 !important;
}
.v-input__slot {
  margin-bottom: 4px !important;
}
.v-text-field {
  min-height: 36px !important;
  /*background-color: orange;
  width: 10em;
  height: 0.5em;
}
.v-input .v-input__control {
  min-height: 36px !important;
}
.v-select-list .v-list__tile {
  font-size: 12px !important;
  height: 36px !important;
}*/
</style>