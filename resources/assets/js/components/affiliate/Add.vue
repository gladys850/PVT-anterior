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
              small
              color="success"
              bottom
              right
              absolute
              v-on="on"
              style="margin-right: -9px;"
              :to="{ name: 'affiliateAdd', params: { id:'new'} }"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </template>
          <span>Activar edición</span>
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
        >
          <v-icon v-if="icons">mdi-account-heart</v-icon>
        </v-tab>
            <v-tab
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
            <v-card-text><Profile/></v-card-text>
          </v-card>
        </v-tab-item>
          <v-tab-item
          :value="'tab-3'"
        >
          <v-card flat tile >
            <v-card-text><PoliceData/></v-card-text>
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
          <v-card-text><Fingerprint/></v-card-text>
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

export default {
  name: "affiliate-index",
  components: {
    Breadcrumbs,
    Profile,
    PoliceData,
    Spouse,
    Fingerprint
  },
  data () {
    return {
    affiliate:{
      first_name:null
      },
      tab: null,
      text: 'hola',
      text4: 'huella',
      icons: true,
      vertical: true,
      tabs: 3
    }
  },
  beforeMount() {
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Afiliados',
        to: { name: 'affiliateIndex' }
      }, {
        text: 'Nuevo',
        to: { name: 'affiliateAdd', params: { id:'new'} }
      }
    ])
  },
  mounted() {
    if (this.$route.params.id != 'new') {
      this.getAffiliate(this.$route.params.id)
    }
  },
  methods: {
  async getAffiliate(id) {
    try {
      this.loading = true
      let res = await axios.get(`affiliate/${id}`)
      this.affiliate = res.data
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
  }
}
}
</script>