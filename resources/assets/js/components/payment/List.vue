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

 
  </v-data-table>
</template>
<script>

export default {
  name: 'payment-list',
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
        text: 'Nro recibo',
        value: 'code',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: true
      },{
        text: 'Fecha estimada de pago',
        value: 'estimated_date',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Nro de cuota',
        value: 'quota_number',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Cuota [Bs]',
        value: 'estimated_quota',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Interes',
        value: 'interest_payment',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Interes penal',
        value: 'penal_payment',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Capital pagado [Bs]',
        value: 'capital_payment',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }, {
        text: 'Estado',
        value: 'state_id',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      },{
        text: 'Acciones',
        value: 'actions',
        class: ['normal', 'white--text'],
        align: 'center',
        sortable: false
      }
    ],
        printDocs: []
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
    this.docsLoans()
  },
  methods: {
    searchProcedureModality(item, attribute = null) {
      let procedureModality = this.procedureModalities.find(o => o.id == item.procedure_modality_id)
      if (procedureModality) {
        if (attribute) {
          return procedureModality[attribute]
        } else {
          return procedureModality
        }
      } else {
        return null
      }
    },
    updateOptions($event) {
      if (this.options.page != $event.page || this.options.itemsPerPage != $event.itemsPerPage || this.options.sortBy != $event.sortBy || this.options.sortDesc != $event.sortDesc) this.$emit('update:options', $event)
    },
    async imprimir(id, item)
    {
      try {
        let res
        if(id==1){
          res = await axios.get(`loan/${item}/print/contract`)
        }else if(id==2){
          res = await axios.get(`loan/${item}/print/form`)
        }else {
          res = await axios.get(`loan/${item}/print/plan`)
        } 
        printJS({
            printable: res.data.content,
            type: res.data.type,
            documentTitle: res.data.file_name,
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
    docsLoans(){
      let docs =[]    
      if(this.$store.getters.permissions.includes('print-contract-loan') && this.$store.getters.permissions.includes('print-payment-plan')){
        docs=[
          { id: 1, title: 'Contrato', icon: 'mdi-file-document'},
          { id: 2, title: 'Solicitud', icon: 'mdi-file'},
          { id: 3, title: 'Plan de pagos', icon: 'mdi-cash'}
        ]
      }
      else if(this.$store.getters.permissions.includes('print-contract-loan')){
        docs=[
          { id: 1, title: 'Contrato', icon: 'mdi-file-document'},
          { id: 2, title: 'Solicitud', icon: 'mdi-file'}
        ]
      }
      else if(this.$store.getters.permissions.includes('print-payment-plan')){
        docs=[
          { id: 3, title: 'Plan de pagos', icon: 'mdi-cash'}
        ]
      }
      this.printDocs=docs
    },
  }
}
</script>
<style>
th.text-start {
  background-color: #757575;
}
</style>