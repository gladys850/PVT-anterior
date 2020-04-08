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
          v-show="!isNew"
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
          v-show="!isNew"
        >
          <v-icon v-if="icons">mdi-account</v-icon>
        </v-tab>
        <v-tab
          v-show="!isNew"
          :href="`#tab-5`"
        >
          <v-icon v-if="icons">mdi-police-badge</v-icon>
        </v-tab>
         <v-tab
          v-show="!isNew"
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
              <Requirement
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
          <v-card-text>
            <ObserverQualification
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
import Requirement from '@/components/loan/Requirement'
import ObserverQualification from '@/components/qualification/ObserverQualification'
import PoliceData from '@/components/affiliate/PoliceData'
import Fingerprint from '@/components/affiliate/Fingerprint'
import Dashboard from '@/components/qualification/Dashboard'

export default {
  name: "qualification-index",
  components: {
    Breadcrumbs,
    Profile,
    BallotsResult,
    Requirement,
    PoliceData,
    Fingerprint,
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
    spouse: {
    affiliate_id: null,
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
    death_certificate_number:null,
    city_birth_id:null,
    civil_status:null,
    official:null,
    book:null,
    departure:null,
    marriage_date:null
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
    isNew() {
      return this.$route.params.id == 'new'
          },
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
  mounted() {
    if (!this.isNew) {
      this.resetForm()
    } else {
      this.tab = 'tab-2'
      this.editable = true
      this.setBreadcrumbs()
    }
  },
  methods: {
    resetForm() {
      this.getAffiliate(this.$route.params.id)
      this.getAddress(this.$route.params.id)
      this.editable = false
      this.reload = true
      this.$nextTick(() => {
      this.reload = false
      })
    },
    async saveAffiliate() {
      try {
        if (!this.editable) {
            this.editable = true
        } else {
          if (this.isNew) {
          // New affiliate
            await axios.post(`affiliate`, this.affiliate)
            this.toastr.success('Afiliado adicionado')
            await axios.patch(`affiliate/${this.affiliate.id}/address`, {
            addresses: this.addresses.map(o => o.id)
            })
          } else {
            // Edit affiliate
            await axios.patch(`affiliate/${this.affiliate.id}`, this.affiliate)
            await axios.patch(`affiliate/${this.affiliate.id}/address`, {
              addresses: this.addresses.map(o => o.id)
            })
            if (this.spouse.id)
            {
              await axios.patch(`spouse/${this.spouse.id}`, this.spouse)
            }
            else{
              this.spouse.affiliate_id=this.affiliate.id
              await axios.post(`spouse`, this.spouse)
            }
            this.editable = false
          }
        this.toastr.success('Registro guardado correctamente')
        this.editable = false
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
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
    async getAffiliate(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}`)
        this.affiliate = res.data
        this.setBreadcrumbs()
        if (this.affiliate.dead) {
          this.getSpouse(this.$route.params.id)
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getSpouse(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/spouse`)
        this.spouse = res.data
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
  },
}
</script>
