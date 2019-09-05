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
        <td @click.stop="getRoles(props)">{{ props.item.username }}</td>
        <td @click.stop="getRoles(props)">{{ props.item.first_name }}</td>
        <td @click.stop="getRoles(props)">{{ props.item.last_name }}</td>
        <td @click.stop="getRoles(props)">{{ props.item.position }}</td>
        <td>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn fab dark x-small color="error" v-on="on" @click.stop="bus.$emit('openRemoveDialog', props.item.id)">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
            <span class="caption">Eliminar</span>
          </v-tooltip>
        </td>
      </tr>
    </template>
    <template v-slot:expanded-item="{ headers }">
      <tr>
        <td :colspan="headers.length" class="tertiary">
          123
        </td>
      </tr>
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
      itemsPerPage: 8,
      sortBy: ['username'],
      sortDesc: [false]
    },
    users: [],
    totalUsers: 0,
    headers: [
      {
        text: 'Usuario',
        value: 'username',
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
        text: 'Apellido',
        value: 'last_name',
        class: ['normal', 'white--text'],
        width: '20%',
        sortable: true
      }, {
        text: 'Cargo',
        value: 'position',
        class: ['normal', 'white--text'],
        width: '45%',
        sortable: true
      }, {
        text: 'Acciones',
        class: ['normal', 'white--text'],
        width: '5%',
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
    async getRoles(props) {
      props.expand(!props.isExpanded)
    },
    async getUsers(params) {
      try {
        this.loading = true
        let res = await axios.get(`user`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
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

