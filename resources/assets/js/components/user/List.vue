<template>
  <v-data-table
    :headers="headers"
    :items="users"
    :loading="loading"
    :options.sync="options"
    :server-items-length="totalUsers"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
    multi-sort
    single-expand
  >
    <template v-slot:item="props">
      <tr :class="props.isExpanded ? 'secondary white--text' : ''">
        <td @click.stop="props.expand(!props.isExpanded )">ssdfs</td>

        <td >
          <v-tooltip top v-if="$store.getters.permissions.includes('update-user')">
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                dark
                x-small
                color="warning"
                v-on="on"
         
              >
                <v-icon>mdi-cancel</v-icon>
              </v-btn>
            </template>
            <span class="caption">Deshabilitar</span>
          </v-tooltip>
        </td>

      </tr>
    </template>
    <template v-slot:expanded-item="{ headers }">
      <tr>
        <td :colspan="headers.length" class="px-0">
          fdfgdfgdf
        </td>
      </tr>
    </template>
  </v-data-table>
</template>

<script>
import Role from '@/components/user/Role'

export default {
  name: 'user-list',
  components: {
    Role
  },

  data: () => ({
    loading: true,
    search: '',
    active: true,
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['last_name'],
      sortDesc: [false]
    },
    users: [],
    selectedUser: 0,
    totalUsers: 0,
    headers: [
      {
        text: 'Apellido',
        value: 'last_name',
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: true
      }, {
        text: 'Nombre',
        value: 'first_name',
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: true
      }, {
        text: 'Cargo',
        value: 'position',
        class: ['normal', 'white--text'],
        width: '50%',
        sortable: true
      }, {
        text: 'Usuario',
        value: 'username',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: true
      }, {
        text: 'Acciones',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      }
    ]
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getUsers()
      }
    },

  },
  mounted() {

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
            sortDesc: this.options.sortDesc,
            active: this.active,
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
