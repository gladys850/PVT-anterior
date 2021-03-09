<template>
  <v-container >
        <v-card>
          <v-card-text >
            <v-row justify="center" class="ma-0 pb-0">
              <v-col cols="12" class="ma-0 pb-0">
                <v-container class="py-0">
                  <v-col cols="12" md="12">
                  <v-layout row wrap>
                    <v-flex xs12 class="px-2">
                      <fieldset class="pa-3">
                    <ValidationObserver ref="observer">
                    <v-form>
                      <center>
                       <v-toolbar-title>AMORTIZACIONES</v-toolbar-title>
                      </center>
                      <v-progress-linear></v-progress-linear>
                      <template>
                      <v-row>
                        <v-col cols="7" class="ma-0 pb-0" v-show="!isNew">
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="!isNew">
                          <label class="caption"><strong> CODIGO DE PAGO:</strong></label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="!isNew">
                          <label class="caption"><strong>{{data_payment.code}}</strong></label>
                        </v-col>
                        <v-col cols="2" class="ma-0  pr-1">
                          <label class="caption">Tipo de Tramite: </label>
                        </v-col>
                        <v-col cols="2" class="ma-0  pr-1" v-show="!isNew">
                          <label class="caption">AMORTIZACIONES PRESTAMOS </label>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0"  v-show="isNew">
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            @change="Onchange()"
                            v-model="loanTypeSelected"
                            :outlined="!editable"
                            :readonly="editable"
                            :items="tipo_tramite"
                            item-text="name"
                            item-value="id"
                          ></v-select>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="isNew" >
                          <label class="caption" >Tipo de Amortizacion:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="isNew">
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            v-model="data_payment.procedure_modality_id"
                            :outlined="!editable"
                            :readonly="editable"
                            :items="tipo_de_amortizacion"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="1" class="ma-0 pb-0" v-show="isNew">
                          <label class="caption">Pago:</label>
                          </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="isNew">
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            v-model="data_payment.amortization"
                            :outlined="!editable"
                            :readonly="editable"
                            :items="tipo_de_pago_amortizacion"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                          <v-col cols="1" class="ma-0 pb-0" >
                          <label class="caption">Pago del :</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            :Onchange="OnchangeAffiliate()"
                            v-model="data_payment.affiliate_id"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :items="tipo_afiliado"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="garante_show">
                            <ul style="list-style: none" class="pa-0" >
                               <li v-for="guarantor in garantes.guarantors" :key="guarantor.id" class="mb-4">
                                 {{$options.filters.fullName(guarantor, true)}}
                                 <br>
                               </li>
                            </ul>
                        </v-col>
                         <v-col cols="1" class="my-0 pb-0" v-show="garante_show">
                            <ul style="list-style: none" class="pa-0 my-0" >
                               <li v-for="guarantor in garantes.guarantors" :key="guarantor.id" class="my-0">
                                  <v-radio-group  v-model="radios" class="py-0 my-0">
                              <v-radio
                              color="info"
                               :click="prueba()"
                              :value="guarantor.id"
                              class="py-0  my-0"
                            ></v-radio>
                          </v-radio-group>
                               </li>
                            </ul>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="!editable">
                          <label>Codigo de Comprobante:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="!editable">
                          <v-text-field
                            dense
                            v-model="data_payment.voucher"
                            :outlined="isNew"
                            :readonly="!isNew"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0">
                          <label class="caption"> Fecha de Calculo:</label>
                        </v-col>
                        <v-col cols="2">
                          <v-menu
                            v-model="dates.paymentDate.show"
                            :close-on-content-click="false"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                            :disabled="editable || ver"
                          >
                            <template v-slot:activator="{ on }">
                              <v-text-field
                                style="font-size: 15px;"
                                dense
                                :outlined="isNew"
                                :readonly="editable || ver"
                                v-model="dates.paymentDate.formatted"
                                hint="Día/Mes/Año"
                                persistent-hint
                                append-icon="mdi-calendar"
                                v-on="on"
                              ></v-text-field>
                            </template>
                            <v-date-picker v-model="data_payment.payment_date" no-title @input="dates.paymentDate.show = false"></v-date-picker>
                          </v-menu>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="view">
                          <label>TOTAL PAGADO:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="view">
                          <v-text-field
                            dense
                            v-model="data_payment.pago_total"
                            :outlined="isNew"
                            :readonly="editable || ver"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="1" class="ma-0 pb-0" v-show="!isNew" v-if="!$store.getters.userRoles.includes('PRE-tesoreria-cobros')" >
                           <label  >TIPO DE COBRO:</label>
                        </v-col>
                        <v-col cols="1" v-if="$store.getters.userRoles.includes('PRE-tesoreria-cobros')"></v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="isNew" >
                          <label >TIPO DE COBRO:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-if="!$store.getters.userRoles.includes('PRE-tesoreria-cobros')" >
                          <v-select
                             class="caption"
                            style="font-size: 10px;"
                            dense
                            v-model="data_payment.pago"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :items="payment_types"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable">
                          <label >TIPO DE PAGO:</label>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable">
                          <v-select
                            class="caption"
                            style="font-size: 10px;"
                            dense
                            v-model="data_payment.tipo_pago"
                            :outlined="editable"
                            :readonly="!editable"
                            :items="payment_type_treasury"
                            item-text="name"
                            item-value="id"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="2" class="ma-0 pb-0" v-show="editable">
                          <label  >NRO DE COMPROBANTE:</label>
                        </v-col>
                        <v-col cols="3" v-show="editable" >
                          <v-text-field
                            v-model="data_payment.comprobante"
                            :outlined="editable"
                            :readonly="!editable"
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="7" class="ma-0 pb-0" >
                          <v-text-field
                            v-show="editable" v-if="!ver"
                            v-model="data_payment.glosa"
                            :outlined="editable"
                            :readonly="!editable"
                            dense
                            label="Glosa"
                          ></v-text-field>
                        </v-col>
                           <v-col cols="1">
                        </v-col>
                      </v-row>
                    </template>
                  </v-form>
                </ValidationObserver>
                      </fieldset>
                    </v-flex>
                  </v-layout>
                </v-col>
              </v-container>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
  </v-container>
</template>
<script>
export default {
  name: "add-amortization",
  props: {
    data_payment: {
      type: Object,
      required: true
    },
  },
  data: () => ({
    loan: {},
    radios:[],
    garante_show: false,
    loanTypeSelected:null,
    loanTypeSelectedOne:null,
    loanTypeSelectedTwo:null,
    loanTypeSelectedThree:null,
    tipo_tramite: [],
    garantes:{
      lenders:[]
    },
    radio:null,
    codigo:null,
    separa:[],
    tipo_de_amortizacion: [],
    tipo_de_pago_amortizacion: [
      {name:"Regular",
      id:1
      },
      {name:"Total",
      id:2
      }
    ],
    tipo_afiliado:[
      {name:"Titular",
      id:"T"
      },
      {name:"Garante",
      id:"G"
      }
    ],
    view:true,
    efectivo:false,
    loan_payment:{},
    payment_types:[],
    voucher_type:[],
    payment_type_treasury:[],
    dates: {
      paymentDate:{
        formatted: null,
        picker: false,
      }
    },
  }),
   computed: {
    isNew() {
      return  this.$route.params.hash == 'new'
    },
    editable(){
       return  this.$route.params.hash == 'edit'
    },
    ver(){
       return  this.$route.params.hash == 'view'
    },
  },
  beforeMount(){
    this.getTypeProcedure()
    this.getPymentTypes()
    this.getAmortizationTypes()
    this.getVoucherTypes()
    if(this.$route.params.hash == 'view')
    {
      this.formatDate('paymentDate',this.data_payment.payment_date)
    }
      if(this.$route.params.hash == 'edit')
    {
      this.formatDate('paymentDate',this.data_payment.payment_date)
    }
  },
  mounted(){
    if(this.isNew)
    {
      this.getLoan(this.$route.query.loan_id)
    }
  },
   watch: {
    'data_payment.payment_date': function(date) {
      this.formatDate('paymentDate', date)
    }
  },
  methods: {
      OnchangeAffiliate(){
      if(this.data_payment.affiliate_id=="G")
      {
        this.garante_show= true
      }
     else{
        this.garante_show= false
        if(this.isNew)
        {
          this.data_payment.voucher=null
        }
       // this.data_payment.voucher=null
         for (let i = 0; i<  this.garantes.lenders.length; i++) {
            this.data_payment.affiliate_id_paid_by=this.garantes.lenders[0].id
         }
        console.log("garante"+ this.data_payment.affiliate_id_paid_by)}
    },
    prueba()
    {
      if(this.data_payment.affiliate_id=='G')
      {
        for (let i = 0; i<  this.garantes.guarantors.length; i++) {
        if(this.garantes.guarantors[i].id==this.radios)
        {
          this.data_payment.affiliate_id_paid_by=this.radios
          if(this.garantes.guarantors[i].first_name!=null && this.garantes.guarantors[i].second_name  && this.garantes.guarantors[i].last_name && this.garantes.guarantors[i].mothers_last_name)
          {
            this.separa[0]=this.garantes.guarantors[i].first_name
            this.separa[1]=this.garantes.guarantors[i].second_name
            this.separa[2]=this.garantes.guarantors[i].last_name
            this.separa[3]=this.garantes.guarantors[i].mothers_last_name
            this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)+this.separa[2].charAt(0)+this.separa[3].charAt(0)
          }
          else{
            if(this.garantes.guarantors[i].second_name!=null && this.garantes.guarantors[i].last_name!=null && this.garantes.guarantors[i].mothers_last_name!=null){
              this.separa[0]=this.garantes.guarantors[i].second_name
              this.separa[1]=this.garantes.guarantors[i].last_name
              this.separa[2]=this.garantes.guarantors[i].mothers_last_name
              this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)+this.separa[2].charAt(0)
          }else{
              if(this.garantes.guarantors[i].first_name!=null && this.garantes.guarantors[i].last_name!=null && this.garantes.guarantors[i].mothers_last_name!=null){
                this.separa[0]=this.garantes.guarantors[i].first_name
                this.separa[1]=this.garantes.guarantors[i].last_name
                this.separa[2]=this.garantes.guarantors[i].mothers_last_name
                this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)+this.separa[2].charAt(0)
            }else{
                if(this.garantes.guarantors[i].first_name!=null && this.garantes.guarantors[i].last_name!=null){
                  this.separa[0]=this.garantes.guarantors[i].first_name
                  this.separa[1]=this.garantes.guarantors[i].last_name
                  this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)
              }else{
                  if(this.garantes.guarantors[i].first_name!=null && this.garantes.guarantors[i].mothers_last_name!=null){
                    this.separa[0]=this.garantes.guarantors[i].first_name
                    this.separa[1]=this.garantes.guarantors[i].mothers_last_name
                    this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)
              }else{
                if(this.garantes.guarantors[i].second_name!=null && this.garantes.guarantors[i].last_name!=null){
                      this.separa[0]=this.garantes.guarantors[i].second_name
                      this.separa[1]=this.garantes.guarantors[i].last_name
                      this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)
              }else{
                  if(this.garantes.guarantors[i].second_name!=null && this.garantes.guarantors[i].mothers_last_name!=null){
                      this.separa[0]=this.garantes.guarantors[i].second_name
                      this.separa[1]=this.garantes.guarantors[i].mothers_last_name
                      this.data_payment.voucher='GAR-'+this.separa[0].charAt(0)+ this.separa[1].charAt(0)
                    }
                  }
                }
              }
            }
          }
        }
        }
      }
      }
    },
    OnchangeAmortization(){
      this.data_payment.procedure_modality_id=this.loanTypeSelectedOne
    },
    Onchange(){
      if(this.loanTypeSelected!=null)
      {
        this.data_payment.procedure_id=this.loanTypeSelected
        this.getTypeAmortization(this.loanTypeSelected)
            console.log("verdadero"+this.loanTypeSelected)
      }
      else{
        console.log("falso"+this.loanTypeSelected)
      }
    },
    async getTypeProcedure() {
      try {
        this.loading = true
        let resp = await axios.get(`module`,{
          params: {
            name: 'prestamos',
            sortBy: ['name'],
            sortDesc: ['false'],
            per_page: 10,
            page: 1
          }
        })
        this.modulo= resp.data.data[0].id
        let res = await axios.get(`module/${this.modulo}/amortization_loan`)
        this.tipo_tramite = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getTypeAmortization(id) {
      try {
        this.loading = true
        let res = await axios.get(`procedure_type/${id}/modality`)
        this.tipo_de_amortizacion = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para sacar los tipos de pagos
    async getPymentTypes() {
      try {
        this.loading = true
        let res = await axios.get(`payment_type`)
        this.payment_type_treasury = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
    //Metodo para sacar los tipos de voucher
     async getVoucherTypes() {
      try {
        this.loading = true
        let res = await axios.get(`voucher_type`)
        this.voucher_type = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para sacar los tipos de cobros
    async getAmortizationTypes() {
      try {
        this.loading = true
        let res = await axios.get(`amortization_type`)
        this.payment_types = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getLoan(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}`)
        this.garantes=res.data
        console.log('entro al get loan')
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>