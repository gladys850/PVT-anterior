<template>
  <div>
    <span>{{loansSelected}}</span>
    <!--v-switch v-model="showSelect" label="Habilitar checks" class="pa-3"></v-switch-->
    <v-data-table
      :headers="headers"
      :items="loans"
      :loading="loading"
      :options.sync="options"
      :server-items-length="totalloans"
      :footer-props="{ itemsPerPageOptions: [8, 15, 30] }"
      multi-sort
      single-expand
      v-model="loansSelected"
      :show-select="showSelect"
      :single-select="false"
    >
      <!--template v-slot:item="props">
        <tr :class="props.isExpanded ? 'secondary white--text' : ''">
          <td>
            <v-checkbox v-model="selected" value="true"></v-checkbox>
          </td>
          <td class="text-center">{{ props.item.id }}</td>
          <td class="text-center">{{ props.item.request_date | date }}</td>
          <td class="text-center">{{ props.item.loan_term }}</td>
          <td class="text-center">{{ props.item.disbursement_date | date }}</td>
          <td class="text-right">{{ props.item.balance | money }}</td>
          <td class="text-right">{{ props.item.estimated_quota | money }}</td>
          <td class="text-right">{{ props.item.amount_requested | money }}</td>
          <td class="text-right">{{ props.item.amount_approved | money }}</td>
          <td class="text-center">
            <v-menu offset-y close-on-content-click>
              <template v-slot:activator="{ on }">
                <v-btn icon small color="primary" v-on="on">
                  <v-icon>mdi-printer</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                  v-for="(item, index) in [{title:'Contrato'},{title:'Formulario'}]"
                  :key="index"
                >
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn icon small color="info" v-on="on">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span class="caption">Editar</span>
            </v-tooltip>
          </td>
        </tr>
      </template-->

      <template v-slot:item.request_date="{ item }">
       {{item.request_date | date}}
      </template>
      <template v-slot:item.balance="{ item }">
       {{item.balance | money}}
      </template>
      <template v-slot:item.actions="{ item }">
        <v-btn
          icon
          small
          color="warning"
          :to="{ name: 'qualificationAdd', params: { id: item.id }}"
        >
          <v-icon>mdi-eye</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  name: "qualification-list",
  props: {
    bus: {
      type: Object,
      required: true
    },
    statusLoans: {
      type: Number,
      required: true
    }
  },
  data: () => ({
    loading: true,
    search: "",
    options: {
      page: 1,
      itemsPerPage: 8,
      sortBy: ["request_date"],
      sortDesc: [true]
    },
    loans: [],
    selectedLoan: 0,
    totalloans: 0,
    headers: [
        {
          text: "Correlativo",
          value: "id",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Código",
          value: "code",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Nombre del solicitante",
          value: "",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Modalidad",
          value: "procedure_modality_id",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Fecha solicitud",
          value: "request_date",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Solicitado [Bs]",
          value: "amount_requested",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Saldo capital [Bs]",
          value: "balance",
          class: ["normal", "white--text"],
          align: "center",

          sortable: false
        },
        {
          text: "Meses Plazo",
          value: "loan_term",
          class: ["normal", "white--text"],
          align: "center",

          sortable: true
        },
        {
          text: "Cuota [Bs]",
          value: "estimated_quota",
          class: ["normal", "white--text"],
          align: "center",
          width: "3%",
          sortable: false
        },
        {
          text: "Acciones",
          class: ["normal", "white--text"],
          align: "center",
          value: "actions",

          sortable: false
        }
      ],
    loansSelected: [],
    showSelect:false,
    validated: [],
    received:[],
    countValidated: 0,
    countReceived: 0
  }),
  watch: {
    options: function(newVal, oldVal) {
      if (
        newVal.page != oldVal.page ||
        newVal.itemsPerPage != oldVal.itemsPerPage ||
        newVal.sortBy != oldVal.sortBy ||
        newVal.sortDesc != oldVal.sortDesc
      ) {
        this.getloans();
      }
    },
    search: function(newVal, oldVal) {
      if (newVal != oldVal) {
        this.options.page = 1;
        this.getloans();
      }
    },
    statusLoans (status) {
      if(status == 1){
        this.showSelect = false;
        //
        this.loans=this.received
          console.log(this.received)
      } 
      else if (status == 2) {
        this.showSelect = true;
        //this.loans = []
        this.loans=this.validated
        console.log(this.validated)    
      }
      else{
        this.showSelect = false;
        this.getloans()
      }   
    }
  },
  mounted() {
    this.bus.$on("added", val => {
      this.getloans();
    });
    this.bus.$on("removed", val => {
      this.getloans();
    });
    this.bus.$on("search", val => {
      this.search = val;
    });
    this.getloans();
    
  },
  methods: {
    async getloans(params) {
      try {
        this.loading = true;
        let res = await axios.get(`loan`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            search: this.search
          }
        });
        this.loans = res.data.data;
        this.totalloans = res.data.total;
        delete res.data["data"];
        this.options.page = res.data.current_page;
        this.options.itemsPerPage = parseInt(res.data.per_page);
        this.options.totalItems = res.data.total;
        this.clasificarLoans()
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    clasificarLoans(){
      let val = []
      let rec = []
      let countVal = 0
      let countRec = 0
      for (let i = 0; i < this.loans.length; i++) {
        if(this.loans[i].validated==true){
          val.push(this.loans[i])
          countVal ++
        }
        else{
          rec.push(this.loans[i])
          countRec ++
        }
      }
      this.validated= val
      this.received = rec
      this.countValidated = countVal
      this.countReceived = countRec

      console.log(this.countValidated)
      console.log(this.countReceived)

    },
  }
};
</script>
