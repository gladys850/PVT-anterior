<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>
          <Breadcrumbs />
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn-toggle active-class="primary white--text" mandatory v-model="statusLoans" v-if="selectedProcedure > 0">
          <v-btn text :value="false">RECIBIDOS</v-btn>
          <v-btn text :value="true">REVISADOS</v-btn>
        </v-btn-toggle>

        <v-divider class="mx-2" inset vertical></v-divider>
        <v-flex xs3>
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Buscar"
            class="mr-5 pr-5"
            single-line
            hide-details
            clearable
          ></v-text-field>
        </v-flex>
        <!--<Fab v-if="$store.getters.permissions.includes('create-qualification')"/>-->
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <template>
        <v-card>
          <v-row class="ma-0 pa-0 px-2">
            <v-col cols="3" class="ma-0 pa-0 pr-2">
              <v-select
                eager
                label="Trámites"
                v-model="selectedProcedure"
                :items="itemsProcedure"
                item-text="display_name"
                item-value="id"
                prepend-inner-icon="mdi-file-eye"
                :loading="loading"
                outlined
                
              ></v-select>
              {{selectedProcedure}}
            </v-col>
            <!--Mostrar modalidades de trámites-->
            <v-col cols="9" class="ma-0 pa-0" v-if="selectedProcedure > 0">
              <v-tabs v-model="tab" background-color="primary" dark>
                <v-tab v-for="pt in procedureTypes" :key="pt.id" @click.stop="getModality(pt.id)">
                  {{pt.second_name}}
                </v-tab>
              </v-tabs>
            </v-col>
          </v-row>

          <v-tabs-items v-model="tab">
            <v-tab-item eager v-for="pt in procedureTypes" :key="pt.id">
              <v-card flat class="ma-0 pa-0 px-2">
                <List 
                  :bus="bus" 
                  :procedureTypeId="procedureTypeId" 
                  :userRoles="userRoles" 
                  :moduleRoles="moduleRoles" 
                  :selectedProcedure="selectedProcedure" />
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </template>
    </v-card-text>
    <RemoveItem :bus="bus" />
  </v-card>
</template>

<script>
import Breadcrumbs from "@/components/shared/Breadcrumbs"
import RemoveItem from "@/components/shared/RemoveItem"
import List from "@/components/qualification/List"
import Fab from "@/components/loan/Fab"

export default {
  name: "qualification-index",
  components: {
    Breadcrumbs,
    Fab,
    List,
    RemoveItem
  },
  data: () => ({
    tab: null,
    search: "",
    bus: new Vue(),
    module: 6,
    procedureTypes: [],
    moduleRoles:[],
    userRoles: [],
    selectedProcedure: 0,    
    itemsProcedure: [
      {
      id:'0',
      display_name:'Ver todos'
      },
    ],
    statusLoans: false,
    procedureTypeId: 0,    
  }),
  beforeMount() {
    this.$store.commit("setBreadcrumbs", [
      {
        text: "Préstamos",
        to: { name: "loanIndex" }
      }
    ])
    this.getModuleRoles(this.module)
    this.getProcedureTypes()
    this.getUserRoles(this.$store.getters.id)
  },
  watch: {
    search: _.debounce(function() {
      this.bus.$emit("search", this.search)
    }, 1000),
    statusLoans: function() {
      this.bus.$emit("statusLoans", this.statusLoans)
    }
  },
  methods: {
    getModality(id) {
      this.procedureTypeId = id
    },
    async getProcedureTypes() {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type`,{
          params:{
           module_id: this.module 
          }
        })
        this.procedureTypes = res.data.data
        console.log(this.procedureTypes)
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
        this.moduleRoles = res.data
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
        this.userRoles = res.data.roles
        this.procedureRol()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    procedureRol(){
      //obtiene nombre de roles en base a su id => Tramites
      let rol = []
      for(let i = 0; i < this.userRoles.length; i++){
        rol = this.moduleRoles.find((item) => item.id == this.userRoles[i])
        this.itemsProcedure.push(rol)
      }
    },
  }
}
</script>
