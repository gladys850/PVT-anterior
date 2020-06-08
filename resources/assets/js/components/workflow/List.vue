<template>
  <v-data-table
    v-model="selectedLoans"
    :headers="headers"
    :items="loans"
    :loading="loading"
    :options="options"
    :server-items-length="totalLoans"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
    multi-sort
    :show-select="tray == 'validated'"
    @update:options="updateOptions"
  >
    <template v-slot:header.data-table-select="{ on, props }">
      <v-simple-checkbox color="info" class="grey lighten-3" v-bind="props" v-on="on"></v-simple-checkbox>
    </template>
    <template v-slot:item.data-table-select="{ isSelected, select }">
      <v-simple-checkbox color="success" :value="isSelected" @input="select($event)"></v-simple-checkbox>
    </template>
    <template v-slot:item.role_id="{ item }">
      {{ $store.getters.roles.find(o => o.id == item.role_id).display_name }}
    </template>
    <template v-slot:item.procedure_modality_id="{ item }">
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <span v-on="on">{{ procedureModalities.find(o => o.id == item.procedure_modality_id).shortened }}</span>
        </template>
        <span>{{ procedureModalities.find(o => o.id == item.procedure_modality_id).name }}</span>
      </v-tooltip>
    </template>
    <template v-slot:item.request_date="{ item }">
      {{ item.request_date | date }}
    </template>
    <template v-slot:item.amount_requested="{ item }">
      {{ item.amount_requested | money }}
    </template>
    <template v-slot:item.balance="{ item }">
      {{ item.balance | money }}
    </template>
    <template v-slot:item.estimated_quota="{ item }">
      {{ item.estimated_quota | money }}
    </template>
    <template v-slot:item.actions="{ item }">
      <v-tooltip bottom>
         <template v-slot:activator="{ on }">
          <v-btn
            icon
            small
            v-on="on"
            color="warning"
            :to="{ name: 'flowAdd', params: { id: item.id }}"
          >
            <v-icon>mdi-eye</v-icon>
          </v-btn>
         </template>
        <span>Ver trámite</span>
      </v-tooltip>      
   <v-menu
      offset-y
      close-on-content-click
      v-if="$store.getters.permissions.includes('print-contract-loan') || $store.getters.permissions.includes('print-plan-payment') "
    >
      <template v-slot:activator="{ on }">
        <v-btn
          icon
          color="primary"
          dark
          v-on="on"
        >
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </template>
      <v-list dense class="py-0">
        <v-list-item
          v-for="option in printItems"
          :key="option.id"
          @click="imprimir(option.id, item.id)"
        >
          <v-list-item-icon class="ma-0 py-0 pt-2">
            <v-icon 
              class="ma-0 py-0"
              small
              v-text="option.icon"
              color="light-blue accent-4"
            ></v-icon>
          </v-list-item-icon>
          <v-list-item-title 
            class="ma-0 py-0 mt-n2">
            {{ option.title }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    </template>
  </v-data-table>
</template>
<script>

export default {
  name: 'workflow-list',
  props: {
    bus: {
      type: Object,
      required: true
    },
    tray: {
      type: String,
      default: 'received'
    },
    options: {
      type: Object,
      default: {
        itemsPerPage: 8,
        page: 1,
        sortBy: ['request_date'],
        sortDesc: [true]
      }
    },
    loans: {
      type: Array,
      required: true
    },
    totalLoans: {
      type: Number,
      required: true
    },
    loading: {
      type: Boolean,
      required: true
    },
    procedureModalities: {
      type: Array,
      required: true
    }
  },
  data: () => ({
    selectedLoans: [],
    headers: [
      {
        text: 'Correlativo',
        value: 'code',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Fecha solicitud',
        value: 'request_date',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Solicitado [Bs]',
        value: 'amount_requested',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Saldo capital [Bs]',
        value: 'balance',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Meses Plazo',
        value: 'loan_term',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      }, {
        text: 'Cuota [Bs]',
        value: 'estimated_quota',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Acciones',
        value: 'actions',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }
    ],
        printItems: [
        ]
  }),
  watch: {
    selectedLoans(val) {
      this.bus.$emit('selectLoans', this.selectedLoans)
      if (val.length) {
        this.$emit('allowFlow', true)
      } else {
        this.$emit('allowFlow', false)
      }
    },
    tray(val) {
      if (typeof val === 'string') this.updateHeader()
    }
  },
  mounted() {
    this.bus.$on('emitRefreshLoans', val => {
      this.selectedLoans = []
    }),
    this.printItemsLoans()
  },
  methods: {
    updateOptions($event) {
      if (this.options.page != $event.page || this.options.itemsPerPage != $event.itemsPerPage || this.options.sortBy != $event.sortBy || this.options.sortDesc != $event.sortDesc) this.$emit('update:options', $event)
    },
    async imprimir(id,item)
    {
      try {
        let res
        if(id==1)
        {
          res = await axios.get(`loan/${item}/print/contract`)
          console.log("contrato"+id)
        }
        else if(id==2){
          res = await axios.get(`loan/${item}/print/form`)
           console.log("formu"+id)
        } 
        else {
          res = await axios.get(`loan/${item}/print/plan`)
           console.log("plan "+id)
        } 
        printJS({
            printable: res.data.content,
            type: res.data.type,
            file_name: res.data.file_name,
            base64: true
        })  
      } catch (e) {
        this.toastr.error("Ocurrió un error en la impresión.")
        console.log(e)
      }
      
    },
    updateHeader() {
      if (this.tray != 'all') {
        this.headers = this.headers.filter(o => o.value != 'role_id')
        this.headers = this.headers.filter(o => o.value != 'procedure_modality_id')
      } else {
        if (!this.headers.some(o => o.value == 'role_id')) {
          this.headers.unshift({
            text: 'Modalidad',
            class: ['normal', 'white--text'],
            align: 'center',
            value: 'procedure_modality_id',
            sortable: true
          })
          this.headers.unshift({
            text: 'Área',
            class: ['normal', 'white--text'],
            align: 'center',
            value: 'role_id',
            sortable: true
          })
        }
      }
    },
    printItemsLoans(){
      let items =[]    
      if(this.$store.getters.permissions.includes('print-contract-loan') && this.$store.getters.permissions.includes('print-plan-payment')){
        items=[
          { id: 1, title: 'Contrato', icon: 'mdi-file-document'},
          { id: 2, title: 'Solicitud', icon: 'mdi-file'},
          { id: 3, title: 'Plan de pagos', icon: 'mdi-cash'}
        ]
      }
      else if(this.$store.getters.permissions.includes('print-contract-loan')){
        items=[
          { id: 1, title: 'Contrato', icon: 'mdi-file-document'},
          { id: 2, title: 'Solicitud', icon: 'mdi-file'}
        ]
      }
      else if(this.$store.getters.permissions.includes('print-plan-payment')){
        items=[
          { id: 3, title: 'Plan de pagos', icon: 'mdi-cash'}
        ]
      }
      this.printItems=items
    }
  }
}
</script>
<style>
th.text-start {
  background-color: #757575;
}
</style>