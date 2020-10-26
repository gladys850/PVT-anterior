<template>
  <v-container class="ma-0 pa-0">
    <v-row>
      <v-col cols="8" class="text-center">
        <v-card color="#EDF2F4" shaped class="mx-5">
          <v-card-title>Préstamos</v-card-title>
          <v-card-text>
            <div>
              <h1 v-if="loan.length === 0">NO TIENE PRÉSTAMOS REGISTRADOS</h1>
              <ul style="list-style: none;" class="pa-0">
                <li v-for="(item,index) in loan" :key="item.id" class="pb-2">
                  <div v-if="index < 3">
                    {{item.procedure_modality_id}}
                    <strong>Cód.:</strong>
                    {{ item.code }} |
                    <strong>Desembolsado:</strong>
                    {{ item.amount_approved}}
                    <strong>Saldo capital:
                    {{ item.balance  }}
                    </strong>
                    <span>
                      <v-tooltip
                        left              
                      >
                        <template v-slot:activator="{ on }">
                          <v-btn
                            icon
                            dark
                            small
                            color="warning"
                            bottom
                            right                        
                            v-on="on" 
                            :to="{ name: 'flowAdd', params: { id: item.id }}" 
                          >
                            <v-icon>mdi-eye</v-icon>
                          </v-btn>
                        </template>
                        <span>Ver préstamo</span>
                      </v-tooltip>            
                    </span>
                    
                    <span>
                    <v-tooltip
                    left  
                    v-if="item.procedure_modality_id != 32 && item.procedure_modality_id != 33"            
                    >
                    <template v-slot:activator="{ on }">
                      <v-btn
                        icon
                        dark
                        small
                        color="success"
                        bottom
                        right                        
                        v-on="on" 
                        @click.stop="validateRefinancingLoan(affiliate.id, item.id)"
                      >
                      <v-icon>mdi-cash-multiple</v-icon>
                      </v-btn>
                    </template>
                    <span>Refinanciamiento</span>
                    </v-tooltip>            
                    </span>
                    <span>

                    <v-tooltip
                    left   
                    v-if="item.procedure_modality_id != 32 && item.procedure_modality_id != 33"            
                    >
                      <template v-slot:activator="{ on }">
                        <v-btn
                          icon
                          dark
                          small
                          color="info"
                          bottom
                          right
                          v-on="on" 
                          @click.stop="validateReprogrammingLoan(affiliate.id, item.id)"
                        >
                        <v-icon>mdi-calendar-clock</v-icon>
                        </v-btn>
                      </template>
                      <span>Reprogramacion</span>
                    </v-tooltip>            
                    </span>


                    <v-progress-linear
                      :color="randomColor()"
                      height="15"
                      :value="(((item.amount_approved-item.balance)*100)/item.amount_approved).toFixed(2)"
                      striped
                    >
                      <strong>Porcentaje pagado: {{ (((item.amount_approved-item.balance)*100)/item.amount_approved).toFixed(2) }}%</strong>
                    </v-progress-linear>
                  
                  </div>

                </li>
              </ul>
            </div>
            <v-tooltip
              left v-if="$store.getters.permissions.includes('create-loan')"              
            >
              <template v-slot:activator="{ on }">
                <v-btn
                  fab
                  dark
                  small
                  color="success"
                  bottom
                  right
                  absolute
                  v-on="on"
                  style="margin-right: 99px;"
                  @click="validateAffiliate($route.params.id, 'is_new')"                
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
              <span>Nuevo trámite</span>
            </v-tooltip>
            <v-tooltip
              left v-if="$store.getters.permissions.includes('create-loan')"              
            >
              <template v-slot:activator="{ on }">
                <v-btn
                  fab
                  dark
                  small
                  color="success"
                  bottom
                  right
                  absolute
                  v-on="on"
                  style="margin-right: 49px;"
                  @click="validateAffiliate($route.params.id, 'is_refinancing')"          
                >
                  <v-icon>mdi-cash-multiple</v-icon>
                </v-btn>
              </template>
              <span>Refinanciamiento SISMU</span>
            </v-tooltip>
            <v-tooltip
              left v-if="$store.getters.permissions.includes('create-loan')"              
            >
              <template v-slot:activator="{ on }">
                <v-btn
                  fab
                  dark
                  small
                  color="info"
                  bottom
                  right
                  absolute
                  v-on="on"
                  style="margin-right: -9px;"
                  @click="validateAffiliate($route.params.id, 'is_reprogramming')"              
                >
                  <v-icon>mdi-calendar-clock</v-icon>
                </v-btn>
              </template>
              <span>Reprogramación SISMU</span>
            </v-tooltip>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="4" class="ma-0 pa-0">
        <v-card color="secondary">
          <v-card-title class="ma-0 pa-0">
            <v-col cols="6">
              <v-row>
                <div>
                  <div v-if="profilePictures.length > 0">
                    <v-avatar class="mx-auto d-block elevation-3" size="125">
                      <v-img
                        :src="`data:${profilePictures[0].format};base64,${profilePictures[0].content}`"
                      />
                    </v-avatar>
                  </div>
                  <div v-else>
                    <v-avatar class="mx-auto d-block elevation-3" size="125">
                      <v-icon size="125" color="black" v-if="affiliate.gender==='M'">mdi-face</v-icon>
                      <v-icon size="125" color="black" v-else>mdi-face-woman</v-icon>
                    </v-avatar>
                  </div>
                </div>
              </v-row>
            </v-col>
            <v-col cols="5" class="red--text text--lighten-5 ma-0 pa-0">
              <small>
                C.I.: {{affiliate.identity_card_ext}}
                <br />
                Categoría: <span v-if="affiliate.category != null">{{affiliate.category.name}}</span>
                <br />
                Estado:  {{this.state_name_status}}
                <br />
              </small>
            </v-col>
          </v-card-title>
          <v-card-text class="ma-0 pa-0">
            <v-col cols="12" color="#EDF2F4" class="red--text text--lighten-5 ma-0 pa-0">
              <center>
                Grado: {{this.degree_name}}
                <br/>
                Unidad: {{this.unit_name}}
                <br />
                Estado Civil: <span v-if ="affiliate.gender==='M'">
                  {{affiliate.civil_status=='C'? 'CASADO':affiliate.civil_status=='S'? 'SOLTERO':affiliate.civil_status=='D'?'DIVORCIADO':'VIUDO'}}
                  </span>
                <span v-else>
                  {{affiliate.civil_status=='C'? 'CASADA':affiliate.civil_status=='S'? 'SOLTERA':affiliate.civil_status=='D'?'DIVORCIADA':'VIUDA'}}
                  </span>
              </center>
            </v-col>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import common from "@/plugins/common";
export default {
  name: "affiliate-dashboard",
  props: {
    affiliate: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: false,
    fingerprints: [],
    profilePictures: [],
    degree_name: null,
    category_name: null,
    unit_name: null,
    state_name: null,
    state_name_type: null,
    state_name_status: null,
    loan: [],
    loan_one: null,
    loan_two: null,
    loan_three: null
  }),
  created() {
    this.randomColor = common.randomColor;
  },
  computed: {
    isNew() {
      return this.$route.params.id == "new";
    }
  },
  watch: {
    affiliate(newVal, oldVal) {
      if (oldVal != newVal) {
        if (newVal.hasOwnProperty('category_id')) this.getCategory_name(newVal.category_id)
        if (newVal.hasOwnProperty('degree_id')) this.getDegree_name(newVal.degree_id)
        if (newVal.hasOwnProperty('unit_id')) this.getUnit_name(newVal.unit_id)
      }
    }
  },
  mounted() {
    if (!this.isNew) {
      this.getProfilePictures(this.$route.params.id);
      this.getLoan(this.$route.params.id);
      this.getState_name(this.$route.params.id);
    }
  },
  methods: {
    async getProfilePictures(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/profile_picture`);
        this.profilePictures = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`degree/${id}`);
        this.degree_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`category/${id}`)
        this.category_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getUnit_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`unit/${id}`);
        this.unit_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    /* async get_refinanciamiento(affiliateid, loanid) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${affiliateid}/${loanid}`);
        this.unit_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },*/
    async getLoan(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/loan`, {
          params: {
            guarantor:0
          }
        });
        this.loan = res.data.data;
        let num = this.loan.length;
        /* FIXME no tiene uso
        let idl=this.loan.id;
       */
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getState_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/state`);
        this.state_name = res.data;
        this.state_name_type = this.state_name.affiliate_state_type.name;
        this.state_name_status = this.state_name.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    /*validateRefinanciamiento(affiliateid, loanid){
      this.$router.push({ name: 'loanAddref',  params: { hash: 'ref'}, query:{ affiliate_id: affiliateid,loan_id:loanid } })
    },
    validateReprogramacion(affiliateid, loanid){
     this.$router.push({ name: 'loanAddrep',  params: { hash: 'ref'}, query:{ affiliate_id: affiliateid,loan_id:loanid } })
    },*/
    validateAffiliate(id, type_procedure) {
      if(this.state_name_type != 'Baja' && this.state_name_status != 'Fallecido' && this.state_name != ''){
        if(this.affiliate.identity_card != null && this.affiliate.city_identity_card_id != null){
          if(this.affiliate.civil_status != null){
            if(this.affiliate.financial_entity_id != null && this.affiliate.account_number != null && this.affiliate.sigep_status != null){
              if(this.affiliate.birth_date != null && this.affiliate.city_birth_id != null){
                if(type_procedure == "is_new"){
                  this.$router.push({ name: 'loanAdd',  params: { hash: 'new'},  query: { affiliate_id: id}})
                } if(type_procedure == "is_refinancing"){
                  this.$router.push({ name: 'loanAdd', params: { hash: 'refinancing'}, query: { affiliate_id: id, type_sismu: true}})
                } if(type_procedure == "is_reprogramming"){
                  this.$router.push({ name: 'loanAdd', params: { hash: 'reprogramming'}, query: { affiliate_id: id, type_sismu: true}})
                }
                }else{
                  this.toastr.error("El afiliado no tiene registrado su fecha de nacimiento ó ciudad de nacimiento.")
                }
            }else{
            this.toastr.error("El afiliado no tiene registrado la entidad financiera")
            }         
          }else{
            this.toastr.error("El afiliado no tiene registrado su estado civil.")
          }          
        }else{
          this.toastr.error("El afiliado no tiene registrado su CI ó ciudad de expedición del CI.")
        }
      }else{
        this.toastr.error("El afiliado no puede acceder a un préstamo por estar fallecido ó dado de baja ó no tener registrado su estado.")
      }
      
    },
   /* validateRefinancing(a_id, l_id){
      for(let i = 0; i < this.loan.length; i++){
        if(l_id == this.loan[i].id){
          if(this.loan[i].procedure_modality_id != 32 && this.loan[i].procedure_modality_id != 33){
            this.$router.push({ name: 'loanAdd',  params: { hash: 'refinancing'}, query:{ affiliate_id: a_id, loan_id: l_id } })
          }else{
            this.toastr.error("No se puede realizar el REFINANCIAMIENTO para un trámite de Anticipo")
          }
        }
      }
    },
    validateReprogramming(a_id, l_id){
      for(let i = 0; i < this.loan.length; i++){
        if(l_id == this.loan[i].id){
          if(this.loan[i].procedure_modality_id != 32 && this.loan[i].procedure_modality_id != 33){
            this.$router.push({ name: 'loanAdd',  params: { hash: 'reprogramming'}, query:{ affiliate_id: a_id, loan_id: l_id } })
          }else{
            this.toastr.error("No se puede realizar la REPROGRAMACIÓN para un trámite de Anticipo")
          }
        }
      }
    }*/
    async validateRefinancingLoan(a_id, l_id){
      try {
          let res = await axios.post(`loan/${l_id}/validate_re_loan`,{
            type_procedure: true
          })
          let validate = res.data
          if(validate.percentage){
            if(validate.paids){
              if(validate.defaulted){
                this.$router.push({ name: 'loanAdd',  params: { hash: 'refinancing'}, query:{ affiliate_id: a_id, loan_id: l_id } })
                }else{
                  this.toastr.error("El préstamo se encuentra en MORA")
                }
            }else{
              this.toastr.error("Tiene pendiente menos de TRES pagos para finalizar la deuda")
            }
          }else{
            this.toastr.error("No tiene el 25% pagado de su préstamo para acceder a un refinanciamiento")
          }
      } catch (e) {
        console.log(e)
      }
    },
    async validateReprogrammingLoan(a_id, l_id){
      try {
          let res = await axios.post(`loan/${l_id}/validate_re_loan`,{
            type_procedure: false
          })
          let validate = res.data
           if(validate.paids){
              if(!validate.defaulted){
                this.$router.push({ name: 'loanAdd',  params: { hash: 'reprogramming'}, query:{ affiliate_id: a_id, loan_id: l_id } })
                }else{
                  this.toastr.error("El préstamo se encuentra en MORA")
                }
            }else{
              this.toastr.error("Tiene pendiente menos de TRES pagos para finalizar la deuda")
            }          
      } catch (e) {
        console.log(e)
      }
    },
  }
};
</script>