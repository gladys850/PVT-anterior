<template>
  <v-container class="ma-0 pa-0">
    <v-row>
      <v-col cols="8" class="text-center">
        <v-card color='#EDF2F4' shaped class="mx-5">
            <v-card-title>
              Prestamos
            </v-card-title>
            <v-card-text>
              <div>
                <v-progress-linear
                :color="randomColor()"
                height="15"
                v-model='this.loan_one'
                striped
                ><strong>{{loan_one}}%</strong>
                </v-progress-linear>
                <br>
                <v-progress-linear
                  :color="randomColor()"
                  height="15"
                  v-model='this.loan_two'
                  striped
                ><strong>{{loan_two}}%</strong>
                </v-progress-linear>
                <br>
                <v-progress-linear
                  height="15"
                  v-model='this.loan_three'
                  striped
                  :color="randomColor()"
                ><strong>{{loan_three}}%</strong>
                </v-progress-linear>
              </div>
              <v-tooltip left>
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
                      :to="{ name: 'loanAdd', params: { hash: 'new'}, query: { affiliate_id: affiliate.id}}"
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
        <v-card
          color='secondary'
        >
          <v-card-title class="ma-0 pa-0">
            <v-col cols="6">
            <v-row >
              <template v-for="image in profilePictures">
                <v-col cols="12" :key="image.name">
                  <v-avatar
                    class="mx-auto d-block elevation-3"
                    size="125"
                  >
                    <v-img :src="`data:${image.format};base64,${image.content}`"/>
                  </v-avatar>
                </v-col>
              </template>
            </v-row>
          </v-col>
          <v-col cols="4" class="red--text text--lighten-5 ma-0 pa-0">
            <small>
              ITEM: {{this.category_name}}
              <br>
              C.I.: {{affiliate.identity_card}}
              <v-icon color='#EDF2F4'>mdi-account</v-icon>
              {{this.state_name_type}}<br>
            </small>
              <h6 class="text-center">{{this.state_name_status}}</h6>
          </v-col>
          </v-card-title>
          <v-card-text class="ma-0 pa-0">
            <title>Prestamos</title>
            <v-col cols="12" color='#EDF2F4' class="red--text text--lighten-5 ma-0 pa-0" >
              <center>
                <v-icon color='#EDF2F4'>mdi-police-badge</v-icon>
                {{this.degree_name}}
                <br>
                UNIDAD: {{this.unit_name}}
                <br>
                TIPO: {{affiliate.type}}
              </center>
            </v-col>
            <br>
            <v-col cols="12" color='#EDF2F4' class="red--text text--lighten-5 ma-0 pa-0">
              <center>
                <v-icon color='#EDF2F4'>mdi-account-heart</v-icon>
                {{affiliate.civil_status=='C'? 'CASADO':affiliate.civil_status=='S'? 'SOLTERO':affiliate.civil_status=='D'?'DIVORCIADO':'VIUDO'}}
                <br>
              </center>
            </v-col>
            </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import common from '@/plugins/common'

export default {
  name: 'affiliate-fingerprint',
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
    degree_name:null,
    category_name:null,
    unit_name:null,
    state_name:null,
    state_name_type:null,
    state_name_status:null,
    loan:[],
    loan_one:null,
    loan_two:null,
    loan_three:null,
  }),
  created() {
    this.randomColor = common.randomColor
  },
  computed: {
    isNew() {
      return this.$route.params.id == 'new'
          },
  },
  mounted() {
    if (!this.isNew) {
      this.getProfilePictures(this.$route.params.id)
      this.getDegree_name(this.$route.params.id)
      this.getCategory_name(this.$route.params.id)
      this.getUnit_name(this.$route.params.id)
      this.getLoan(this.$route.params.id)
      this.getState_name(this.$route.params.id)
    }
    console.log('este es el id '+this.$route.params.id)
  },
  methods: {
    async getProfilePictures(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/profile_picture`)
        this.profilePictures = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getDegree_name(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/degree_name`)
        this.degree_name = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
      async getCategory_name(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/category_name`)
        this.category_name = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
      async getUnit_name(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/unit_name`)
        this.unit_name = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getLoan(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${1}/last_three_loans`)
        this.loan = res.data
        let num= this.loan.length
        if(num==3){
          this.loan_one=((this.loan[0].balance*100)/this.loan[0].amount_disbursement).toFixed(2)
          this.loan_two=((this.loan[1].balance*100)/this.loan[1].amount_disbursement).toFixed(2)
          this.loan_three=((this.loan[2].balance*100)/this.loan[2].amount_disbursement).toFixed(2)
        }else{
          if(num==2){
            this.loan_one=((this.loan[0].balance*100)/this.loan[0].amount_disbursement).toFixed(2)
            this.loan_two=((this.loan[1].balance*100)/this.loan[1].amount_disbursement).toFixed(2)
          }else{
            if(num==1){
              this.loan_one=((this.loan[0].balance*100)/this.loan[0].amount_disbursement).toFixed(2)
              }else{
                this.loan_one=null
              }
          }
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getState_name(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/state`)
        this.state_name = res.data
        this.state_name_type = this.state_name.state_type.name
        this.state_name_status=this.state_name.state.name
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>
