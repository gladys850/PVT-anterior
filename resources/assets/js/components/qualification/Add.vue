<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary" style="z-index: 1;">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-tabs
        v-model="tab"
        background-color="deep-blue accent-2"
        class="elevation-2"
        dark
        :vertical="vertical"
        :icons-and-text="icons"
      >
        <v-tabs-slider></v-tabs-slider>

        <v-tab
          v-if="!editable"
          :href="`#tab-1`"
        >
          <v-icon v-if="icons">mdi-trending-up</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-2`"
        >
          <v-icon v-if="icons">mdi-file-eye</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-3`"
        >
          <v-icon v-if="icons">mdi-file</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-4`"
        >
          <v-icon v-if="icons">mdi-account</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-5`"
        >
          <v-icon v-if="icons">mdi-police-badge</v-icon>
        </v-tab>
         <v-tab
          :href="`#tab-6`"
        >
          <v-icon v-if="icons">mdi-comment-eye-outline</v-icon>
        </v-tab>
        <v-tab-item
        :value="'tab-1'"
      >
        <v-card flat tile >
          <v-card-text>
            <Dashboard
              :affiliate.sync="affiliate"/>
          </v-card-text>
        </v-card>
      </v-tab-item>
        <v-tab-item
          :value="'tab-2'"
        >
          <v-card flat tile >
            <v-card-text>
              <BallotsResult
              :bonos.sync="bonos"
              :datos.sync="datos"
              :payable_liquid.sync="payable_liquid"
              :calculos.sync="calculos"
              :modalidad.sync="modalidad"/>
            </v-card-text>
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-3'"
        >
          <v-card flat tile >
            <v-card-text>
              <DocumentsQualification
              :datos.sync="datos"
              :formulario.sync="formulario"
              :calculos.sync="calculos"
              :intervalos.sync="intervalos"
              :modalidad.sync="modalidad"
              />
            </v-card-text>
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-4'"
        >
          <v-card flat tile >
            <v-card-text>
              <Profile
                v-if="!reload"
                :affiliate.sync="affiliate"
                :addresses.sync="addresses"
                :editable.sync="editable"
                :permission="permission"
              />
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item
          :value="'tab-5'"
        >
          <v-card flat tile >
          <v-card-text>
            <PoliceData
                v-if="!reload"
                :affiliate.sync="affiliate"
                :editable.sync="editable"
                :permission="permission"
              />
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item
          :value="'tab-6'"
        >
          <v-card flat tile >
          <v-card-text class=" pa-0 mb-0">
            <ObserverQualification
            :observations.sync="observations"
            :record.sync="record"
            /></v-card-text>
          </v-card>
        </v-tab-item>
      </v-tabs>
    </v-card-text>
  </v-card>
</template>
<script>
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import Profile from '@/components/affiliate/Profile'
import BallotsResult from '@/components/loan/BallotsResult'
import DocumentsQualification from '@/components/qualification/DocumentsQualification'
import ObserverQualification from '@/components/qualification/ObserverQualification'
import PoliceData from '@/components/affiliate/PoliceData'
import Dashboard from '@/components/qualification/Dashboard'

export default {
  name: "qualification-index",
  components: {
    Breadcrumbs,
    Profile,
    BallotsResult,
    DocumentsQualification,
    PoliceData,
    ObserverQualification,
    Dashboard
  },
  data: () => ({
    addresses:[],
    affiliate:{
      first_name: null,
      second_name:null,
      last_name: null,
      mothers_last_name:null,
      identity_card:null,
      birth_date:null,
      date_death:null,
      reason_death:null,
      phone_number:null,
      cell_phone_number:null,
      city_identity_card_id:null,
      date_entry:null,
      service_years:null,
      service_months:null,
      date_derelict:null,
      unit_name:null
    },
    bonos:[0,0,0,0],
    payable_liquid:[0,0,0],
    calculos:{},
    modalidad:{},

    datos:[],
    formulario:[],
    calculos:{},
    intervalos:{},
    modalidad:{},


    icons: true,
    vertical: true,
    tabs: 3,
    editable: false,
    reload: false,
    tab: 'tab-1'
  }),
  computed: {
    permission() {
      return {
        primary: this.primaryPermission,
        secondary: this.secondaryPermission
      }
    },
    secondaryPermission() {
      if (this.affiliate.id) {
        return this.$store.getters.permissions.includes('update-affiliate-secondary')
      } else {
        return this.$store.getters.permissions.includes('create-affiliate')
    }
  },
  primaryPermission() {
      if (this.affiliate.id) {
        return this.$store.getters.permissions.includes('update-affiliate-primary')
      } else {
        return this.$store.getters.permissions.includes('create-affiliate')
      }
    }
  },
  beforeMount(){
     this.getloan(4)
    this.getObservation(1)
    this.getRecords()

  },
  mounted() {
    this.getloan(1)
    this.getObservation(1)
    this.getRecords()
  },
  methods: {
    resetForm() {
      
      this.getAddress(this.$route.params.id)
      this.editable = false
      this.reload = true
      this.$nextTick(() => {
      this.reload = false
      })
    },
    setBreadcrumbs() {
      let breadcrumbs = [
        {
          text: 'Calificacion',
          to: { name: 'affiliateIndex' }
        }
      ]
      this.$store.commit('setBreadcrumbs', breadcrumbs)
    },
    async getloan(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}`);
        this.loan = res.data;
          this.calculos.plazo=this.loan.loan_term
          this.calculos.montos=this.loan.amount_approved
          this.calculos.calculo_de_cuota=this.loan.estimated_quota
          this.calculos.monto_maximo_sugerido=this.loan.amount_approved
          this.calculos.promedio_liquido_pagable=this.loan.payable_liquid_calculated
          this.calculos.total_bonos= this.loan.bonus_calculated
          this.calculos.liquido_para_calificacion= this.loan.liquid_qualification_calculated
          this.calculos.indice_endeudamiento = this.loan.indebtedness_calculated
                          
        let res1 = await axios.get(`affiliate/${this.loan.disbursable_id}`)
        this.affiliate = res1.data
        this.setBreadcrumbs()
        console.log(this.loan+'este es el prestamo')
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getObservation(id) {
    try {
      this.loading = true
      let res = await axios.get(`loan/${id}/observation`)
      this.observations = res.data
      console.log('este la observacion'+this.observations)
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  },
   async getAddress(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/address`)
        this.addresses = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  async getRecords() {
    try {
      this.loading = true
      let res = await axios.get(`record`,{param:{
      loan_id:4
    }})
      this.record = res.data.data
      console.log('este el record'+this.record)
    } catch (e) {
      console.log(e)
    } finally {
        this.loading = false
    }
  },
  },
}
</script>
