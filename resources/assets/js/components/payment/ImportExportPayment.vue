<template>
  <v-container fluid>
    <ValidationObserver>
      <v-form>
        <v-card flat>
          <v-card-title class="pa-0 pb-3">
            <v-toolbar dense color="tertiary" class="font-weight-regular pa-3">
              <v-col cols="12" md="12">
                <v-row>
                  <v-col cols="12" md="8" class="py-0">
                    <v-toolbar-title>GENERACIÓN DE PERIODOS </v-toolbar-title>
                  </v-col>
                  <v-col cols="12" md="2" class="py-0">
                    <v-select
                      dense
                      :items="year"
                      item-text="year"
                      item-value="year"
                      :loading="loading"
                      label="Gestion"
                      v-model="period_year"
                      @change="Onchange()"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="2" class="py-0">
                    <v-tooltip top>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          fab
                          dark
                          x-small
                          :color="'success'"
                          bottom
                          right
                          v-on="on"
                          @click.stop="savePeriod()"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </template>
                      <div>
                        <span>Añadir Periodo</span>
                      </div>
                    </v-tooltip>
                  </v-col>
                </v-row>
              </v-col>
            </v-toolbar>
          </v-card-title>
          <template>
            <v-container fluid class="px-2 pt-0">
              <v-row justify="center" class="py-0">
                <v-col cols="12" md="4" class="py-0"
                  v-for="(item, i) in month"
                  :key="i">
                  <v-card color="#454545" class="headline font-weight-bold"  max-width="90%" max-height="500" >
                    <v-card-text >
                      <v-row>
                        <v-col cols="4" md="12" class="py-0">
                          <center><b><h1 style="color:white">
                            {{meses[item.month - 1]}}
                          </h1></b></center>
                        </v-col>
                        <v-progress-linear color="white"></v-progress-linear>
                        <br>
                        <v-col cols="6" md="6" class="py-0">
                          <b style="color:white">Solicitud Comando/Senasir</b>
                        </v-col>
                        <v-col cols="6" md="6" class="py-0">
                          <b style="color:white">Importaciones Comando/Senasir</b>
                        </v-col>
                        <v-col cols="6" md="6" class="py-0">
                          <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                fab
                                dark
                                small
                                :color="'info'"
                                bottom
                                right
                                v-on="on"
                                @click.stop="solicitudComando('C', item.id)"
                               >
                                <v-icon style="color:white">mdi-city-variant-outline</v-icon>
                              </v-btn>
                            </template>
                            <div>
                              <span>Solicitud Comando</span>
                            </div>
                          </v-tooltip>
                          <v-tooltip top>
                            <template v-slot:activator="{ on}">
                              <v-btn
                                fab
                                dark
                                small
                                :color="'info'"
                                right
                                v-on="on"
                                 @click.stop="solicitudSenasir('S', item.id)"
                              >
                                <v-icon  style="color:white" >mdi-city</v-icon>
                              </v-btn>
                            </template>
                            <div>
                              <span>Solicitud Senasir</span>
                            </div>
                          </v-tooltip>
                        </v-col>
                        <v-col cols="6" md="6" class="py-0">
                           <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                fab
                                dark
                                small
                                :color="'info'"
                                bottom
                                right
                                v-on="on"
                                @click.stop="importacionComando(item.month, item.id)"
                               >
                                <v-icon style="color:white">mdi-warehouse</v-icon>
                              </v-btn>
                            </template>
                            <div>
                              <span>Importación Comando</span>
                            </div>
                          </v-tooltip>
                           <v-tooltip top>
                            <template v-slot:activator="{ on}">
                              <v-btn
                                fab
                                dark
                                small
                                :color="'info'"
                                right
                                v-on="on"
                                 @click.stop="importacionSenasir(item.month, item.id)"
                              >
                                <v-icon  style="color:white" >mdi-home-analytics</v-icon>
                              </v-btn>
                            </template>
                            <div>
                              <span>Importación Senasir</span>
                            </div>
                          </v-tooltip>
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-progress-linear color="white"></v-progress-linear>
                    <v-card-actions>
                      <v-col cols="4" md="8" class="py-0">
                        <b style="color:white" class="caption">Reportes Comando/Senasir</b>
                      </v-col>
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            fab
                            dark
                            small
                            :color="'#454545'"
                            bottom
                            right
                            v-on="on"
                            :loading="reporte_comando_loading"
                            @click.stop="reporteComandoSenasir(item.id,'C')"
                          >
                            <v-icon>mdi-warehouse</v-icon>
                          </v-btn>
                        </template>
                        <div>
                          <span>Reporte Pago Comando</span>
                        </div>
                      </v-tooltip>
                      <v-tooltip top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            fab
                            dark
                            small
                            :color="'#454545'"
                            bottom
                            right
                            v-on="on"
                            :loading="reporte_senasir_loading"
                            @click.stop="reporteComandoSenasir(item.id,'S')"
                          >
                            <v-icon >mdi-home-analytics</v-icon>
                          </v-btn>
                        </template>
                        <div>
                          <span>Reporte Pago Senasir</span>
                        </div>
                      </v-tooltip>
                    </v-card-actions>
                  </v-card>
                  <br>
                </v-col>
              </v-row>
            </v-container>
            <v-dialog
              v-model="dialog"
              fullscreen
              hide-overlay
              transition="dialog-bottom-transition"
            >
              <v-card>
                <v-toolbar dark color="primary" >
                  <v-btn icon dark @click="dialog=close()" >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-toolbar-title>IMPORTACION {{title}}</v-toolbar-title>
                </v-toolbar>
                <v-col cols="12" >
                  <v-row>
                    <v-col cols="2"  md="2" >
                    </v-col>
                    <v-col cols="8"  md="8" >
                      <v-col cols="12" >
                        <v-row>
                           <v-col cols="1"  md="1" >
                           </v-col>
                          <v-col cols="8"  md="8" >
                            <v-toolbar-title>
                              <center><b>INFORMACION PARA EL DESCUENTO DEL MES DE {{meses[ aux_period- 1]}} </b></center>
                            </v-toolbar-title>
                          </v-col>
                          <v-col cols="2"  md="2" >
                            <div class="text-center">
                            <v-menu
                              v-model="menu"
                              :close-on-content-click="false"
                              :nudge-width="200"
                              offset-x
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                  text
                                  color="indigo"
                                  dark
                                  v-bind="attrs"
                                  v-on="on"
                                >
                                  Información
                                </v-btn>
                              </template>
                              <v-card>
                                <v-list>
                                  <v-list-item>
                                    <v-list-item-content>
                                      <v-list-item-title v-show="import_export.state_affiliate !='C'">Importación Senasir</v-list-item-title>
                                       <v-list-item-title v-show="import_export.state_affiliate =='C'">Importación Comando</v-list-item-title>
                                      <v-list-item-subtitle>Descripción de documento de Importacion</v-list-item-subtitle>
                                    </v-list-item-content>
                                  </v-list-item>
                                </v-list>
                                <v-divider></v-divider>
                                <div class="py-1 pl-2 ma-1">
                                    <small class="py-0 ma-0"><v-icon class="py-1 ma-0">mdi-check</v-icon>
                                      Archivo CSV </small> <br>
                                    <small v-show="import_export.state_affiliate =='C'"><v-icon>mdi-check</v-icon>
                                      NOMBRE DEL ARCHIVO EJEMPLO: comando-2021-03.csv</small>
                                    <small v-show="import_export.state_affiliate !='C'"><v-icon>mdi-check</v-icon>
                                      NOMBRE DEL ARCHIVO EJEMPLO: senasir-2021-03.csv</small><br>
                                    <small class=" pl-6 ma-1"><v-icon >mdi-arrow-right-thick</v-icon>
                                      tipo-año-periodo.csv</small><br>
                                    <small><v-icon>mdi-check</v-icon>
                                      FORMATO CABECERA DEL ARCHIVO</small><br v-show="import_export.state_affiliate !='C'">
                                      <small class=" pl-6 ma-1" v-show="import_export.state_affiliate !='C'">
                                      MATRICULA:MATRICULA_DH:MONTO</small><br v-show="import_export.state_affiliate =='C'">
                                      <small class=" pl-6 ma-1" v-show="import_export.state_affiliate =='C'">
                                      CI:MONTO</small><br>
                                     <small><v-icon>mdi-check</v-icon>
                                      Campos del Archivo CSV</small><br v-show="import_export.state_affiliate !='C'" >
                                    <small v-show="import_export.state_affiliate !='C'" class=" pl-6 ma-1"><v-icon >mdi-arrow-right-thick</v-icon>
                                      MATRICULA: Matricula del afiliado</small><br v-show="import_export.state_affiliate !='C'">
                                     <small class=" pl-6 ma-1" v-show="import_export.state_affiliate !='C'"><v-icon >mdi-arrow-right-thick</v-icon>
                                      MATRICULA DERECHO HABIENTE: Matricula del conyugue</small><br v-show="import_export.state_affiliate =='C'">
                                    <small class=" pl-6 ma-1" v-show="import_export.state_affiliate =='C'"><v-icon >mdi-arrow-right-thick</v-icon>
                                      CI: CI del afiliado</small><br>
                                     <small class=" pl-6 ma-1"><v-icon >mdi-arrow-right-thick</v-icon>
                                      MONTO : Monto de la importación</small><br>
                                </div>
                              <v-card-actions>
                              <v-spacer></v-spacer>
                              <v-btn
                                text
                                @click="menu = false"
                              >
                                Salir
                              </v-btn>
                            </v-card-actions>
                          </v-card>
                        </v-menu>
                      </div>
                    </v-col>
                  </v-row>
                  <v-progress-linear
                    color="info"
                    height="15"
                    :value="percentage"
                    striped
                  >
                    <strong>Porcentaje de Importación: {{percentage}}%</strong>
                  </v-progress-linear>
                </v-col>
                    <v-stepper v-model="e1" >
                      <v-stepper-header class=" !pa-0 ml-0" >
                        <template>
                          <v-stepper-step
                            :key="`${1}-step`"
                            :complete="e1 > 1"
                            :step="1">Subir Archivo
                          </v-stepper-step >
                          <v-divider v-if="1 !== steps" :key="1" ></v-divider>
                          <v-stepper-step
                            :key="`${2}-step`"
                            :complete="e1 > 2"
                            :step="2">Validar Datos
                          </v-stepper-step>
                          <v-divider v-if="2 !== steps" :key="2" ></v-divider>
                          <v-stepper-step
                            :key="`${3}-step`"
                            :complete="e1 > 3"
                            :step="3">Importar
                          </v-stepper-step>
                          <v-divider v-if="3 !== steps" :key="3" ></v-divider>
                        </template>
                      </v-stepper-header>
                      <v-stepper-items>
                        <v-stepper-content :key="`${1}-content`" :step="1">
                          <v-card color="grey lighten-1">
                            <v-card-text >
                              <v-card color="white">
                                <v-col cols="12" md="12">
                                  <v-row>
                                    <v-col cols="1" md="1">
                                    </v-col>
                                    <v-col cols="5" md="5">
                                      <v-select
                                        dense
                                        :items="state_affiliate"
                                        item-text="name"
                                        item-value="value"
                                        label="Estado del afiliado"
                                        disabled
                                        readonly
                                        v-model="import_export.state_affiliate"
                                      ></v-select>
                                    </v-col>
                                    <v-col cols="5" md="5">
                                      <v-file-input
                                        counter
                                        show-size
                                        truncate-length="30"
                                        outlined
                                        small-chips
                                        dense
                                        label="Cargar Archivo"
                                        v-model="import_export.file"
                                      ></v-file-input>
                                    </v-col>
                                    <v-col cols="9" md="9" class="py-0">
                                    </v-col>
                                    <v-col cols="2" md="2" class="py-0">
                                      <v-btn
                                        color="success"
                                        @click.stop="uploadFilePayment()"
                                      > Subir Archivo
                                      </v-btn>
                                    </v-col>
                                  </v-row>
                                </v-col>
                              </v-card >
                            </v-card-text>
                            <v-container class="py-0">
                              <v-row>
                                <v-spacer></v-spacer> <v-spacer></v-spacer> <v-spacer></v-spacer>
                                  <v-col class="py-0">
                                    <v-btn
                                      v-show="validar_datos"
                                      color="primary"
                                      @click="nextStep(1)">
                                      Siguiente
                                    </v-btn>
                                  </v-col>
                                  <!--{{contrib_codebtor}}-->
                              </v-row>
                            </v-container>
                          </v-card>
                        </v-stepper-content>
                        <v-stepper-content :key="`${2}-content`" :step="2" >
                          <v-card color="grey lighten-1">
                            <v-card-text >
                              <v-card color="white">
                                <v-row>
                                  <v-col cols="2">
                                  </v-col>
                                  <v-col cols="3">
                                    <label>
                                      Nombre del Archivo:{{import_export.file_name}}
                                    </label>
                                  </v-col>
                                  <v-col cols="4" v-show="import_export.state_affiliate== 'S'">
                                    <label>
                                      Tipo de Importacion : SENASIR
                                    </label>
                                  </v-col>
                                  <v-col cols="4" v-show="import_export.state_affiliate== 'C'">
                                    <label>
                                      Tipo de Importacion : COMANDO
                                    </label>
                                  </v-col>
                                  <v-col cols="3">
                                    <label>
                                      {{'Periodo: '+period_year+'-'+aux_period}}
                                    </label>
                                  </v-col>
                                  <v-col cols="2">
                                  </v-col>
                                  <v-col cols="3" style="color:teal">
                                    <label>
                                      <b>{{'Datos Copiados: '+import_export.reg_copy}}</b>
                                    </label>
                                  </v-col>
                                  <v-col cols="3" style="color:teal">
                                    <label>
                                      <b>{{'Datos Agrupados: '+import_export.reg_group}}</b>
                                    </label>
                                  </v-col>
                                  <v-col cols="12" >
                                    <v-progress-linear></v-progress-linear>
                                  </v-col>
                                  <v-col cols="4" >
                                  </v-col>
                                  <v-col cols="3" v-show="validar_datos" >
                                    <v-btn
                                      color="success"
                                      @click.stop="validateFilePayment()"
                                      >Validar Datos
                                      <v-icon color="white">mdi-check</v-icon>
                                    </v-btn>
                                  </v-col>
                                  <v-col cols="4" v-show="true" >
                                    <v-tooltip top>
                                      <template v-slot:activator="{ on }">
                                        <v-btn
                                          color="info"
                                          v-on="on"
                                          @click="dialog_confirm=true"
                                        >Rehacer
                                          <v-icon color="white">mdi-eraser</v-icon>
                                        </v-btn>
                                      </template>
                                      <div>
                                        <span>Empezar de nuevo</span>
                                      </div>
                                    </v-tooltip>
                                  </v-col>
                                </v-row>
                              </v-card>
                              <v-container class="py-0">
                                <v-row>
                                <v-spacer></v-spacer><v-spacer> </v-spacer> <v-spacer></v-spacer>
                                  <v-col class="py-0">
                                    <v-btn
                                      v-show="importacion"
                                      right
                                      color="primary"
                                      @click.stop="nextStep(2)">
                                      Siguiente
                                    </v-btn>
                                  </v-col>
                                </v-row>
                              </v-container>
                            </v-card-text >
                          </v-card>
                        </v-stepper-content>
                        <v-stepper-content :key="`${3}-content`" :step="3" >
                          <v-card color="grey lighten-1">
                            <h3 class="text-uppercase text-center">Ultimo Paso la Importación</h3>
                              <v-card-text >
                                <v-card color="white">
                                  <v-row>
                                   <v-col cols="2">
                                    </v-col>
                                    <v-col cols="3">
                                      <label>
                                        Nombre del Archivo:{{import_export.file_name}}
                                      </label>
                                    </v-col>
                                    <v-col cols="4" v-show="import_export.state_affiliate== 'S'">
                                      <label>
                                        Tipo de Importacion : SENASIR
                                      </label>
                                    </v-col>
                                    <v-col cols="4" v-show="import_export.state_affiliate== 'C'">
                                      <label>
                                        Tipo de Importacion : COMANDO
                                      </label>
                                    </v-col>
                                    <v-col cols="3">
                                      <label>
                                        {{'Periodo: '+period_year+'-'+aux_period}}
                                      </label>
                                    </v-col>
                                    <v-col cols="2">
                                    </v-col>
                                    <v-col cols="3" style="color:teal">
                                      <label>
                                        <b>{{'Datos Copiados: '+import_export.reg_copy}}</b>
                                      </label>
                                    </v-col>
                                    <v-col cols="3" style="color:teal">
                                      <label>
                                        <b>{{'Datos Agrupados: '+import_export.reg_group}}</b>
                                      </label>
                                    </v-col>
                                    <v-col cols="12" >
                                      <v-progress-linear></v-progress-linear>
                                    </v-col>
                                    <v-col cols="3" >
                                    </v-col>
                                    <v-col cols="4" >
                                      <v-btn dark color="success" v-show="importacion" @click="dialog_confirm_import=true" >
                                        Ejecutar la Importación
                                        <v-icon color="white">mdi-check</v-icon>
                                      </v-btn>
                                    </v-col>
                                    <v-col cols="4" v-show="true" >
                                      <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                          <v-btn
                                            color="info"
                                            v-on="on"
                                            @click.stop="closePayment()"
                                          > Rehacer
                                            <v-icon>mdi-eraser</v-icon>
                                          </v-btn>
                                        </template>
                                        <div>
                                          <span>Empezar de nuevo</span>
                                        </div>
                                      </v-tooltip>
                                    </v-col>
                                  </v-row>
                                </v-card>
                              </v-card-text >
                            </v-card>
                          </v-stepper-content>
                        </v-stepper-items>
                      </v-stepper>
                    </v-col>
                    <v-col cols="6"  md="6" class="py-0" >
                    </v-col>
                  </v-row>
                </v-col>
              </v-card>
            </v-dialog>
            <v-dialog
              v-model="dialog_confirm"
              max-width="600"
            >
              <v-card>
                <v-card-title>
                  <center>¿Esta seguro que quiere rehacer el proceso de importación?</center>
                  <br>
                  <br> <small class='caption'>Al rehacer se borraran todos los datos ingresados</small>
                </v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="red darken-1"
                    text
                    @click="dialog_confirm=false"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    color="green darken-1"
                    text
                    @click="closePayment()"
                  >
                    Aceptar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog
              v-model="dialog_confirm_import"
              max-width="500"
            >
              <v-card>
                <v-card-title>
                  Esta seguro de realizar la importación?
                </v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="red darken-1"
                    text
                    @click="dialog_confirm_import=false"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    color="green darken-1"
                    text
                    :loading="loading_importacion"
                    @click="importPayment()"
                  >
                    Aceptar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </template>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "payment-ImportExport",
  data: () => ({
    e1: 1,
  
     steps: 6,

 fav: true,
      menu: false,
      message: false,
      hints: true,



  bus: new Vue(),
  importacion:false,
  loading_importacion:false,
  reporte_comando_loading:false,
  reporte_senasir_loading:false,
  validar_datos:false,
  dialog: false,
  dialog_confirm : false,
  dialog_confirm_import:false,
  aux_period:null,
  title: null,
  dialog1: false,
  tab: null,
  import_export: {
    file: null,
    state_affiliate: null,
    cutoff_date: null,
  },
  meses: [ 'ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'
  ],
  actions: [
    { nameTab: "Exportación", value: "export" },
    { nameTab: "Importación", value: "import" },
  ],
  state_affiliate: [
    { name: "Activo - Comando", value: 'C' },
    { name: "Pasivo - Senasir", value: 'S' },
  ],
  paymentsBatch: [],
  datos: [],
  import_payments: {
    automatic: 0,
    no_automatic: 0,
  },
  period_year:null,
  mes:null,
  year:[],
  month:[],
  visible: false,
  loading_rpb: false,
  loading_ipb: false,
  aux:null,
  percentage:0

  }),
    watch: {
    steps (val) {
      if (this.e1 > val) {
        this.e1 = val
      }
    },
    /*'loanTypeSelected.id': function(newVal, oldVal){
      if(newVal!= oldVal)
      this.loanTypeSelected.id = this.modalidad_refi_repro_remake
      //alert ('steps' + this.loanTypeSelected.id)
    },*/
  },
  beforeMount() {
    this.getYear();
  },
  methods: {
      nextStep (n) {
      if (n == this.steps) {
        this.e1 = 1
      }
      else {
        if(n==1)
        {
          this.percentage= this.percentage + 30
        }
        if(n==2)
        {
          this.percentage= this.percentage + 30
        }
        this.e1 = n + 1
      }
    },

    clearInputs() {
      this.import_export.file = null
      this.loading_rpb =false
      this.loading_ipb =false
      this.loading_importacion =false
      this.loading =false
      this.percentage=0
      this.import_export.reg_copy = 0
      this.import_export.reg_group = 0
    },

    async uploadFilePayment() {
      const formData = new FormData();
      formData.append("file", this.import_export.file);
      formData.append("state", this.import_export.state_affiliate);
      this.loading_ipb = true
      await axios({
        url: "/loan_payment/upload_file_payment",
        method: "POST",
        headers: { Accept: "application/vnd.ms-excel" },
        data: formData,
      })
        .then((response) => {
          console.log(response.data);
           this.validar_datos=response.data.validate
           if(this.validar_datos){
              this.import_export.reg_copy = response.data.message[0].count
              this.toastr.success('Datos efectivizados '+ response.data.message[0].count)
           }
           else{
            this.toastr.error(response.data.message)
           }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    async getYear() {
      try {
        this.loading = true;
        let res = await axios.get(`get_list_year`);
        this.year = res.data;
      } catch (e) {
        this.dialog = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
      Onchange(){
      if(this.period_year!=null)
      {
         this.getMonthYear()
      }
    },
    async getMonthYear() {
      try {
         let res = await axios.get(`get_list_month`,{
          params:{
            year:this.period_year
          }
        });
        this.month = res.data
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async savePeriod() {
      try {
         let res = await axios.post(`periods`);
         if(res.data.message){
            this.toastr.error(res.data.message)
         }else{
            this.month.push(res.data.month)
            this.period_year=res.data.year

            this.mes= res.data.id
            this.getMonthYear()
            this.$forceUpdate();
         }
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async importacionComando(month, id){
      try {
        let res = await axios.get(`periods/${id}`)
        if(res.data.import_command){
          this.toastr.error('Este periodo ya fue Importado')
        }else{
          this.aux_period= month
          this.mes=id
          this.dialog=true
          this.import_export.state_affiliate = 'C'
          this.title= 'COMANDO'
          let resp = await axios.post(`loan_payment/import_progress_bar`,{
            period_id: this.mes,
            origin: this.import_export.state_affiliate
          });
          if(resp.data.percentage > 0){
            this.percentage=resp.data.percentage
            if(resp.data.query_step_1 == true)
            {
              this.e1=2
              this.validar_datos=true
            }else{
              if(resp.data.query_step_2 == true){
                this.e1=3
                this.importacion=true
              }
            }
            this.import_export.reg_copy = resp.data.reg_copy
            this.import_export.reg_group = resp.data.reg_group
          }else{
            this.percentage=0
            this.e1=1
          }
        }
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async importacionSenasir(month, id){
        try {
        let res = await axios.get(`periods/${id}`)
        if(res.data.import_senasir){
          this.toastr.error('Este periodo ya fue Importado')
        }else{
          this.aux_period= month
          this.mes=id
          this.dialog=true
          this.import_export.state_affiliate = 'S'
          this.title= 'SENASIR'
          let resp = await axios.post(`loan_payment/import_progress_bar`,{
            period_id: this.mes,
            origin: this.import_export.state_affiliate
          });
          if(resp.data.percentage > 0){
            this.percentage=resp.data.percentage
            if(resp.data.query_step_1 == true)
            {
              this.e1=2
              this.validar_datos=true
            }else{
              if(resp.data.query_step_2 == true){
                this.e1=3
                this.importacion=true
              }
            }
            this.import_export.reg_copy = resp.data.reg_copy
            this.import_export.reg_group = resp.data.reg_group
          }else{
            this.percentage=0
            this.e1=1
          }
        }
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async validateFilePayment(){
    try {

      let res = await axios.get(`agruped_payments`,{
        params:{
        origin: this.import_export.state_affiliate,
        period: this.mes
        }
      })
       if(res.data.validated_agroup){
        this.importacion =res.data.validated_agroup
        this.import_export.reg_group = res.data.count_affilites
        this.toastr.success(res.data.message +' '+'cantidad de afiliados '+res.data.count_affilites)
      }
      else{
         this.importacion =res.data.validated_agroup

          const formData = new FormData();

         await axios({
          url: '/upload_fail_validated_group',
          method: "GET",
          responseType: "blob", // important
          headers: { Accept: "application/vnd.ms-excel" },
          //headers: { Accept: "text/plain" },
          data: formData,
          params: {
            origin: this.import_export.state_affiliate,
            period: this.mes
          }
        })
          .then((response) => {
            console.log(response.data);
             const url = window.URL.createObjectURL(new Blob([response.data]));
           const link = document.createElement("a");
           link.href = url;
           link.setAttribute("download", "ReporteAfiliadosNoImportados.xls");
           document.body.appendChild(link);
           link.click();
         })
           this.toastr.error(res.data.message)
      }

      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async importPayment(){
    try {
      this.loading_importacion = true
      if( this.loading_importacion == true)
      {
        if(this.import_export.state_affiliate=='C'){
          let res = await axios.get(`create_payments_command`,{
            params:{
              period: this.mes
          }
        })
        if(res.data.importation_validated){
          if(res.status==201 || res.status == 200){
            this.dialog=false
            this.dialog_confirm_import=false
            this.toastr.success('Importado Correctamente: '+res.data.paid_by_lenders+ ' titulares y '+ res.data.paid_by_guarantors+' garantes' )
          }
        }else{
          this.dialog_confirm_import=false
          this.toastr.error(res.data.message)
        }
      }else{
        let res = await axios.get(`importation_payments_senasir`,{
        params:{
          period: this.mes
        }
      })
      if(res.data.importation_validated){
        if(res.status==201 || res.status == 200){
          this.dialog=false
          this.dialog_confirm_import=false
          this.toastr.success('Importado Correctamente: '+res.data.paid_by_lenders+ ' titulares y '+ res.data.paid_by_guarantors+' garantes' )
        }
      }
      else{
        this.toastr.error(res.data.message)
      }
     }
      }
        this.importacion=false
        this.validar_datos=false
        this.clearInputs()
       } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async closePayment(){
        let res = await axios.get(`rollback_copy_groups_payments`,{
        params:{
          origin: this.import_export.state_affiliate,
          period: this.mes
        }
      })
      if(res.data.validated_rollback){
          this.toastr.success(res.data.message)
      }
      else{
        this.toastr.error(res.data.message)
      }
      this.clearInputs()

      this.e1=1
      this.dialog_confirm=false
      this.validar_datos=false
    },
    close()
    {
      this.clearInputs()
    },
    async reporteComandoSenasir(id, tipo){
    try {
          if(tipo=='C')
          {
            this.reporte_comando_loading = true
          }
          else{
            this.reporte_senasir_loading = true
          }
          if(this.reporte_comando_loading==true || this.reporte_senasir_loading ){

          const formData = new FormData();

         await axios({
          url: '/report_amortization_importation_payments',
          method: "GET",
          responseType: "blob", // important
          headers: { Accept: "application/vnd.ms-excel" },
          //headers: { Accept: "text/plain" },
          data: formData,
          params: {
            origin: tipo,
            period: id
          }
        })
          .then((response) => {
           const url = window.URL.createObjectURL(new Blob([response.data]));
           const link = document.createElement("a");
           link.href = url;
           link.setAttribute("download", "ReporteImportacion.xls");
           document.body.appendChild(link);
           link.click();
         if(response.status==201 || response.status == 200){
              this.reporte_comando_loading=false
              this.reporte_senasir_loading=false
            }
        })

          }
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async solicitudComando(tipo, id){
      try {
        const formData = new FormData();

         await axios({
          url: '/report_request_institution',
          method: "GET",
          responseType: "blob", // important
          headers: { Accept: "application/vnd.ms-excel" },
          //headers: { Accept: "text/plain" },
          data: formData,
          params: {
            origin: tipo,
            period: id
          }
        })
          .then((response) => {
           const url = window.URL.createObjectURL(new Blob([response.data]));
           const link = document.createElement("a");
           link.href = url;
           link.setAttribute("download", "SolicitudComando.xls");
           document.body.appendChild(link);
           link.click();
        })

      }
       catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
       async solicitudSenasir(tipo, id){
        try {
        const formData = new FormData();

         await axios({
          url: '/report_request_institution',
          method: "GET",
          responseType: "blob", // important
          headers: { Accept: "application/vnd.ms-excel" },
          //headers: { Accept: "text/plain" },
          data: formData,
          params: {
            origin: tipo,
            period: id
          }
        })
          .then((response) => {
           const url = window.URL.createObjectURL(new Blob([response.data]));
           const link = document.createElement("a");
           link.href = url;
           link.setAttribute("download", "SolicitudSenasir.xls");
           document.body.appendChild(link);
           link.click();
        })
      } catch (e) {
        this.loading = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
