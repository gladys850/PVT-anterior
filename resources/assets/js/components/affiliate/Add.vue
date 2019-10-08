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
        <v-tooltip top>
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

        <v-tab-item
        :value="'tab-1'"
      >
        <v-card flat tile >
          <v-card-text>{{affiliate.first_name}}</v-card-text>
        </v-card>
      </v-tab-item>
        <v-tab-item
          :value="'tab-2'"
        >
          <v-card flat tile >
<<<<<<< HEAD
            <v-card-text><Profile :affiliate.sync="affiliate"/></v-card-text>
=======
            <v-card-text><Profile :editable.sync="editable"/></v-card-text>
>>>>>>> d00acf43f41174f2c4dbd7c84d95eefb8d44cb5f
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-3'"
        >
          <v-card flat tile >
<<<<<<< HEAD
            <v-card-text><PoliceData :affiliate.sync="affiliate"/></v-card-text>
=======
            <v-card-text><PoliceData :editable.sync="editable"/></v-card-text>
>>>>>>> d00acf43f41174f2c4dbd7c84d95eefb8d44cb5f
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-4'"
        >
          <v-card flat tile >
            <v-card-text><Spouse/></v-card-text>
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-5'"
        >
          <v-card flat tile >
          <v-card-text><Fingerprint :affiliate.sync="affiliate" :editable.sync="editable"/></v-card-text>
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
import { log } from 'util'

export default {
  name: "affiliate-index",
  components: {
    Breadcrumbs,
    Profile,
    PoliceData,
    Spouse,
    Fingerprint
  },
  data: () => ({
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
      date_derelict:null
    },
    tab: null,
    text: 'hola',
    text4: 'huella',
    icons: true,
    vertical: true,
    tabs: 3,
    editable: false
  }),
  computed: {
    isNew() {
      return this.$route.params.id == 'new'
    }
  },
  mounted() {
    if (!this.isNew) {
      this.resetForm()
    } else {
      this.setBreadcrumbs()
    }
  },
  methods: {
    resetForm() {
      this.getAffiliate(this.$route.params.id)
      this.editable = false
    },
    async saveAffiliate() {
      try {
        if (!this.editable) {
          this.editable = true
        } else {
          if (this.isNew) {
            // New affiliate
          } else {
            // Edit affiliate
          }
          this.toast('Registro guardado correctamente', 'success')
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
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  },
  async saveAffiliate() {
    try {
      if (await this.$validator.validateAll()) {
        this.loading = true
        if (this.$route.params.id != 'new') {
          let res = await axios.patch(`affiliate/${this.affiliate.id}`, this.affiliate)
          console.log(res.data)
          this.$router.push({
          name: "affiliateIndex"
          });
          this.toast('Afiliado modificado', 'success')
        } else {
          let res = await axios.post(`affiliate`, this.affiliate)
          this.toast('Afiliado adicionado', 'success')
          this.$router.push({
          name: "affiliateIndex"
          });console.log(res.data)
        }
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
    }
}
</script>