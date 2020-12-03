<template>
  <v-flex xs12 class="px-3">
      <v-form>
        <v-row justify="center">
          <v-col cols="12"  >
            <v-card>
              <ValidationObserver ref="observer" >
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
              <v-container class="py-0" >
                <v-row>
                  <v-col cols="12" md="12">
                    BOLETAS DE PAGO
                  </v-col>
                  <v-col cols="12" md="4" class="py-0"  >
                    <ValidationProvider v-slot="{ errors }" name="1ra Boleta de pago" mode="aggressive">
                    <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="payable_liquid[0]"
                      label="1ra Boleta"
                     ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="4" class="py-0" v-if="visible">
                    <ValidationProvider v-slot="{ errors }" name="2da Boleta de pago" :rules="'min_value:'+livelihood_amount"  mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="payable_liquid[1]"
                      label="2ra Boleta"
                      :outlined="editar"
                  ></v-text-field>
                  </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="4" class="py-0" v-if="visible" >
                    <ValidationProvider v-slot="{ errors }" name="3ra Boleta de pago" :rules="'min_value:'+livelihood_amount"  mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="payable_liquid[2]"
                      label="3ra Boleta"
                      :outlined="editar"
                     ></v-text-field>
                     </ValidationProvider>
                  </v-col>
                  <v-col cols="12" class="py-0" >
                    BONOS
                  </v-col>
                  <v-col cols="12" md="3" >
                    <ValidationProvider v-slot="{ errors }" name="Bono Frontera" :rules="'max_value:'+payable_liquid[0]"  mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="bonos[0]"
                      label="Bono Frontera"
                      :outlined="editar"
                     ></v-text-field>
                     </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider v-slot="{ errors }" name="Bono Oriente" :rules="'max_value:'+payable_liquid[0]"  mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="bonos[1]"
                      label="Bono Oriente"
                      :outlined="editar"
                     ></v-text-field>
                     </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3" >
                    <ValidationProvider v-slot="{ errors }" name="Bono Cargo" :rules="'max_value:'+payable_liquid[0]"  mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="bonos[2]"
                      label="Bono Cargo"
                      :outlined="editar"
                    ></v-text-field>
                    </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3">
                    <ValidationProvider v-slot="{ errors }" name="Bono Seguridad Ciudadana" :rules="'max_value:'+payable_liquid[0]" mode="aggressive">
                    <v-text-field
                    :error-messages="errors"
                      dense
                      v-model="bonos[3]"
                      label="Bono Seguridad Ciudadana"

                      :outlined="editar"
                     ></v-text-field>
                     </ValidationProvider>
                  </v-col>
                  <template v-if="type_sismu">
                  <v-col cols="12" class="py-0">
                    DATOS SISMU
                  </v-col>
                  <v-col cols="12" md="3" >
                    <ValidationProvider v-slot="{ errors }" name="cuota" :rules="'min_value:1'"  mode="aggressive">
                    <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="data_sismu.quota_sismu"
                      outlined
                      label="Cuota"
                     ></v-text-field>
                     </ValidationProvider>
                  </v-col>
                  <v-col cols="12" md="3" class="ma-0 pa-0" v-if="this.loanTypeSelected.id==11 || this.loanTypeSelected.id==12">
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
                :affiliate.sync="affiliate"
                :data_loan.sync="data_loan"/>
             </ValidationObserver>
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
    editar:false,
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
    },
    livelihood_amount:{
    type: Number,
    required:true,
    default:0
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
      if(this.$route.params.hash == 'reprogramming'){
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
          this.getLoanModality(this.$route.query.affiliate_id)
        } /*else{
        console.log('NO ES IGUAL A MODALIDAD INTERVALS'+this.interval[i].procedure_type_id +"=="+this.loanTypeSelected.id )
        }*/
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
        let resp = await axios.post(`affiliate/${id}/loan_modality?procedure_type_id=${this.loanTypeSelected.id}`,{
          type_sismu: this.data_sismu.type_sismu,
          cpop_sismu: this.data_sismu.cpop_sismu,
          reprogramming: this.reprogramming
        })
        if(resp.data ==''){
          this.loan_detail.not_exist_modality = true
          this.toastr.error("El afiliado no puede ser evaluado en esta modalidad")
        }else{
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
          this.modalidad.max_lenders = loan_modality.loan_modality_parameter.max_lenders

          this.loan_detail.min_guarantor_category= loan_modality.loan_modality_parameter.min_guarantor_category
          this.loan_detail.max_guarantor_category= loan_modality.loan_modality_parameter.max_guarantor_category
          if(loan_modality.loan_modality_parameter.quantity_ballots>1){
            this.visible = true
          }else{
            this.visible = false
          }
          this.getBallots(id)
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
    try {
      let data_ballots=[]
      let res = await axios.get(`affiliate/${id}/contribution`, {
        params:{
          city_id: this.$store.getters.cityId,
          sortBy: ['month_year'],
          sortDesc: [1],
          //per_page: this.modalidad.quantity_ballots,
          page: 1,
        }
      })
      //console.log("-------")
      //console.log(this.modalidad.quantity_ballots)
      data_ballots = res.data.data
      //console.log(data_ballots)

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