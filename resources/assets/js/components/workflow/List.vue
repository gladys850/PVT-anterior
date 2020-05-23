<template>
  <v-data-table
    v-model="selectedLoans"
    :headers="headers"
    :items="loans"
    :loading="loading"
    :options.sync="options"
    :server-items-length="totalloans"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
    multi-sort
    :show-select="allowFlow"
  >
    <template v-slot:header.data-table-select="{ on, props }">
      <v-simple-checkbox color="info" class="grey lighten-3" v-bind="props" v-on="on"></v-simple-checkbox>
    </template>
    <template v-slot:item.data-table-select="{ isSelected, select }">
      <v-simple-checkbox color="success" :value="isSelected" @input="select($event)"></v-simple-checkbox>
    </template>
    <template v-slot:item.role_id="{ item }">
      {{ $store.getters.roles.find(o => o.id == item.role_id).display_name }}
    </template>
    <template v-slot:item.request_date="{ item }">
      {{ item.request_date | date }}
    </template>
    <template v-slot:item.amount_requested="{ item }">
      {{ item.amount_requested | money }}
    </template>
    <template v-slot:item.balance="{ item }">
      {{ item.balance | money }}
    </template>
    <template v-slot:item.estimated_quota="{ item }">
      {{ item.estimated_quota | money }}
    </template>
    <template v-slot:item.actions="{ item }">
      <v-menu
        offset-y
        close-on-content-click
      >
        <template v-slot:activator="{ on }">
          <v-btn
            icon
            small
            color="primary"
            v-on="on"
          >
            <v-icon>mdi-printer</v-icon>
          </v-btn>
        </template>
        <v-list
        class="py-0">
          <v-list-item
            class="py-0"
            v-for="(option, index) in [{title:'Contrato'},{title:'Formulario'}]"
            :key="index"
          >
            <v-list-item-title class="py-0">
              <v-btn text 
                @click="imprimir(index,item.id)" >
                <v-icon v-show="index==0"> mdi-file-account-outline</v-icon> 
                <v-icon v-show="index==1"> mdi-file-document-outline</v-icon> 
                {{ item.title }}
              </v-btn>
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
  </v-data-table>
</template>

<script>
export default {
  name: 'workflow-list',
  props: {
    bus: {
      type: Object,
      required: true
    },
    params: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: true,
    selectedLoans: [],
    search: '',
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['request_date'],
      sortDesc: [true]
    },
    loans: [],
    totalloans: 0,
    headers: [
      {
        text: 'Correlativo',
        value: 'code',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Fecha solicitud',
        value: 'request_date',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Solicitado [Bs]',
        value: 'amount_requested',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Saldo capital [Bs]',
        value: 'balance',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Meses Plazo',
        value: 'loan_term',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Cuota [Bs]',
        value: 'estimated_quota',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Acciones',
        value: 'actions',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }
    ]
  }),
  computed: {
    validOptions() {
      return this.params.procedure_type_id != null && this.params.role_id != null && (this.params.hasOwnProperty('validated') || this.params.hasOwnProperty('trashed'))
    },
    allowFlow() {
      if (this.params.hasOwnProperty('role_id') && this.params.hasOwnProperty('validated')) {
        return (this.params.role_id > 0 && this.params.validated)
      } else {
        return false
      }
    }
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc && this.validOptions) {
        this.getloans()
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal && this.validOptions) {
        this.options.page = 1
        this.getloans()
      }
    },
    selectedLoans(val) {
      this.bus.$emit('selectLoans', this.selectedLoans)
      if (val.length) {
        this.$emit('allowFlow', true)
      } else {
        this.$emit('allowFlow', false)
      }
    },
    params(val) {
      if (this.validOptions) {
        this.selectedLoans = []
        this.getloans()
        this.updateHeader()
      }
    }
  },
  mounted() {
    this.bus.$on('added', val => {
      this.getloans()
    })
    this.bus.$on('removed', val => {
      this.getloans()
    })
    this.bus.$on('search', val => {
      this.search = val
    })
  },
  methods: {
    async imprimir(index,item)
    {
      if(index==0)
      {
         let res = await axios.get(`loan/${item}/print/contract`)
      }
      else{
        let res = await axios.get(`loan/${item}/print/form`)
      } 
    },
    updateHeader() {
      if (this.params.role_id > 0) {
        this.headers = this.headers.filter(o => o.value != 'role_id')
      } else {
        if (!this.headers.some(o => o.value == 'role_id')) {
          this.headers.unshift({
            text: 'Área',
            class: ['normal', 'white--text'],
            align: 'center',
            value: 'role_id',
            sortable: true
          })
        }
      }
    },
    async getloans() {
      try {
        this.loading = true
        let res = await axios.get(`loan`, {
          params: {...{
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }, ...this.params}
        })
        this.loans = res.data.data
        this.totalloans = res.data.total
        delete res.data['data']
        this.options.page = res.data.current_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
        this.options.totalItems = res.data.total
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
<style>
th.text-start {
  background-color: #757575;
}
</style>