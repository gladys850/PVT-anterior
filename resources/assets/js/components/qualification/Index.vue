<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>
          <Breadcrumbs/>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn-toggle
        
        active-class="primary white--text"
        mandatory
        >
          <!--<v-btn text value="0" @click="typeStatusLoan(0)">
            TODOS
          </v-btn>-->
          <v-btn text value="1" @click="typeStatusLoan(1)">
            RECIBIDOS
          </v-btn>
          <v-btn text value="2" @click="typeStatusLoan(2)">
            REVISADOS
          </v-btn>
        </v-btn-toggle>

        <v-divider
          class="mx-2"
          inset
          vertical
        ></v-divider>
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

        <Fab v-if="$store.getters.permissions.includes('create-qualification')"/>
      </v-toolbar>
    </v-card-title>
    <!--<v-card-text> 
      <template v-if="statusLoans==0">
        <v-card flat class="ma-0 px-2">
          <List :bus="bus" :statusLoans="0"/>
      </v-card>
      </template>--> 
      <template>
        <v-card class="ma-0 px-0" >
         <v-row class="ma-0 pa-0 px-2">
          <v-col cols="8" class="ma-0 px-0" >
          <v-tabs v-model="tab" background-color="primary" dark>
            <v-tab href="#tab-1">Mod. Anticipo</v-tab>
            <v-tab href="#tab-2">Mod. Corto Plazo</v-tab>
            <v-tab href="#tab-3">Mod. Largo Plazo</v-tab>
            <v-tab href="#tab-4">Mod. Hipotecario</v-tab>       
          </v-tabs>
          </v-col>
          
         <v-col cols="3" class="ma-0 pa-0 pl-2" v-if="this.statusLoans==2">
            <v-select label="Seleccionar Área"  >             
            </v-select>
           </v-col>  
          <v-col cols="1" class="ma-0 pb-0" v-if="this.statusLoans==2">   
            <v-btn small tile color="success">
                <v-icon left>mdi-file-send</v-icon> Derivar
            </v-btn>          
         </v-col>
         </v-row>   
          <v-tabs-items v-model="tab">
            <v-tab-item v-for="i in 4" :key="i" :value="'tab-' + i">
              <v-card flat class="ma-0 px-2">
                <List :bus="bus" :statusLoans="statusLoans"/>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </template> 
  
    </v-card-text>
    <RemoveItem :bus="bus"/>
  </v-card>
</template>

<script>
import Breadcrumbs from '@/components/shared/Breadcrumbs'
import RemoveItem from '@/components/shared/RemoveItem'
import List from '@/components/qualification/List'
import Fab from '@/components/loan/Fab'

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
    search: '',
    bus: new Vue(),
    statusLoans: 0,  
  }),
  beforeMount() {
    this.$store.commit('setBreadcrumbs', [
      {
        text: 'Calificación',
        to: { name: 'loanIndex' }
      }
    ])
  },
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000)
  },
  methods: {
    //Escoger los tramites validados o recibidos
    typeStatusLoan (status) {
      this.statusLoans = status;
    }    
  },
}
</script>