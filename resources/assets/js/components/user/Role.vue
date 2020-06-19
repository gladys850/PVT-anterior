<template>
  <div>
    <v-tabs
      v-model="selectedModule"
      vertical
    >
      <v-tab
        v-for="module in modules"
        :key="module.id"
        class="grey lighten-5"
      >
        {{ module.display_name }}
      </v-tab>
      <v-tabs-items
        v-model="selectedModule"
        class="px-5"
      >
        <v-tab-item
          v-for="module in modules"
          :key="module.id"
        >
          <v-list
            dense
            v-if="!loading"
          >
            <v-subheader v-if="modules.length > 0">
              <span v-if="filteredRoles.length == 0">
                No se encontraron roles para el módulo
              </span>
              <span v-else>
                Roles para el módulo
              </span>
              <span class="font-weight-bold">
                &nbsp;{{ modules[selectedModule].name }}
              </span>
            </v-subheader>
            <v-row no-gutters>
              <template v-for="rolesColumn in slicedRoles">
                <v-col :key="filteredRolesIndex(rolesColumn)">
                  <div
                    v-for="role in rolesColumn"
                    :key="role.id"
                    class="my-3"
                  >
                    <v-hover v-slot:default="{ hover }">
                      <v-chip
                        :class="hover ? 'elevation-4' : 'elevation-0'"
                        :color="selectedRoles.includes(role.id) ? 'info' : 'secondary'"
                        dark
                        style="width: 230px;"
                        :outlined="!selectedRoles.includes(role.id)"
                        @click.stop="switchRole(role.id)"
                      >
                        <v-avatar left v-if="selectedRoles.includes(role.id)">
                          <v-icon>mdi-checkbox-marked-circle</v-icon>
                        </v-avatar>
                        {{ role.display_name }}
                      </v-chip>
                    </v-hover>
                  </div>
                </v-col>
              </template>
              <template v-for="(n, index) in (slicedRoles.length == 1) ? 2 : ((slicedRoles.length == 2) ? 1 : 0)">
                <v-col :key="index"></v-col>
              </template>
            </v-row>
            <v-row>
              <v-pagination
                v-model="options.page"
                :length="options.lastPage"
                :total-visible="8"
                color="secondary"
              ></v-pagination>
            </v-row>
          </v-list>
          <Loading v-else/>
        </v-tab-item>
      </v-tabs-items>
    </v-tabs>
  </div>
</template>

<script>
import Loading from '@/components/shared/Loading'
import Module from '@/services/ModuleService'

export default {
  name: 'user-role',
  components: {
    Loading
  },
  props: {
    user: {
      type: Number,
      default: 0
    }
  },
  data: () => ({
    loading: true,
    modules: [],
    roles: [],
    selectedModule: null,
    selectedRoles: [],
    filteredRoles: [],
    options: {
      page: 1,
      lastPage: 1,
      itemsPerPage: 18
    }
  }),
  computed: {
    slicedRoles() {
      return _.chunk(this.filteredRoles.slice((this.options.page - 1) * 18,  this.options.page * 18), 6)
    }
  },
  watch: {
    user(val) {
      if (val != 0) this.getUserRoles(val)
      if (this.modules.length > 0) this.selectedModule = 0
    },
    selectedModule(newVal, oldVal) {
      if (newVal != oldVal) {
        this.filteredRoles = this.$store.getters.roles.filter(o => o.module_id === this.modules[newVal].id)
        this.options.page = 1
        this.options.lastPage = Math.ceil(this.filteredRoles.length / 18)
      }
    }
  },
  mounted() {
    this.getModules()
    if (this.user != 0) this.getUserRoles(this.user)
  },
  methods: {
    filteredRolesIndex(filteredRoles) {
      return filteredRoles.reduce((acc, item) => {
        return acc + item.id
      }, Math.random() * (100 - 1000) + 100)
    },
    async switchRole(id) {
      try {
        this.loading = true
        if (this.selectedRoles.includes(id)) {
          this.selectedRoles = this.selectedRoles.filter(o => o != id)
        } else {
          this.selectedRoles.push(id)
        }
        let res = await axios.patch(`user/${this.user}/role`, {
          roles: this.selectedRoles
        })
        this.selectedRoles = res.data.roles
        this.toastr.success('Actualizado correctamente')
      } catch (e) {
        console.log(e)
        this.selectedRoles = this.selectedRoles.filter(o => o != id)
      } finally {
        this.loading = false
      }
    },
    getModules() {
        this.loading = true
        const module = new Module()
        module.get(null, {
          page: 1,
          per_page: 100,
          sortBy: ['description'],
          sortDesc: [false]
        }).then(res => {
          this.modules = res.data
          if (res.data.length > 0) this.selectedModule = 0
        }).catch(e => {
          console.log(e)
        }).finally(() => {
          this.loading = false
        })
    },
    async getUserRoles(id) {
      try {
        this.loading = true
        let res = await axios.get(`user/${id}/role`)
        this.selectedRoles = res.data.roles
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>