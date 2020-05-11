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
        <td class="text-right">{{ props.item.amount_requested | money }}</td>
        <td class="text-right">{{ props.item.amount_approved | money }}</td>
        <td class="text-center">
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
                v-for="(item, index) in [{title:'Contrato'},{title:'Formulario'}]"
                :key="index"
              >
                <v-list-item-title class="py-0">
                  <v-btn text 
                    @click="imprimir(index,props.item.id)" >
                    <v-icon v-show="index==0"> mdi-file-account-outline</v-icon> 
                    <v-icon v-show="index==1"> mdi-file-document-outline</v-icon> 
                    {{ item.title }}
                  </v-btn>
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                icon
                small
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
        width: '5%',
        sortable: true
      }, {
        text: 'Fecha solicitud',
        value: 'request_date',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '10%',
        sortable: true
      }, {
        text: 'Meses Plazo',
        value: 'loan_term',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '8%',
        sortable: true
      }, {
        text: 'Fecha desembolso',
        value: 'disbursement_date',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '10%',
        sortable: true
      }, {
        text: 'Saldo capital [Bs]',
        value: 'balance',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '8%',
        sortable: false
      }, {
        text: 'Cuota [Bs]',
        value: 'estimated_quota',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '3%',
        sortable: false
      }, {
        text: 'Solicitado [Bs]',
        value: 'amount_requested',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '6%',
        sortable: true
      }, {
        text: 'Desembolso [Bs]',
        value: 'amount_approved',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '6%',
        sortable: true
      }, {
        text: 'Acciones',
        class: ['normal', 'white--text'],
        align: 'center',
        width: '11%',
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
