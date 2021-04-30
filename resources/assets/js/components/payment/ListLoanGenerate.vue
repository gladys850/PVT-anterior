<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>
          <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular">
              <v-toolbar-title>Préstamos Desembolsados</v-toolbar-title>
            </v-toolbar>
          </v-card-title>
                    <v-data-table
        :headers="headers"
        :items="loan_disbursement"
     
        >
               <template v-slot:headers="props">
           <tr>
                <th v-for="(header,index) in props.headers" :key="index" class="text-xs-left">
                    
                        <v-flex >
                            <v-tooltip bottom>
                                <span slot="activator">{{header.text}}</span>
                                <span >{{header.input}}</span>
                            </v-tooltip>
                            <v-menu  v-model="header.menu"
                                    :close-on-content-click="false"
                                    >
                                    <v-btn
                                        slot="activator"
                                        icon
                                        v-if="header.sortable!=false"
                                    >
                                    <v-icon  small :color="header.input!=''?'blue':'black'">fa-filter</v-icon>
                                    </v-btn>
                                    <v-card  v-if="header.type=='text'">
                                        <v-text-field
                                            outline
                                            hide-details
                                            v-model="header.input"
                                            append-icon="search"
                                            :label="`Buscar ${header.text}...`"
                                            @keydown.enter="search()"
                                     
                                            @keyup.esc="header.menu=false"
                                        ></v-text-field>
                                    
                                    </v-card>
                                    <v-card  v-if="header.type=='date'">
                                        
                                        <v-list>
                                            <v-list-tile avatar>
                                                <v-list-tile-content>
                                                    <v-menu
                                                        
                                                        :close-on-content-click="false"
                                                        v-model="menu_date"
                                                        :nudge-right="40"
                                                        lazy
                                                        transition="scale-transition"
                                                        offset-y
                                                        full-width
                                                        max-width="290px"
                                                        min-width="290px"
                                                    >
                                                    
                                                    <v-text-field
                                                        hide-details
                                                        slot="activator"
                                                        v-model="header.input"
                                                        :label="`Buscar ${header.text}...`"
                                                        readonly
                                                    ></v-text-field>
                                                    <v-date-picker v-model="header.input" no-title @input="menu_date = false"></v-date-picker>
                                                
                                                    </v-menu>
                                                </v-list-tile-content>

                                                <v-list-tile-avatar>
                                            
                                                <v-icon @click="search()">search</v-icon>
                                                </v-list-tile-avatar>

                                            </v-list-tile>
                                            </v-list>

                                    </v-card>
                            </v-menu>
                            <!-- <v-icon small @click="toggleOrder(header.value)" v-if="header.value == filterName ">{{pagination.descending==false?'arrow_upward':'arrow_downward'}}</v-icon> -->
                        </v-flex>
                </th>
            </tr>
        </template>
       <template v-slot:item="props">
            
            <tr>
            
            <td class="text-xs-left">{{ props.item.code_loan }}</td>
            <td class="text-xs-left">{{ props.item.registration_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.first_name_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.second_name_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.last_name_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.mothers_last_name_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.surname_husband_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.sub_modality_loan }}</td>
            <td class="text-xs-left">{{ props.item.modality_loan }}</td>
            <td class="text-xs-left">{{ props.item.amount_approved_loan }}</td>
            <td class="text-xs-left">{{ props.item.state_loan}}</td>
            <td class="text-xs-left">{{ props.item.guarantor_loan_affiliate }}</td>
            <td class="text-xs-left">{{ props.item.pension_entity_affiliate }}</td>
            <td>
       
               <!-- <v-edit-dialog
                    :return-value.sync="props.item.PresMeses"
                    large
                    cancel-text="Cancelar"
                    save-text="Guardar"
                    @save="updateLoan(props.item.IdPrestamo, {'PresMeses': props.item.PresMeses})"
                    @cancel="cancelPrueba(props.item.IdPrestamo)"
                > {{ props.item.PresMeses }}
                    <v-text-field
                        slot="input"
                        v-model="props.item.PresMeses"
                        single-line
                        autofocus
                    ></v-text-field>
                </v-edit-dialog>
            </td>
            <td class="text-xs-left"> <a  v-bind:href="generate_link(props.item.IdPrestamo)"><v-icon>assignment</v-icon></a>
                <v-btn icon @click="makePDF(props.item.IdPrestamo)"><v-icon color="info">insert_drive_file</v-icon></v-btn>-->
            </td>
                 </tr>
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
  data: () => ({
      loan_disbursement: [],
              headers: [
            { text: 'Cód. Préstamo', value: 'code_loan',input:'' , menu:false,type:"text"},
            { text: 'CI', value: 'code_loan',input:'' , menu:false,type:"text"},
            { text: 'Matricula', value: 'registration_affiliate' ,input:'', menu:false,type:"text"},
            { text: '1er Nombre', value: 'first_name_affiliate',input:'' , menu:false,type:"text"},
            { text: '2do Nombre', value: 'second_name_affiliate',input:'' , menu:false,type:"text"},
            { text: 'Ap. Paterno', value: 'last_name_affiliate',input:'', menu:false,type:"text"},
            { text: 'Ap. Materno',value:'mothers_last_name_affiliate',input:'', menu:false,type:"text"},
            { text: 'Ap. Casada',value:'surname_husband_affiliate',input:'', menu:false,type:"text"},
            { text: 'Submodalidad',value:'sub_modality_loan',input:'', menu:false,type:"text"},
            { text: 'Modalidad',value:'modality_loan',input:'', menu:false,type:"text"},
            { text: 'Monto Aprobado ',value:'amount_approved_loan',input:'', menu:false,type:"text"},
            { text: 'Tipo Estado Afiliado',value:'state_loan',input:'', menu:false,type:"text"},
            { text: 'Garantes',value:'guarantor_loan_affiliate',input:'', menu:false,type:"text"},
            { text: 'Ente Gestor',value:'pension_entity_affiliate',input:'', menu:false,type:"text"},
            { text: 'Accion',value:'actions',input:'', menu:false,type:"text"},
        ],
                amortizations: [],
        loading: true,
        last_page:1,
        total:0,
        from:0,
        to:0,
        page:1,
        paginationRows: 10,
        pagination_select:[10,20,30],
  }),
    mounted()
    {
        this.search();
    },
  methods: {

        async search(){
            try {
                let res = await axios.post(`list_loan_generate`,
                    {
                        code_loan: "PTM"
                    }
                )
                this.loan_disbursement = res.data.data
            } catch (e) {
                console.log(e)
            }
        },
        getParams(){
            this.params={};
            this.headers.forEach(element => {
                params[element.value] = element.input.toUpperCase();
            });
            // params['sorted']=this.filterName;
            // params['order']=this.pagination.descending==true?'asc':'desc';
            params['page']=this.page;
            params['pagination_rows']=this.paginationRows;
            return params;
        },

  }
};
</script>
