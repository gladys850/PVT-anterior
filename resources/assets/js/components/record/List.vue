<template>
  <v-data-table
    :headers="headers"
    :items="records"
    :loading="loading"
    :options.sync="options"
    :server-items-length="totalRecords"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
    multi-sort
    single-expand
  >
    <template v-slot:item="props">
      <tr>
        <td class="text-center">{{ props.item.action }}</td>
        <td class="text-center">{{ props.item.created_at | datetime }}</td>
      </tr>
    </template>
  </v-data-table>
</template>

<script>
export default {
  name: 'record-list',
  props: ['bus'],
  data: () => ({
    loading: true,
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['created_at'],
      sortDesc: [true]
    },
    records: [],
    totalRecords: 0,
    headers: [
      {
        text: 'Actividad',
        value: 'action',
        class: ['normal', 'white--text'],
        width: '70%',
        align: 'center',
        sortable: false
      }, {
        text: 'Fecha',
        value: 'created_at',
        class: ['normal', 'white--text'],
        width: '30%',
        align: 'center',
        sortable: true
      }
    ],
    search: ''
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getRecords()
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.options.page = 1
        this.getRecords()
      }
    }
  },
  beforeMount() {
    Echo.channel('record').listen('.saved', (msg) => {
      this.records.unshift(msg.data)
      this.records.pop()
    })
  },
  mounted() {
    this.bus.$on('search', val => {
      this.search = val
    })
    this.getRecords()
  },
  methods: {
    async getRecords(params) {
      try {
        this.loading = true
        let res = await axios.get(`record`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            active: this.active,
            search: this.search
          }
        })
        this.records = res.data.data
        this.totalRecords = res.data.total
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
