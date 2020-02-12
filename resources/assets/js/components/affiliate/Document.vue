<template>
  <v-data-table
    :headers="headers"
    :items="documents"
    :loading="loading"
    :options.sync="options"
    :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
  >
  <template v-slot:item="props">
    <tr>
      <td class="text-xs-left">{{ procedures.find(o => o.id == props.item.procedure_document_id).name }}</td>
      <td>
        <v-icon x-large dark class="mr-1" :color="props.item.picture_saved ? 'error' : 'tertiary'">mdi-file-pdf</v-icon>
      </td>
      <td >
        <v-btn
          fab
          dark
          x-small
          :to="{ name: 'scannedDocuments', params: {id: props.item.procedure_document_id }}"
          color="danger"
          @click="dialog=true"
        >
        <v-icon>mdi-upload</v-icon>
        </v-btn>
      </td>
    </tr>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
      <v-toolbar dense flat color="tertiary">
        <v-toolbar-title>Subir Documentos</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="dialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
        <v-card-text>
          <v-col cols="12" sm="8" md="8">
                  <v-text-field label="Fecha de vencimiento del documento" required></v-text-field>
          </v-col>
        
          <v-col cols="12" sm="8" md="8">
                  <v-text-field label="Seleccionar Archivo" required></v-text-field>
          </v-col>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="danger" text @click="dialog = false">Guardar</v-btn>
        </v-card-actions>
      
      </v-card>  
    </v-dialog>
  </template>
  </v-data-table>
  
</template>

<script>

import Document from '@/components/affiliate/Document'

export default {
  name: 'document',
  components: {
  Document
  },
  props: ['bus'],
  data: () => ({
    dialog: false,
    loading: true,
    search: '',
    options: {
      page: 1,
      itemsPerPage: 8,
      sortDesc: [false]
    },
    documents: [],
    procedures: [],
    headers: [
      { 
        text: 'Documentos Presentados',
        value: 'procedure_document_id', 
        class: ['normal', 'white--text'],
        width: '80%',
        sortable: false 
      },{ 
        text: 'Estado',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      }
      ,{ 
        text: 'Subir Archivo',
        value: 'procedure_document_id', 
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      }    
    ]
  }),

  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getScannedDocument()
      }
    },  

  },
  mounted() {
    if (!this.isNew) {
      this.resetForm()
    } else {
      this.tab = 'tab-2'
      this.editable = true

      this.procedures()
    }
  },
  methods: {
    resetForm() {
      this.getScannedDocument(this.$route.params.id)
      this.getProcedureDocument(this.$route.params.id)
      
      this.editable = false
      this.reload = true
      this.$nextTick(() => {
      this.reload = false
      })
    },
      async getAffiliate(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}`)
        this.affiliate = res.data
        this.setBreadcrumbs()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getScannedDocument(id) {
      try {
        console.log(id)  
        console.log("true") 
        let res = await axios.get(`document/${id}`)
        console.log(res)
        this.documents = res.data
        console.log('entro'+this.documents)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getProcedureDocument() {
    try {
      this.loading = true
      let res = await axios.get(`procedureDocument`);
      this.procedures = res.data;

    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },

  }
}
</script>

