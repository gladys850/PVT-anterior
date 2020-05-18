<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>
          <Breadcrumbs />
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn-toggle active-class="primary white--text" mandatory v-model="statusLoans">
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
                :items="listProcedure"
                item-text="display_name"
                item-value="id"
              ></v-select>
            </v-col>
            <!--Mostrar modalidades de trámites-->
            <v-col cols="9" class="ma-0 pa-0">
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
                <List :bus="bus" :procedureTypeId="procedureTypeId" :userRoles="userRoles" :listRoles="listRoles" />
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
    userRoles: [],
    selectedRoles: [],
    
    listProcedure: [
     {
      id:'1',
      display_name:'Todos'
    },
    ],
    procedureTypes: null,
    statusLoans: false,
    procedureTypeId: 10,
    selectedProcedure: null,
    listRoles:[]
    
  }),
  beforeMount() {
    this.$store.commit("setBreadcrumbs", [
      {
        text: "Préstamos",
        to: { name: "loanIndex" }
      }
    ])
    this.getModuleRoles(this.module)
    this.getModuleProcedureTypes(this.module)
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
    //Escoger los tramites validados o recibidos
    /*typeStatusLoan (status) {
      this.statusLoans = status
    },*/
    getModality(id) {
      this.procedureTypeId = id
      console.log("prodecure padre" + id)
    },
    async getModuleProcedureTypes(id) {
      try {
        this.loading = true
        let res = await axios.get(`module/${id}/procedure_type`)
        this.procedureTypes = res.data
        console.log("procedureType " + this.procedureTypes)
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
        this.listRoles = res.data
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
      //obtiene nombre de roles(areas) en base a su id
      let rol = []
      let area = []
      console.log("hola1")
      console.log("hla "+ this.userRoles.length)
      for(let i = 0; i < this.userRoles.length; i++){
        area = this.listRoles.find((item) => item.id == this.userRoles[i])
        console.log( area)
        this.listProcedure.push(area)
        console.log("procedure"+this.listProcedure)
      }
      //this.listRoleUser.forEach(element => console.log(element));
      console.log("hola")
    },

  }
}
</script>
