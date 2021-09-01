<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>
        <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular">
              <v-toolbar-title>{{trashed_loan ? 'Tramites anulados' : 'Seguimiento de Préstamos'}}</v-toolbar-title>
            </v-toolbar>
          </v-card-title>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                @click="download_loans()"
                color="success"
                v-on="on"
                x-small
                absolute
                left
                style="margin-top: -53px; margin-left:260px"
                :loading= loading
              >
                <v-icon> mdi-file-excel </v-icon>
              </v-btn>
            </template>
            <span class="caption">Descargar reporte</span>
          </v-tooltip>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                @click="clearAll()"
                color="info"
                v-on="on"
                x-small
                absolute
                left
                style="margin-top: -53px; margin-left:300px"
              >
                <v-icon> mdi-broom </v-icon>
              </v-btn>
            </template>
            <span class="caption">Limpiar todos los filtros</span>
          </v-tooltip>
          <v-tooltip top v-if="permissionSimpleSelected.includes('show-deleted-loan')">
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                @click="trashed_loan = !trashed_loan"
                :color="!trashed_loan ? 'error' : 'success'"
                v-on="on"
                x-small
                absolute
                right
                style="margin-top: -53px; margin-right:10px"
              >
                <v-icon>{{!trashed_loan ? 'mdi-file-cancel' : 'mdi-file-multiple'}}</v-icon>
              </v-btn>
            </template>
            <span v-if="!trashed_loan">Trámites anulados</span>
            <span v-else>Seguimiento de tramites</span>
          </v-tooltip>
          <v-data-table
            dense
            :headers="headers"
            :items="loans"
            :options.sync="options"
            :server-items-length="totalLoans"
            :item-class="itemRowBackground"
            :footer-props="{ itemsPerPageOptions: [8, 15, 50,100] }"
            :loading = loading_table
          >
            <template v-slot:[`header.city_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.city_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.city_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.name_role_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.name_role_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.name_role_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.user_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.user_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.user_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.code_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.code_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.code_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.identity_card_borrower`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.identity_card_borrower != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.identity_card_borrower"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.registration_borrower`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="searching.registration_borrower != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.registration_borrower"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.full_name_borrower`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="
                        searching.full_name_borrower != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.full_name_borrower"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.shortened_sub_modality_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="
                        searching.shortened_sub_modality_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.shortened_sub_modality_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`header.state_loan`]="{ header }">
              {{ header.text }}<br />
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon
                      small
                      :color="
                        searching.state_loan != '' ? 'red' : 'black'"
                    >
                      mdi-filter
                    </v-icon>
                  </v-btn>
                </template>
                <div>
                  <v-text-field
                    dense
                    v-model="searching.state_loan"
                    type="text"
                    :label="'Buscar ' + header.text"
                    @keydown.enter="search_loans()"
                    hide-details
                    single-line
                  ></v-text-field>
                </div>
              </v-menu>
            </template>

            <template v-slot:[`item.shortened_sub_modality_loan`]="{ item }">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <span v-on="on">{{ item.shortened_sub_modality_loan }}</span>
                </template>
                <span>{{ item.sub_modality_loan }}</span>
              </v-tooltip>
            </template>
            <template v-slot:[`item.request_date_loan`]="{ item }">
              {{ item.request_date_loan | date }}
            </template>
            <template v-slot:[`item.amount_approved_loan`]="{ item }">
              {{ item.amount_approved_loan | money }}
            </template>
            <template v-slot:[`item.balance_loan`]="{ item }">
              {{ item.balance_loan | money }}
            </template>
            <template v-slot:[`item.quota_loan`]="{ item }">
              {{ item.quota_loan | moneyString }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <template v-if="item.state_loan != 'Anulado'">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      icon
                      small
                      v-on="on"
                      color="black"
                      :to="{ name: 'tracingAdd', params: { id: item.id_loan } }"
                      ><v-icon>mdi-eye</v-icon>
                    </v-btn>
                  </template>
                  <span>Ver información del trámite</span>
                </v-tooltip>

                <v-tooltip bottom v-if="permissionSimpleSelected.includes('release-loan-user')">
                  <template v-slot:activator="{ on }" v-if="item.user_loan != null">
                    <v-btn
                      icon
                      small
                      v-on="on"
                      color="error"
                      @click.stop="freeLoan(item.id, item.code)"
                    >
                      <v-icon>mdi-lock-open-variant</v-icon>
                    </v-btn>
                  </template>
                  <span>Liberar usuario del trámite</span>
                </v-tooltip>

                <v-menu offset-y close-on-content-click>
                  <template v-slot:activator="{ on }">
                    <v-btn icon color="primary" dark v-on="on">
                      <v-icon>mdi-printer</v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="py-0">
                    <span v-for="doc in printDocs" :key="doc.id">
                    <v-list-item v-if="!(doc.id >= 3 && item.state_loan == 'En Proceso')" @click="imprimir(doc.id, item.id_loan)">
                        <v-list-item-icon class="ma-0 py-0 pt-2">
                          <v-icon class="ma-0 py-0" small v-text="doc.icon" color="light-blue accent-4"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-title class="ma-0 py-0 mt-n2">{{ doc.title }}</v-list-item-title>
                    </v-list-item>
                    </span>
                  </v-list>
                </v-menu>
              </template>
            </template>
          </v-data-table>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>
export default {
  name: "list-loans-generate",

  data() {
    return {
      searching: {
        city_loan:"",
        name_role_loan:"",
        user_loan:"",
        code_loan: "",
        identity_card_borrower: "",
        registration_borrower: "",
        full_name_borrower: "",
        shortened_sub_modality_loan: "",
        state_loan: "",
      },
      headers: [
        { text: 'Dpto', value: 'city_loan',input:'' , menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '10%', sortable: false},
        { text: 'Área', value: 'name_role_loan',input:'' , menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Usuario',value:'user_loan',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Cód. Préstamo', value: 'code_loan',input:'' , menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '15%',sortable: true},
        { text: 'CI Prestatario', value: 'identity_card_borrower',input:'' , menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Matrícula Prestatario', value: 'registration_borrower',input:'' , menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%', sortable: false},
        { text: 'Nombre Completo Prestatario',value:'full_name_borrower',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '20%', sortable: false},
        { text: 'Corto Sub modalidad',value:'shortened_sub_modality_loan',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '10%',sortable: false},
        { text: 'Fecha Solicitud',value:'request_date_loan',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Monto aprobado', value: 'amount_approved_loan' ,input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Plazo', value: 'loan_term',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '2%',sortable: false},
        { text: 'Cuota',value:'quota_loan',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Estado',value:'state_loan',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'],width: '5%',sortable: false},
        { text: 'Acción',value:'actions',input:'', menu:false,type:"text",class: ['normal', 'white--text','text-md-center'], sortable: false,width: '15%'},
      ],
      loans: [],
      printDocs: [],
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ["code_loan"],
        sortDesc: [false],
      },
      totalLoans: 0,
      loading: false,
      loading_table: false,
      trashed_loan: false
    };
  },
  computed: {
    //permisos del selector global por rol
    permissionSimpleSelected() {
      return this.$store.getters.permissionSimpleSelected;
    },
  },
  watch: {
    options: function (newVal, oldVal) {
      if (
        newVal.page != oldVal.page ||
        newVal.itemsPerPage != oldVal.itemsPerPage ||
        newVal.sortBy != oldVal.sortBy ||
        newVal.sortDesc != oldVal.sortDesc
      ) {
        this.search_loans()
      }
    },
    trashed_loan: function(newVal, oldVal){
      if(newVal!= oldVal){
        this.search_loans()
      }
    }
  },
  mounted() {
    this.search_loans()
    this.docsLoans()
  },
  methods: {
    async search_loans() {
      this.loading_table = true
      try {
        let res = await axios.get(`loan_tracking`, {
          params: {
            city_loan:this.searching.city_loan,
            name_role_loan:this.searching.name_role_loan,
            user_loan:this.searching.user_loan,
            code_loan: this.searching.code_loan,
            identity_card_borrower: this.searching.identity_card_borrower,
            registration_borrower: this.searching.registration_borrower,
            full_name_borrower: this.searching.full_name_borrower,
            shortened_sub_modality_loan: this.searching.shortened_sub_modality_loan,
            state_loan: this.searching.state_loan,
            excel: false,
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sortBy: this.options.sortBy,
            sortDesc: this.options.sortDesc,
            trashed_loan: this.trashed_loan
          },
        });
        this.loans = res.data.data
        this.totalLoans = res.data.total
        delete res.data["data"]
        this.options.page = res.data.current_page
        this.options.itemsPerPage = parseInt(res.data.per_page)
        //this.options.totalItems = res.data.total
        this.loading_table = false
      } catch (e) {
        console.log(e)
        this.loading_table = false
      }
    },

    async download_loans() {
      this.loading = true
      await axios({
        url: "/loan_tracking",
        method: "GET",
        responseType: "blob", // important
        headers: { Accept: "application/vnd.ms-excel" },
        data: this.datos,
        params: {
            city_loan:this.searching.city_loan,
            name_role_loan:this.searching.name_role_loan,
            user_loan:this.searching.user_loan,
            code_loan: this.searching.code_loan,
            identity_card_borrower: this.searching.identity_card_borrower,
            registration_borrower: this.searching.registration_borrower,
            full_name_borrower: this.searching.full_name_borrower,
            shortened_sub_modality_loan: this.searching.shortened_sub_modality_loan,
            state_loan: this.searching.state_loan,
            excel: true,
            trashed_loan: this.trashed_loan
        },
      })
        .then((response) => {
          console.log(response)
          const url = window.URL.createObjectURL(new Blob([response.data]))
          const link = document.createElement("a")
          link.href = url
          link.setAttribute("download", "ReportePrestamo.xls")
          document.body.appendChild(link)
          link.click()
        })
        .catch((error) => {
          console.log(error)
          this.loading = false
        })
        this.loading = false
    },

    clearAll() {
      this.searching.city_loan = "",
      this.searching.name_role_loan = "",
      this.searching.user_loan = "",
      this.searching.code_loan = "",
      this.searching.identity_card_borrower = "",
      this.searching.registration_borrower = "",
      this.searching.full_name_borrower = "",
      this.searching.shortened_sub_modality_loan = "",
      this.searching.state_loan = "",
      this.search_loans()
    },

    async imprimir(id, item) {
      try {
        let res
        if (id == 1) {
          res = await axios.get(`loan/${item}/print/contract`)
        } else if (id == 2) {
          res = await axios.get(`loan/${item}/print/form`)
        } else if (id == 3) {
          res = await axios.get(`loan/${item}/print/plan`)
        } else {
          res = await axios.get(`loan/${item}/print/kardex`)
        }
        printJS({
          printable: res.data.content,
          type: res.data.type,
          documentTitle: res.data.file_name,
          base64: true,
        })
      } catch (e) {
        this.toastr.error("Ocurrió un error en la impresión.")
        console.log(e)
      }
    },
    docsLoans() {
      let docs = []
        docs.push(
          { id: 1, title: "Contrato", icon: "mdi-file-document" },
          { id: 2, title: "Solicitud", icon: "mdi-file" },
          { id: 3, title: "Plan de pagos", icon: "mdi-cash" },
          { id: 4, title: "Kardex", icon: "mdi-view-list" }
        )
      this.printDocs = docs
      console.log(this.printDocs)
    },
    itemRowBackground: function (item) {
      if(item.validated_loan === true && item.user_loan != null && item.state_loan != 'Anulado'){
        return 'style-1'
      }else if(item.validated_loan === false && item.user_loan != null && item.state_loan != 'Anulado'){
        return 'style-2'
      }else if(item.state_loan == 'Anulado'){
        return 'style-3'
      }else{
        return 'style-4'
      }
    },

    async freeLoan(id, code){
      try {
          //this.loading = true;
            let res = await axios.patch(`loan/${id}`, {
              user_id: null,
              validated: false
            });
            console.log(res)
            //this.sheet = false;
            this.bus.$emit('emitRefreshLoans');
            this.toastr.success("El trámite "+ code +" fue liberado" )
      } catch (e) {
        console.log(e)
        this.toastr.error("Ocurrió un error en la liberación del trámite...")
      }
    }
  },
}
</script>
<style scoped>
.v-text-field {
  background-color: white;
  width: 200px;
  padding: 5px;
  margin: 0px;
  font-size: 0.8em;
  border-color: teal;
}
th.text-start {
  background-color: #757575;
}
.style-1 {
  background-color: #8BC34A
}
.style-2 {
  background-color: yellow
}
.style-3 {
  background-color: yellow
}
.style-4 {
  background-color: white
}
</style>
