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
               :affiliate.sync="affiliate"
               :loan.sync="loan"/>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item 
          :value="'tab-2'"
        >
          <v-card flat tile >
            <v-card-title v-if="$store.getters.permissions.includes('print-plan-payment') ">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    small
                    color='dark'
                    top
                    right
                    absolute
                    v-on="on"
                    @click="imprimir($route.params.id)"           
                  >
                    <v-icon>mdi-printer</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Imprimir plan de pagos</span>
                </div>
              </v-tooltip>
            </v-card-title>
            <v-card-text>
              <BallotsResult
                :bonos.sync="bonos"
                :datos.sync="datos"
                :payable_liquid.sync="payable_liquid"
                :calculos.sync="calculos"
                :modalidad.sync="modalidad">
                <template v-slot:title>
                  <v-col cols="12" class="py-0">
                    <span class="text-grey darken-1 title font-weight-light">
                      RESULTADOS DEL PRÉSTAMO
                    </span>  
                  </v-col>                  
                </template>
              </BallotsResult>
            </v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item
         :value="'tab-3'"
        >
          <v-card flat tile >
            <v-card-text>
              <DocumentsFlow
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
              <ObserverFlow 
              :bus1 = "bus1"
              :observations.sync="observations" 
              :record.sync="record" 
              :loan.sync="loan"/>
            </v-card-text>
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
import DocumentsFlow from '@/components/workflow/DocumentsFlow'
import ObserverFlow from '@/components/workflow/ObserverFlow'
import PoliceData from '@/components/affiliate/PoliceData'
import Dashboard from '@/components/workflow/Dashboard'

export default {
  name: "flow-index",
  components: {
    Breadcrumbs,
    Profile,
    BallotsResult,
    DocumentsFlow,
    PoliceData,
    ObserverFlow,
    Dashboard
  },
  data: () => ({
    bus1: new Vue(),//Creamos la ionstancia de bus1
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
    loan:{},
    datos: {},
    formulario: [],
    record: [],
    observations: [],
    intervalos: {},
    modalidad: {},

    icons: true,
    vertical: true,
    tabs: 3,
    i: 0,
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
  beforeMount() {},
   mounted() {
    this.getloan(this.$route.params.id)
    this.getObservation(this.$route.params.id)
    this.getRecords(this.$route.params.id)
    console.log("params "+this.$route.params.id)
    this.bus1.$on("emitGetObservation", id => {//escucahamos la emision de ObserverFlow
      console.log('entraaaa 2')
      this.getObservation(id); //y monstamos la lista de observaciones segun el id del prestamo
    });
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
          text: 'Préstamo',
          to: { name: 'flowIndex' }
        }
      ]
      breadcrumbs.push({
        text: this.loan.code,
        to: { name: 'flowAdd', params: { id: this.loan.id } }
      })
      this.$store.commit('setBreadcrumbs', breadcrumbs)
    },
    async getloan(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}`);
        this.loan = res.data;
        this.calculos.plazo=this.loan.loan_term
        this.calculos.amount_approved=this.loan.amount_approved
        this.calculos.quota_calculated=this.loan.estimated_quota
        this.calculos.amount_maximum_suggested=this.loan.amount_approved
        this.calculos.payable_liquid_calculated=this.loan.payable_liquid_calculated
        this.calculos.bonus_calculated= this.loan.bonus_calculated
        this.calculos.liquid_qualification_calculated= this.loan.liquid_qualification_calculated
        this.calculos.indebtedness_calculated = this.loan.indebtedness_calculated
        this.calculos.montos=this.loan.amount_approved
       
        let res1 = await axios.get(`affiliate/${this.loan.disbursable_id}`)
        this.affiliate = res1.data
        this.setBreadcrumbs()
        console.log(this.loan)
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
        
         for (this.i = 0; this.i< this.observations.length; this.i++) {
            let res1 = await axios.get(`user/${this.observations[this.i].user_id}`)
            this.observations[this.i].user_name=res1.data.username
         }
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
    async getRecords(id) {
      try {
        this.loading = true
        let res = await axios.get(`record`,{
          params:{
            loan_id: id
          }
        })
        this.record = res.data.data
        console.log(this.record)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async imprimir(item)
    {
      try {
          let res = await axios.get(`loan/${item}/print/plan`)
           console.log("plan "+item)
          printJS({
            printable: res.data.content,
            type: res.data.type,
            file_name: res.data.file_name,
            base64: true
        })  
      } catch (e) {
        this.toastr.error("Ocurrió un error en la impresión.")
        console.log(e)
      }
      
    }
  }
}
</script>
