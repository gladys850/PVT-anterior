<template>
  <div>
    <v-tabs
      v-model="selectedModule"
      vertical
      v-if="!loading"
    >
      <v-tab
        v-for="module in modules"
        :key="module.id"
        class="grey lighten-5"
      >
        {{ module.name }}
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
            flat
            shaped
          >
            <v-row no-gutters>
              <template v-for="(rolesColumn, index) in filteredRoles">
                <v-col :key="index">
                  <v-list-item-group
                    multiple
                  >
                    <v-list-item
                      v-for="role in rolesColumn"
                      :key="role.id"
                      :class="selectedRoles.includes(role.id) ? `info mx-5 my-2` : ``"
                      @click.stop="switchRole(role.id)"
                    >
                      <v-list-item-content :class="selectedRoles.includes(role.id) ? `white--text` : ``">
                        {{ role.name }}
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-col>
              </template>
              <template v-for="n in (filteredRoles.length == 1) ? 2 : ((filteredRoles.length == 2) ? 1 : 0)">
                <v-col :key="n"></v-col>
              </template>
            </v-row>
          </v-list>
        </v-tab-item>
      </v-tabs-items>
    </v-tabs>
    <Loading v-else/>
  </div>
</template>

<script>
import Loading from '@/components/shared/Loading'

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
    filteredRoles: []
  }),
  watch: {
    user: function(val) {
      if (val != 0) this.getUserRoles(val)
      if (this.modules.length > 0) this.selectedModule = 0
    },
    selectedModule: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.filteredRoles = _.chunk(this.roles.filter(o => o.module_id == this.modules[newVal].id), 8)
      }
    }
  },
  mounted() {
    this.getRoles()
    if (this.user != 0) this.getUserRoles(this.user)
  },
  methods: {
    async switchRole(id) {
      try {
        this.loading = true
        if (this.selectedRoles.includes(id)) {
          this.selectedRoles = this.selectedRoles.filter(o => o != id)
        } else {
          this.selectedRoles.push(id)
        }
        let res = await axios.post(`user/${this.user}/role`, {
          roles: this.selectedRoles
        })
        this.selectedRoles = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getModules() {
      try {
        this.loading = true
        let res = await axios.get(`module`)
        this.modules = res.data
        if (this.modules.length > 0) this.selectedModule = 0
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getRoles() {
      try {
        this.loading = true
        let res = await axios.get(`role`)
        this.roles = res.data
        this.getModules()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getUserRoles(id) {
      try {
        this.loading = true
        let res = await axios.get(`user/${id}/role`)
        this.selectedRoles = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>