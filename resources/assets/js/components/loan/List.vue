<template>
  <v-data-table
    :headers="headers"
    :items="loans"
    :loading="loading"
    :options.sync="options"
    :server-items-length="totalloans"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
    multi-sort
    single-expand
  >
    <template v-slot:item="props">
      <tr :class="props.isExpanded ? 'secondary white--text' : ''">
        <td class="text-center">{{ props.item.id }}</td>
        <td class="text-center">{{ props.item.request_date | date }}</td>
        <td class="text-center">{{ props.item.loan_term }}</td>
        <td class="text-center">{{ props.item.disbursement_date | date }}</td>
        <td class="text-right">{{ props.item.balance | money }}</td>
        <td class="text-right">{{ props.item.estimated_quota | money }}</td>
        <td class="text-right">{{ props.item.amount_request | money }}</td>
        <td class="text-right">{{ props.item.amount_disbursement | money }}</td>
        <td class="text-center">
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                icon
                x-small
                color="info"
                v-on="on"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
            </template>
            <span class="caption">Editar</span>
          </v-tooltip>
        </td>
      </tr>
    </template>
  </v-data-table>
</template>

<script>
export default {
  name: 'loan-list',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: true,
    search: '',
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['request_date'],
      sortDesc: [true]
    },
    loans: [],
    selectedLoan: 0,
    totalloans: 0,
    headers: [
      {
        text: 'Correlativo',
        value: 'id',
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
        text: 'Meses plazo',
        value: 'loan_term',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Fecha de desembolso',
        value: 'disbursement_date',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Saldo capital [Bs]',
        value: 'balance',
        class: ['normal', 'white--text'],
        align: 'right',
        sortable: true
      }, {
        text: 'Cuota [Bs]',
        value: 'estimated_quota',
        class: ['normal', 'white--text'],
        align: 'right',
        sortable: true
      }, {
        text: 'Solicitado [Bs]',
        value: 'amount_request',
        class: ['normal', 'white--text'],
        align: 'right',
        sortable: true
      }, {
        text: 'Desembolso [Bs]',
        value: 'amount_disbursement',
        class: ['normal', 'white--text'],
        align: 'right',
        sortable: true
      }, {
        text: 'Acciones',
        class: ['normal', 'white--text'],
        width: '4%',
        align: 'center',
        sortable: false
      }
    ]
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getloans()
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.options.page = 1
        this.getloans()
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
    this.getloans()
  },
  methods: {
    async getloans(params) {
      try {
        this.loading = true
        let res = await axios.get(`loan`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }
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
