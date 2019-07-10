<template>
  <v-container fluid>
    <v-toolbar>
      <v-toolbar-title v-if="$store.getters.ldapAuth">
        <v-select
          :items="['Usuarios', 'LDAP']"
          v-model="viewType"
          class="title font-weight-medium"
        ></v-select>
      </v-toolbar-title>
      <v-toolbar-title v-else>Usuarios</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-card>
      <v-card-text>
        <v-data-table
          :headers="headers"
          :items="users"
          :loading="loading"
          :pagination.sync="pagination"
          :total-items="totalUsers"
          :rows-per-page-items="[10,20,30]"
        >
          <template v-slot:items="props">
            <td class="text-xs-left">{{ props.item.id }}</td>
            <td class="text-xs-left">{{ props.item.username }}</td>
            <td class="text-xs-left">{{ props.item.first_name }}</td>
            <td class="text-xs-left">{{ props.item.last_name }}</td>
            <td class="text-xs-left">{{ props.item.position }}</td>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
import { timingSafeEqual } from 'crypto';

export default {
  name: "userIndex",
  data: () => ({
    viewType: 'Usuarios',
    loading: true,
    search: null,
    status: 'active',
    pagination: {
      page: 1,
      rowsPerPage: 10,
      sortBy: null,
      descending: false
    },
    users: [],
    totalUsers: 0,
    headers: [
      { text: 'ID', value: 'id', sortable: true },
      { text: 'Usuario', value: 'username', sortable: false },
      { text: 'Nombre', value: 'first_name', sortable: false },
      { text: 'Apellido', value: 'last_name', sortable: false },
      { text: 'Cargo', value: 'position', sortable: false }
    ]
  }),
  watch: {
    pagination: function(newVal, oldVal) {
      console.log(newVal)
      console.log(oldVal)

      if (newVal.page != oldVal.page || newVal.rowsPerPage != oldVal.rowsPerPage || newVal.sortBy != oldVal.sortBy || newVal.descending != oldVal.descending) {
        this.getUsers()
      }
    }
  },
  mounted() {
    this.getUsers();
  },
  methods: {
    async getUsers(params) {
      try {
        let res = await axios.get(`user`, {
          params: {
            page: this.pagination.page,
            per_page: this.pagination.rowsPerPage,
            sortBy: this.pagination.sortBy,
            direction: this.pagination.descending ? 'desc' : 'asc',
            status: this.status
          }
        })
        this.users = res.data.data
        this.totalUsers = res.data.total
        delete res.data['data']
        this.pagination.page = res.data.current_page
        this.pagination.rowsPerPage = parseInt(res.data.per_page)
        this.pagination.totalItems = res.data.total
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
