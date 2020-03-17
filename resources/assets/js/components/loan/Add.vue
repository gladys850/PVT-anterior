<template>
  <v-card flat>
    <v-card-title >
      <v-toolbar  dense color="tertiary" style="z-index: 1;">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
    </v-card-title>
    <template>
      <v-container>
        <div  >
          <v-expansion-panels  :focusable="true">
            <v-expansion-panel
              :expand="true">
              <v-expansion-panel-header>
                {{"TITULAR: "+this.degree_name}} {{this.$options.filters.fullName(this.affiliate, true)}}
                <span style="text-align:center; ">CAT. {{this.category_name}}</span>
                <template v-slot:actions >
                  <v-icon color="teal">mdi-check</v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content class="pa-0 ml-0"  >
                <Steps
                :modalidad.sync="modalidad"
                :affiliate.sync="affiliate"
                :addresses.sync="addresses"/>
              </v-expansion-panel-content>
            </v-expansion-panel>

            <v-expansion-panel v-if="ocultar">
              <v-expansion-panel-header disable-icon-rotate>
                GARANTES
                <template v-slot:actions>
                  <v-icon color="error">mdi-alert-circle</v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <StepsGuarantor/>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </div>
      </v-container>
    </template>
  </v-card>
</template>

<script>
import StepsGuarantor from '@/components/loan/StepsGuarantor'
import Steps from '@/components/loan/Steps'
import Ballots from '@/components/loan/Ballots'
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import { Validator } from 'vee-validate'
export default {
  inject: ['$validator'],
  name: "loan-add",
  components: {
    Steps,
    Ballots,
    StepsGuarantor,
    Breadcrumbs
  },
  data: () => ({
    addresses:[],
    affiliate:{
      phone_number:null,
      cell_phone_number:null
    },
     modalidad:{},
    ocultar:false,
    degree_name: null,
    category_name: null
  }),
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
  },
  beforeMount() {
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Préstamos',
        to: { name: 'loanIndex' }
      }
    ])
  },
  mounted() {
    this.getAffiliate(this.$route.query.affiliate_id)
    this.getAddress(this.$route.query.affiliate_id)
    this.getDegree_name(this.$route.query.affiliate_id)
    this.getCategory_name(this.$route.query.affiliate_id)
  },
  methods:{
    async getAffiliate(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}`)
        this.affiliate = res.data
        console.log(res.data);
        this.setBreadcrumbs()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  setBreadcrumbs() {
    let breadcrumbs = [
      {
        text: 'Préstamos',
        to: { name: 'loanIndex' }
      }
    ]
    if (this.isNew) {
      breadcrumbs.push({
        text: 'Nuevo Préstamo',
        to: { name: 'loanIndex', params: { id: 'new' } }
      })
      } else {
      breadcrumbs.push({
        text: this.$options.filters.fullName(this.affiliate, true),
        to: { name: 'loanIndex', params: { id: this.affiliate.id } }
      })
    }
    this.$store.commit('setBreadcrumbs', breadcrumbs)
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
    async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/degree`)
        this.degree_name = res.data.shortened
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/category`);
        this.category_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>