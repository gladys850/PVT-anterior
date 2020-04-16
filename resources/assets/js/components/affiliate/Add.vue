<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary" style="z-index: 1;">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn
              fab
              dark
              x-small
              :color="'error'"
              bottom
              right
              absolute
              v-on="on"
              style="margin-right: 45px;"
              @click.stop="resetForm()"
              v-show="!isNew && editable"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <div>
            <span>Cancelar</span>
          </div>
        </v-tooltip>
        <v-tooltip top v-if="tab != 'tab-1'">
          <template v-slot:activator="{ on }">
            <v-btn
              fab
              dark
              small
              :color="editable ? 'danger' : 'success'"
              bottom
              right
              absolute
              v-on="on"
              style="margin-right: -9px;"
              @click.stop="saveAffiliate()"
            >
              <v-icon v-if="editable">mdi-check</v-icon>
              <v-icon v-else>mdi-pencil</v-icon>
            </v-btn>
          </template>
          <div>
            <span v-if="editable">Guardar</span>
            <span v-else>Editar</span>
          </div>
        </v-tooltip>
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
          <v-icon v-if="icons">mdi-account</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-3`"
        >
          <v-icon v-if="icons">mdi-police-badge</v-icon>
        </v-tab>
        <v-tab
          :href="`#tab-4`"
          v-show="!isNew"
        >
          <v-icon v-if="icons">mdi-account-heart</v-icon>
        </v-tab>
        <v-tab
          v-show="!isNew"
          :href="`#tab-5`"
        >
          <v-icon v-if="icons">mdi-fingerprint</v-icon>
        </v-tab>

        <v-tab
          v-show="!isNew"
          :href="`#tab-6`"
        >
          <v-icon v-if="icons">mdi-file</v-icon>
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
          :value="'tab-3'"
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
          :value="'tab-4'"
        >
          <v-card flat tile >
            <v-card-text>
              <Spouse
                v-if="!reload"
                :spouse.sync="spouse"
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
            <Fingerprint
              :permission="permission"
              :affiliate.sync="affiliate"
              :editable.sync="editable"
            /></v-card-text>
          </v-card>
        </v-tab-item>
        <v-tab-item
          :value="'tab-6'"
        >
          <v-card flat tile >
          <v-card-text>
            <Document
              :permission="permission"
              :affiliate.sync="affiliate"
              :editable.sync="editable"
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
import PoliceData from '@/components/affiliate/PoliceData'
import Spouse from '@/components/affiliate/Spouse'
import Fingerprint from '@/components/affiliate/Fingerprint'
import Document from '@/components/affiliate/Document'
import Dashboard from '@/components/affiliate/Dashboard'

export default {
  name: "affiliate-index",
  components: {
    Breadcrumbs,
    Profile,
    PoliceData,
    Spouse,
    Fingerprint,
    Document,
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
            let res = await axios.post(`affiliate`, this.affiliate)
            this.toastr.success('Afiliado adicionado')
            //Actualizar dirección,  obteniendo respuesta POST afiliado nuevo (res.data.id)
            await axios.patch(`affiliate/${res.data.id}/address`, {
            addresses: this.addresses.map(o => o.id)
            })
          } else {
            // Edit affiliate
            await axios.patch(`affiliate/${this.affiliate.id}`, this.affiliate)
            await axios.patch(`affiliate/${this.affiliate.id}/address`, {
              addresses: this.addresses.map(o => o.id)
            })
            //Preguntar si afiliado esta fallecido
            if((this.affiliate.date_death != null && this.affiliate.date_death != '') || 
                (this.affiliate.reason_death != null && this.affiliate.reason_death != '')){
              if(this.spouse.id){
                await axios.patch(`spouse/${this.spouse.id}`, this.spouse)
              }else{
                this.spouse.affiliate_id=this.affiliate.id
                await axios.post(`spouse`, this.spouse)
              }
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
          text: 'Afiliados',
          to: { name: 'affiliateIndex' }
        }
      ]
      if (this.isNew) {
        breadcrumbs.push({
          text: 'Nuevo Afiliado',
          to: { name: 'affiliateAdd', params: { id: 'new' } }
        })
      } else {
        breadcrumbs.push({
          text: this.$options.filters.fullName(this.affiliate, true),
          to: { name: 'affiliateAdd', params: { id: this.affiliate.id } }
        })
      }
      this.$store.commit('setBreadcrumbs', breadcrumbs)
    },
    async getAffiliate(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}`)
        this.affiliate = res.data
        this.setBreadcrumbs()
        if (this.affiliate.dead) {
          this.getSpouse(this.affiliate.id)
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
