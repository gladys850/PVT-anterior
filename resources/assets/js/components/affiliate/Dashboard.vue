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
                    <strong>Cód.:</strong>
                    {{ item.code }} |
                    <strong>Desembolsado:</strong>
                    {{ item.amount_approved}}
                    <strong>Saldo capital:</strong>
                    {{ item.balance  }}
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
              left
              
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
                  style="margin-right: -9px;"
                  @click="validateAffiliate($route.params.id)"                
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </template>
              <span>Nuevo trámite</span>
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
    validateAffiliate(id) {
      if(this.state_name_type != 'Baja' && this.state_name_status != 'Fallecido' && this.state_name != ''){
        if(this.affiliate.identity_card != null && this.affiliate.city_identity_card_id != null){
          if(this.affiliate.civil_status != null){
            if(this.affiliate.birth_date != null && this.affiliate.city_birth_id != null){
              this.$router.push({ name: 'loanAdd',  params: { hash: 'new'},  query: { affiliate_id: id}})
            }else{
              this.toastr.error("El afiliado no tiene registrado su fecha de nacimiento ó ciudad de nacimiento.")
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
      
    }
  }
};
</script>