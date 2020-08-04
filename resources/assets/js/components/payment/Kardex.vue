<template>
  <div>
    <template>
      <v-col cols="12" class>
        <v-toolbar-title>KARDEX</v-toolbar-title>
      </v-col>
    </template>
    <v-tooltip top>
      <template v-slot:activator="{ on }">
        <v-btn
          fab
          dark
          x-small
          :color="'success'"
          top
          right
          absolute
          v-on="on"
          style="margin-right: -9px;"
          :to="{ name: 'paymentAdd',  params: { hash: 'new'},  query: { loan_id: $route.params.id}}"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <div>
        <span>Nueva amortización</span>
      </div>
    </v-tooltip>

    <v-data-table
      :headers="headers"
      :items="payments"
      :loading="loading"
      :options.sync="options"
      :server-items-length="10"
      multi-sort
      single-expand
    >
      <!--<template v-slot:header.data-table-select="{ on, props }">
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
          <span v-on="on">{{ searchProcedureModality(item, 'shortened') }}</span>
        </template>
        <span>{{ searchProcedureModality(item, 'name') }}</span>
      </v-tooltip>
      </template>-->

    <template v-slot:item.estimated_date="{ item }">
      {{ item.estimated_date | date }}
    </template>

    <template v-slot:item.estimated_quota="{ item }">
      {{ item.estimated_quota | moneyString }}
    </template>

    <template v-slot:item.estimated_quota="{ item }">
      {{ item.estimated_quota | moneyString }}
    </template>

    <template v-slot:item.capital_payment="{ item }">
      {{ item.capital_payment | moneyString }}
    </template>

    <template v-slot:item.state_id="{ item }">
      {{ item.name }}
    </template>

      <template v-slot:item.actions="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              small
              v-on="on"
              color="warning"
              :to="{ name: 'paymentAdd',  params: { hash: 'view'},  query: { loan_payment: item.id}}"
            >
              <v-icon>mdi-eye</v-icon>
            </v-btn>
          </template>
          <span>Ver amortización</span>
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              small
              v-on="on"
              color="error"
            
              @click.stop="bus.$emit('openRemoveDialog', `loan_payment/${item.id}`)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
          <span>Eliminar amortización</span>
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
        <template></template>
      </template>
    </v-data-table>
    <RemoveItem :bus="bus"/>
  </div>
</template>

<script>
import RemoveItem from '@/components/shared/RemoveItem'
export default {
  name: "Kardex-list",
  components:{
    RemoveItem
  },

  data: () => ({
    bus: new Vue(),
    loading: true,
    search: "",
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ["request_date"],
      sortDesc: [true]
    },
    payments: [],
    //selectedPayment: 0,
    //totalPayments: 0,
    paymentState: 0,
    headers: [
      {
        text: "Nro recibo",
        value: "code",
        class: ["normal", "white--text"],
        align: "center",
        sortable: true
      },
      {
        text: "Fecha estimada de pago",
        value: "estimated_date",
        class: ["normal", "white--text"],
        align: "center",
        sortable: true
      },
      {
        text: "Nro de cuota",
        value: "quota_number",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Cuota [Bs]",
        value: "estimated_quota",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Interes [Bs]",
        value: "interest_payment",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Interes penal [Bs]",
        value: "penal_payment",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Capital pagado [Bs]",
        value: "capital_payment",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Estado",
        value: "state_id",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      },
      {
        text: "Acciones",
        value: "actions",
        class: ["normal", "white--text"],
        align: "center",
        sortable: false
      }
    ],
    printDocs: []
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (
        newVal.page != oldVal.page ||
        newVal.itemsPerPage != oldVal.itemsPerPage ||
        newVal.sortBy != oldVal.sortBy ||
        newVal.sortDesc != oldVal.sortDesc
      ) {
        this.getPayments();
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.options.page = 1;
        this.getPayments();
      }
    }
  },
  mounted() {
    this.bus.$on("added", val => {
      this.getPayments();
    });
    this.bus.$on("removed", val => {
      this.getPayments();
    });
    this.bus.$on("search", val => {
      this.search = val;
    });
    this.getPayments();
    this.docsLoans();
    //this.getPaymentState()
  },
  methods: {
    async getPayments() {
      try {
        let res, res1,i
        this.loading = true;
        res = await axios.get(`loan/${this.$route.params.id}/payment`);
        this.payments = res.data
        for ( i = 0; i < this.payments.length; i++) {
           res1 = await axios.get(`loan_payment/${this.payments[i].id}/state`)
          console.log(res1.data.name );
          this.payments[i].name = res1.data.name  
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async deletePayment(id) {
      try {
        this.loading = true;
        let res = await axios.delete(`loan_payment/${id}`);
        this.payments = res.data
        for ( i = 0; i < this.payments.length; i++) {
           res1 = await axios.get(`loan_payment/${this.payments[i].id}/state`)
          console.log(res1.data.name );
          this.payments[i].name = res1.data.name  
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async imprimir(id, item) {
      try {
        let res;
        if (id == 5) {
          res = await axios.get(`loan_payment/${item}/print/loan_payment`);
        }
        printJS({
          printable: res.data.content,
          type: res.data.type,
          documentTitle: res.data.file_name,
          base64: true
        });
      } catch (e) {
        this.toastr.error("Ocurrió un error en la impresión.");
        console.log(e);
      }
    },
    docsLoans() {
      let docs = [];
      if (this.$store.getters.permissions.includes("print-payment-kardex-loan")) {
        docs.push({ id: 5, title: "Registro de pago", icon: "mdi-cash-multiple" });
      } else {
        console.log("Se ha producido un error durante la generación de la impresión");
      }
      this.printDocs = docs;
      console.log(this.printDocs);
    }
  }
};
</script>