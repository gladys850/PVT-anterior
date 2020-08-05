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
        <div>
          <StepsTreasury/>
        </div>
      </v-container>
    </template>
  </v-card>
</template>

<script>
import StepsTreasury from '@/components/payment/StepsTreasury'
import Breadcrumbs from '@/components/shared/Breadcrumbs'

export default {
  name: "loan-add",
  components: {
    StepsTreasury,
    Breadcrumbs
  },
  data: () => ({
    affiliate:{
      phone_number:null,
      cell_phone_number:null
    },
    loan:{},
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
        text: 'Nuevo Cobro',
        to: { name: 'flowIndex' }
      }
    ])
  },
  mounted() {
    this.getLoan(this.$route.query.loan_id);
  },
  methods:{
     async getLoan(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}`);
        this.loan = res.data;
        console.log('esta sacando el loan')
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
  setBreadcrumbs() {
    let breadcrumbs = [
      {
        text: 'Cobros',
        to: { name: 'flowIndex' }
      }
    ]
    if (this.isNew) {
      breadcrumbs.push({
        text: 'Nuevo Cobro',
        to: { name: 'flowIndex', params: { id: 'new' } }
      })
      } else {
      breadcrumbs.push({
        text: this.$options.filters.fullName(this.affiliate, true),
        to: { name: 'flowIndex', params: { id: this.affiliate.id } }
      })
    }
    this.$store.commit('setBreadcrumbs', breadcrumbs)
  }
  }
}
</script>