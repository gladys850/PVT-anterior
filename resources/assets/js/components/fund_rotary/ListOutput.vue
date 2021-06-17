<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>

      
          <v-data-table
            :headers="headers"
            :items="fund_rotatory_list"
            :loading="loading"
            :options.sync="options"
            :server-items-length="totalFundRotatoryEntry"
            :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
            multi-sort
            single-expand
          >
              <template v-slot:top>
      <v-toolbar
        flat
      >
  
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog
          v-model="dialog"
          max-width="500px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              color="primary"
              dark
              class="mb-2"
              v-bind="attrs"
              v-on="on"
            >
              New Item
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{  }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="fund_rotatory_item.check_number"
                      label="Nro Cheque"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="fund_rotatory_item.date_check_delivery"
                      label="Fecha de entrega cheque"
                      type="date"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="fund_rotatory_item.amount"
                      label="Monto"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="fund_rotatory_item.description"
                      label="Descripción"
                    ></v-text-field>
                  </v-col>
                  
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="saveFundRotary()"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:expanded-item="{ headers }">
      <tr>
        <td :colspan="headers.length" class="px-0">
          hola
        </td>
      </tr>
    </template>

            <template v-slot:[`item.date_check_delivery`]="{ item }">
              {{ item.date_check_delivery | date}}
            </template>
            <template v-slot:[`item.voucher_type_name`]="{ item }">
              {{ item.voucher_type ? item.voucher_type.name : ''}}
            </template>
            <template v-slot:[`item.amount`]="{ item }">
              {{ item.amount | money}}
            </template>
            <template v-slot:[`item.balance_previous`]="{ item }">
              {{ item.balance_previous | money}}
            </template>
            <template v-slot:[`item.balance`]="{ item }">
              {{ item.balance | money}}
            </template>

            <template v-slot:[`item.actions`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn
                    icon
                    small
                    v-on="on"
                    color="warning"
                    :to="{ name: 'paymentAdd',  params: { hash: 'view'},  query: { loan_payment: item.payable_id}}"
                  >
                    <v-icon>mdi-eye</v-icon>
                  </v-btn>
                </template>
                <span>Ver voucher</span>
              </v-tooltip>

              <v-tooltip bottom v-if="permissionSimpleSelected.includes('delete-voucher-paid')">
                <template v-slot:activator="{ on }">
                  <v-btn
                    icon
                    small
                    v-on="on"
                    color="error"
                    @click.stop="bus.$emit('openRemoveDialog', `voucher/${item.id}`)"
                  >
                    <v-icon>mdi-file-cancel-outline</v-icon>
                  </v-btn>
                </template>
                <span>Anular voucher</span>
              </v-tooltip>

              <v-menu offset-y close-on-content-click>
                <template v-slot:activator="{ on }">
                  <v-btn icon color="primary" dark v-on="on">
                    <v-icon>mdi-printer</v-icon>
                  </v-btn>
                </template>
                <v-list dense class="py-0">
                  <v-list-item v-for="doc in printDocs" :key="doc.id" @click="imprimir(doc.id, item.id)">
                    <v-list-item-icon class="ma-0 py-0 pt-2">
                      <v-icon class="ma-0 py-0" small v-text="doc.icon" color="light-blue accent-4"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title class="ma-0 py-0 mt-n2">{{ doc.title }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
           </v-data-table>
          <RemoveItem :bus="bus" />
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>

import RemoveItem from '@/components/shared/RemoveItem'
export default {
  name: 'fund_rotatory_entry-list',
  components: {
    RemoveItem
  },
  data: () => ({
    bus: new Vue(),
    loading: true,
    search: '',
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ['code_entry'],
      sortDesc: [false]
    },
    fund_rotatory_list: [],
    totalFundRotatoryEntry: 0,
    headers: [

      { 
        text: 'Código',
        value: 'code_entry', 
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: false 
      },
      {
        text: 'Número de cheque',
        value: 'check_number',
        class: ['normal', 'white--text'],
        width: '15%',
        sortable: false
      },{ 
        text: 'Fecha de entrega cheque',
        value: 'date_check_delivery',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false 
      },{
        text: 'Monto',
        value: 'amount',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      },{
        text: 'Saldo Anterior',
        value: 'balance_previous',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      },{
        text: 'Saldo Actual',
        value: 'balance',
        class: ['normal', 'white--text'],
        width: '10%',
        sortable: false
      },{ 
        text: 'Acción',
        value: "actions",
        class: ['normal', 'white--text'],
        sortable: false,
        width: '10%',
        sortable: false
      }    
    ],
    state: [],
    category:[],
    printDocs: [],
    fund_rotatory_item:{}

  }),
  computed: {
    //Metodo para obtener Permisos por rol
    permissionSimpleSelected() {
      return this.$store.getters.permissionSimpleSelected;
    },
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.getFundRotary()
      }
    },  

  },
  mounted() {
    this.bus.$on('removed', val => {
      this.getFundRotary()
    })
    this.getFundRotary()
    this.docsLoans()
  },
  methods: {
    async getFundRotary(params) {
      try {
        
        this.loading = true
        let res = await axios.get(`fund_rotatory_entry`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            //sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            //search: this.search
          }
        })
        
        this.fund_rotatory_list = res.data.data
        console.log(this.fund_rotatory_list)
        this.totalFundRotatoryEntry = res.data.total
        delete res.data['data']
        this.options.page = res.data.current_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
        this.options.totalItems = res.data.total
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

    async saveFundRotary() {
      try {
        let res = await axios.post(`fund_rotatory_entry`, {
  
            check_number: this.fund_rotatory_item.check_number,
            amount: this.fund_rotatory_item.amount,
            date_check_delivery: this.fund_rotatory_item.date_check_delivery,
            description: this.fund_rotatory_item.description,
            role_id: 82,
          }
        )
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },


    async imprimir(id, item){
      try {
        let res;
        if(id == 6){
          res = await axios.get(`voucher/${item}/print/voucher`);
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

        docsLoans() {
      let docs = [];
      if (this.permissionSimpleSelected.includes("print-payment-voucher")) {
        docs.push({ id: 6, title: "Registro de pago", icon: "mdi-cash-multiple" });
      } else {
        console.log("Se ha producido un error durante la generación de la impresión");
      }
      this.printDocs = docs;
      console.log(this.printDocs);
    }


  }
}
</script>

