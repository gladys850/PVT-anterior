<template>
  <div class="ma-0 pa-0">
   <!--<span>{{loansSelected}}</span>
   <span>{{ $store.getters.username}}{{ $store.getters.roles}}{{ $store.getters.id}}{{ $store.getters.permissions}}</span>-->
   <v-row class="ma-0 pa-0" v-if="selectedProcedure > 0 && statusLoans">       
      <v-col cols="3" class="ma-0 pa-0 pr-2">
        <v-select eager
          label="Escoger Área"
          v-model="selectedArea"
          :items="itemsArea"
          item-text="display_name"
          item-value="id"
          prepend-inner-icon="mdi-format-list-checks"
          :loading="loading"
          outlined
        ></v-select>
        {{selectedArea}}
      </v-col>  
      <v-col cols="1" class="ma-0 pa-0" @click.stop="derivationLoans()">   
        <v-btn small tile color="success">
            <v-icon left>mdi-file-send</v-icon> Derivar
        </v-btn>          
      </v-col>
     </v-row> 
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
    userRoles: {
      type: Array,
      required: true
    },
    procedureTypeId:{
      type: Number,
      required: true,
      default: 9
    },
    moduleRoles: {
      type:Array,
      required: true
    },
    selectedProcedure: {
      type: String,
      required: true
    }
  },
  data: () => ({
    //current_rol: 82,
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
          value: "validated",
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
    showSelect: false,
    statusLoans: false,    
    validated: 0,
    idLoans: [],
    procedureTypeFlow: [],
    itemsArea: [],
    selectedArea: [],
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
    selectedProcedure: function(newVal, oldVal){
      if(newVal != oldVal && newVal != null)
        this.getloans(newVal)
    },
    statusLoans: function(newVal, oldVal){
      if(newVal != oldVal){
        if(newVal){
          this.showSelect = true
          this.validated = 1
        }else{
          this.showSelect = false
          this.validated = 0       
        }
      } 
      this.getloans()
    },
    procedureTypeId: function(newVal, oldVal){
      if(newVal != oldVal)
      this.itemsArea = []
      this.getProcedureTypeFlow()
      this.getloans()
    },
    loansSelected() {
      let loans = [];
      let idLoan = null;
      for (let i = 0; i < this.loansSelected.length; i++) {
        idLoan = this.loansSelected[i].id;
        loans.push(idLoan);
      }
      this.idLoans = loans;
    }
  },
  mounted() {
    this.bus.$on("search", val => {
      this.search = val;
    });
    this.bus.$on("statusLoans", val =>{
      this.statusLoans = val
    })
    this.bus.$on("selectedProcedure", val =>{
      this.selectedProcedure = val
    })
    this.getloans()
    this.$store.getters.username
    this.$store.getters.roles
    this.$store.getters.id
    this.$store.getters.permissions

  },
  methods: {
    async getloans(params) {
      try {
        this.loading = true; 
        let res
        if(this.selectedProcedure > 0){
          res = await axios.get(`loan`, {
            params: {
              role_id: this.selectedProcedure,
              validated: this.validated,
              procedure_type_id: this.procedureTypeId,
              page: this.options.page,
              per_page: this.options.itemsPerPage,
              sortBy: this.options.sortBy,
              sortDesc: this.options.sortDesc,
              search: this.search
            }
          });
        }else{
          res = await axios.get(`loan`, {
            params: {
              page: this.options.page,
              per_page: this.options.itemsPerPage,
              sortBy: this.options.sortBy,
              sortDesc: this.options.sortDesc,
              search: this.search
            }
        });
        }
        this.loans = res.data.data;
        this.totalloans = res.data.total;
        delete res.data["data"];
        this.options.page = res.data.current_page;
        this.options.itemsPerPage = parseInt(res.data.per_page);
        this.options.totalItems = res.data.total;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getProcedureTypeFlow() {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${this.procedureTypeId}/flow`)
        this.procedureTypeFlow = res.data
        this.roleFlowDerivation()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }, 
    roleFlowDerivation(){
      //obtiene nombre de roles en base a su id => Areas
      let roleFlow = []
      let area = []
      roleFlow = this.procedureTypeFlow.filter((item) => item.role_id == this.selectedProcedure)
      for(let i = 0; i < roleFlow.length; i++){
        area = this.moduleRoles.find((item) => item.id == roleFlow[i].next_role_id)
        this.itemsArea.push(area)
      }
    },
    async derivationLoans() {
      try {
        this.loading = true;
        let res = await axios.patch(`loans`, {
          ids: this.idLoans,
          role_id: this.selectedArea
        });
        this.toastr.success("El trámite fue derivado.")
        this.getloans()
      } catch (e) {
        console.log(e);
        this.toastr.error("Ocurrio un error en la derivación...")
      } finally {
        this.loading = false;
      }
    },
  }
};
</script>
