<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Flujos de trabajo de trámites</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-card>
        <v-card-title>
          <v-row align="center" no-gutters>
            <v-col cols="12" md="6">
              <v-select
                v-model="selectedModule"
                :items="modules"
                label="Módulo"
                item-text="description"
                item-value="id"
                :loading="loading"
                prepend-inner-icon="mdi-format-list-checks"
                class="mx-3"
                dense
                flat
                outlined
                shaped
                solo
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="selectedProcedureType"
                :items="procedureTypes"
                label="Tipo de trámite"
                item-text="name"
                item-value="id"
                :loading="loading"
                prepend-inner-icon="mdi-folder-text"
                class="mx-3"
                :disabled="!selectedModule"
                dense
                flat
                outlined
                shaped
                solo
              ></v-select>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text v-if="selectedProcedureType">
          <v-row class="px-5 pb-3">
            <div class="title">
              <span>Flujo de trabajo para los trámites de tipo </span>
              <span class="font-weight-black">{{ procedureTypes.find(o => o.id == selectedProcedureType).name }}</span>
            </div>
            <v-spacer></v-spacer>
            <div v-if="$store.getters.permissions.includes('update-setting')">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    x-small
                    color="info"
                    v-on="on"
                    @click.stop="addSequence"
                    class="mr-3"
                    :disabled="emptySequence"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Añadir secuencia</span>
                </div>
              </v-tooltip>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    small
                    color="success"
                    v-on="on"
                    :disabled="emptySequence || !workflow.length"
                    @click.stop="saveWorkflow"
                  >
                    <v-icon>mdi-check</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Guardar</span>
                </div>
              </v-tooltip>
            </div>
          </v-row>
          <v-simple-table
            v-if="roles.length"
            fixed-header
            height="450px"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Desde</th>
                  <th></th>
                  <th class="text-center">Hacia</th>
                  <th class="text-center" v-if="$store.getters.permissions.includes('update-setting')">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(sequence, index) in workflow"
                  :key="index"
                  :class="(!sequence.role_id || !sequence.next_role_id) ? 'empty' : ''"
                >
                  <td class="text-center">{{ index + 1 }}</td>
                  <td>
                    <v-select
                      :readonly="!$store.getters.permissions.includes('update-setting')"
                      class="pt-5"
                      :items="filteredRoles(sequence, 'role_id')"
                      item-text="display_name"
                      item-value="id"
                      v-model="sequence.role_id"
                      outlined
                      dense
                    ></v-select>
                  </td>
                  <td class="text-center"><v-icon>mdi-arrow-right-bold-outline</v-icon></td>
                  <td>
                    <v-select
                      :readonly="!$store.getters.permissions.includes('update-setting')"
                      class="pt-5"
                      :items="filteredRoles(sequence, 'next_role_id')"
                      item-text="display_name"
                      item-value="id"
                      v-model="sequence.next_role_id"
                      outlined
                      dense
                    ></v-select>
                  </td>
                  <td class="text-center" v-if="$store.getters.permissions.includes('update-setting')">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          icon
                          color="error"
                          v-on="on"
                          @click.stop="workflow.splice(index, 1)"
                        >
                          <v-icon>mdi-minus-circle</v-icon>
                        </v-btn>
                      </template>
                      <div>
                        <span>Eliminar secuencia</span>
                      </div>
                    </v-tooltip>
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "procedure-type-workflow",
  data: () => ({
    loading: true,
    roles: [],
    modules: [],
    procedureTypes: [],
    selectedModule: null,
    selectedProcedureType: null,
    workflow: []
  }),
  watch: {
    selectedModule(newVal, oldVal) {
      if (newVal != oldVal && newVal != null) {
        this.selectedProcedureType = null
        this.getModuleRoles(newVal)
        this.getModuleProcedureTypes(newVal)
      }
    },
    selectedProcedureType(newVal, oldVal) {
      if (!newVal) this.workflow = []
      if (newVal != oldVal && newVal != null) {
        this.getWorkflow(newVal)
      }
    }
  },
  computed: {
    emptySequence() {
      return !!this.workflow.filter(o => o.role_id == null || o.next_role_id == null).length
    }
  },
  mounted() {
    this.getModules()
  },
  methods: {
    filteredRoles(sequence, type) {
      let roles
      if (type == 'role_id') {
        roles = this.roles
        let usedRoles = this.workflow.filter(o => o.next_role_id == sequence.next_role_id).map(o => o['role_id'])
        if (sequence.role_id) usedRoles = usedRoles.filter(o => o != sequence.role_id)
        if (sequence.next_role_id) {
          roles = roles.filter(o => o.id != sequence.next_role_id && o.sequence_number < this.roles.find(o => o.id == sequence.next_role_id).sequence_number)
        }
        roles = roles.filter(o => usedRoles.indexOf(o.id) == -1)
      } else if (type == 'next_role_id') {
        roles = this.roles
        let usedRoles = this.workflow.filter(o => o.role_id == sequence.role_id).map(o => o['next_role_id'])
        if (sequence.next_role_id) {
          usedRoles = usedRoles.filter(o => o != sequence.next_role_id)
        } else {
          roles = this.roles
        }
        if (sequence.role_id) {
          roles = roles.filter(o => o.id != sequence.role_id && o.sequence_number > this.roles.find(o => o.id == sequence.role_id).sequence_number)
        }
        roles = roles.filter(o => usedRoles.indexOf(o.id) == -1)
      }
      return roles
    },
    addSequence() {
      if (!this.emptySequence) {
        this.workflow.push({
          role_id:null,
          next_role_id:null
        })
        this.$nextTick(function() {
          document.querySelector('.empty').scrollIntoView({
            behavior: 'smooth'
          })
        })
      }
    },
    async saveWorkflow() {
      try {
        this.loading = true
        let res = await axios.patch(`procedure_type/${this.selectedProcedureType}/flow`, {
          workflow: this.workflow
        })
        this.workflow = res.data
        this.toastr.success('Flujo modificado')
      } catch (e) {
        console.log(e)
        this.toastr.error('Error al guardar el flujo')
      } finally {
        this.loading = false
      }
    },
    async getWorkflow(id) {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${id}/flow`)
        this.workflow = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getModuleRoles(id) {
      try {
        this.loading = true
        let res = await axios.get(`module/${id}/role`)
        this.roles = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getModuleProcedureTypes(id) {
      try {
        this.loading = true
        let res = await axios.get(`module/${id}/procedure_type`)
        this.procedureTypes = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getModules() {
      try {
        this.loading = true
        let res = await axios.get(`module`, {
          params: {
            sortBy: ['name'],
            sortDesc: ['false'],
            per_page: 100,
            page: 1
          }
        })
        this.modules = res.data.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
};
</script>
