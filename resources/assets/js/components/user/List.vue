<template>
  <v-data-table
    :headers="headers"
    :items="users"
    :loading="loading"
    :options.sync="options"
    :server-items-length="totalUsers"
    :footer-props="{ itemsPerPageOptions: [10, 20, 30] }"
    :fixed-header="true"
    calculate-widths
  >
    <template v-slot:items="props">
      <td class="text-xs-left">{{ props.item.username }}</td>
      <td class="text-xs-left">{{ props.item.first_name }}</td>
      <td class="text-xs-left">{{ props.item.last_name }}</td>
      <td class="text-xs-left">{{ props.item.position }}</td>
    </template>
  </v-data-table>
</template>

<script>
export default {
  name: 'users-list',
  props: ['bus'],
  data: () => ({
    loading: true,
    search: '',
    status: 'active',
    options: {
      page: 1,
      itemsPerPage: 10,
      sortBy: null,
      descending: false
    },
    users: [],
    totalUsers: 0,
    headers: [
      {
        text: 'Usuario',
        value: 'username',
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: false
      }, {
        text: 'Nombre',
        value: 'first_name',
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: false
      }, {
        text: 'Apellido',
        value: 'last_name',
        class: ['normal', 'white--text'],
        width: '20%',
        sortable: false
      }, {
        text: 'Cargo',
        value: 'position',
        class: ['normal', 'white--text'],
        width: '50%',
        sortable: false
      }
    ]
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.descending != oldVal.descending) {
        this.getUsers()
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.getUsers()
      }
    },
    status: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.getUsers()
      }
    }
  },
  mounted() {
    this.bus.$on('search', val => {
      this.search = val
    })
    this.bus.$on('status', val => {
      this.status = val
    })
    this.getUsers()
  },
  methods: {
    async getUsers(params) {
      try {
        this.loading = true
        let res = await axios.get(`user`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            direction: this.options.descending ? 'desc' : 'asc',
            status: this.status,
            search: this.search
          }
        })
        this.users = res.data.data
        this.totalUsers = res.data.total
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

