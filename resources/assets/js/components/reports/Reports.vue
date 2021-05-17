<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>
          <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular">
              <v-toolbar-title>Reportes</v-toolbar-title>
            </v-toolbar>
          </v-card-title>

        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>


export default {
  name: 'report',
  components: {

  },
  data: () => ({
    

  }),
  computed: {
    //Metodo para obtener Permisos por rol
    permissionSimpleSelected() {
      return this.$store.getters.permissionSimpleSelected;
    },
  },
  watch: {
    

  },
  mounted() {
    
  },
  methods: {
    async getVouchers(params) {
      try {
        this.loading = true
        let res = await axios.get(`voucher`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }
        })
        this.vouchers = res.data.data
        this.totalVouchers = res.data.total
        delete res.data['data']
        this.options.page = res.data.current_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
        this.options.totalItems = res.data.total
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },



  }
}
</script>

