<template>
  <v-container >
    <v-card>
      <v-card-text >
        <v-row justify="center" class="ma-0 pb-0">
          <v-col cols="12" class="ma-0 pb-0">
            <v-container class="py-0">
              <v-col cols="12" md="12">
                <v-layout row wrap>
                  <v-flex xs12 class="px-2" >
                    <fieldset class="pa-2">
                      <center>
                       <v-toolbar-title>{{garantes.modality.name}}</v-toolbar-title>
                      </center>
                      <v-progress-linear></v-progress-linear>
                      <template>
                        <v-row>
                          <v-col cols="3" class="ma-0 py-2">
                            <label><b>Nro Prestamos:</b></label>
                             {{garantes.code}}
                          </v-col>
                          <v-col cols="3" class="ma-0 py-2">
                            <label><b>Fecha de Desembolso:</b></label>
                            {{garantes.disbursement_date | date}}
                          </v-col>
                          <v-col cols="3" class="ma-0 py-2">
                            <label><b>Monto Desembolsado:</b></label>
                            {{ garantes.amount_approved | moneyString}}
                          </v-col>
                            <v-col cols="3" class="ma-0 py-2">
                            <label><b>Plazo :</b></label>
                              {{ garantes.loan_term +' Meses'}}
                          </v-col>
                          <v-col cols="12" md="12" class="py-0">
                            <p style="color:teal"><b>PRESTATARIO.-</b></p>
                          </v-col>
                          <ul style="list-style: none" class="py-0" >
                            <li v-for="borrower in garantes.borrower" :key="borrower.id">
                              <v-col cols="12" md="12" class="pa-0">
                                <v-row class="pa-0">
                                   <v-col cols="12" md="4" class="py-0">
                                    <p><b>NOMBRE:</b> {{$options.filters.fullName(borrower, true)}}</p>
                                  </v-col>
                                  <v-col cols="12" md="4" class="py-0">
                                    <p><b>CÉDULA DE IDENTIDAD:</b> {{borrower.identity_card +' '+  borrower.city_identity_card.first_shortened}}</p>
                                  </v-col>
                                  <v-col cols="12" md="4" class="py-0">
                                    <p><b>ESTATDO:</b> {{borrower.state.name}}</p>
                                  </v-col>
                                  <v-col cols="12" md="12" class="py-0">
                                    <p><b>GRADO:</b> {{borrower.city_identity_card.company_address}}</p>
                                  </v-col>
                                </v-row>
                              </v-col>
                            </li>
                           </ul>
                          <v-progress-linear></v-progress-linear>
                        <v-col cols="12" class="py-0" v-show="isNew" v-if="last_payment" >
                          <center>
                            <v-toolbar-title>DATOS DEL PAGO ANTERIOR</v-toolbar-title>
                          </center>
                        </v-col>
                      <v-progress-linear v-show="isNew" v-if="last_payment"></v-progress-linear>
                      <v-col cols="3" class="ma-0 py-2"  v-show="isNew" v-if="last_payment">
                        <label><b style="color:teal" >Saldo Capital:</b></label>
                        <b style="color:teal">{{garantes.balance | moneyString}}</b>
                      </v-col>
                      <v-col cols="3" class="ma-0 py-2" v-show="isNew" v-if="last_payment">
                        <label><b style="color:teal">Número de Cuota:</b></label>
                        <b style="color:teal">{{(garantes.last_payment_validated.quota_number+1)  }}</b>
                      </v-col>
                      <v-col cols="3" class="ma-0 py-2" v-show="isNew" v-if="last_payment">
                        <label><b style="color:teal">Fecha del ultimo Pago:</b></label>
                        <b style="color:teal">{{garantes.last_payment_validated.estimated_date | date }}</b>
                      </v-col>
                      <v-col cols="3" class="ma-0 py-2" v-show="isNew" v-if="last_payment">
                        <label><b style="color:teal" >Total Pagado:</b></label>
                        <b style="color:teal">{{garantes.last_payment_validated.estimated_quota | moneyString}}</b>
                      </v-col>
                      <v-col cols="6" class="ma-0 py-2" v-show="isNew" v-if="last_payment">
                        <label><b>Intereses Corrientes Pendientes:</b></label>
                        {{garantes.last_payment_validated.interest_accumulated}}
                      </v-col>
                      <v-col cols="6" class="ma-0 py-2" v-show="isNew" v-if="last_payment">
                        <label><b>Interes Penales Pendientes:</b></label>
                        {{garantes.last_payment_validated.penal_accumulated}}
                      </v-col>
                      <v-progress-linear></v-progress-linear>
                        <v-col cols="9"  v-show="editable" v-if="permissionSimpleSelected.includes('create-payment-loan') && this.data_payment.validar">
                        </v-col>
                         <v-col cols="3" class="ma-0 py-0" v-show="permissionSimpleSelected.includes('create-payment-loan') && this.data_payment.validar" v-if="editable">
                          <v-checkbox class="ma-0 py-3"
                            :outlined="editable"
                            :readonly="!editable"
                            :disabled="ver "
                            v-model="data_payment.validated"
                            label="Validar Pago"
                          ></v-checkbox>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0"  >
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            @change="Onchange()"
                            v-model="data_payment.procedure_id"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :items="tipo_tramite"
                            item-text="name"
                            item-value="id"
                            label='Tipo de tramite'
                            :disabled="ver || editable"
                          ></v-select>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="isNew">
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
                            label="Tipo de Amortizacion"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
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
                            label="Pago del "
                            persistent-hint
                            :disabled="ver || editable"
                          ></v-select>
                        </v-col>
                         <v-col cols="2" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan') && isNew" >
                          <v-text-field
                          v-show="permissionSimpleSelected.includes('create-payment-loan')"
                            dense
                            v-model="code_initials"
                            label="Codigo "
                            :disabled="true"
                            :readonly="true"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="3" class="ma-0 pb-0" v-show="garante_show">
                            <ul style="list-style: none" class="pa-0" >
                               <li v-for="guarantor in garantes.borrowerguarantors" :key="guarantor.id" class="mb-4">
                                 {{$options.filters.fullName(guarantor, true)}}
                                 <br>
                               </li>
                            </ul>
                        </v-col>
                         <v-col cols="1" class="my-0 pb-0" v-show="garante_show">
                            <ul style="list-style: none" class="pa-0 my-0" >
                               <li v-for="guarantor in garantes.borrowerguarantors" :key="guarantor.id" class="my-0">
                                  <v-radio-group  v-model="radios" class="py-0 my-0">
                              <v-radio
                              color="info"
                               :click="generateGuarantorCode()"
                              :value="guarantor.id"
                              class="py-0  my-0"
                            ></v-radio>
                          </v-radio-group>
                               </li>
                            </ul>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan')" >
                          <v-text-field
                            dense
                            v-model="data_payment.voucher"
                            label="Codigo de Comprobante"
                            :outlined=" isNew || editable "
                            :readonly="ver"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan')" v-if="!isNew">
                           <v-text-field
                             v-model="data_payment.code"
                             :readonly="true"
                            :disabled="true"
                            dense
                            label="Codigo"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan')" v-if="!isNew" >
                           <v-text-field
                            v-model="data_payment.quota_number"
                            :readonly="true"
                            :disabled="true"
                            dense
                            label="Nro.Cuota"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4">
                          <v-text-field
                            dense
                            v-model="data_payment.payment_date"
                            hint="Día/Mes/Año"
                            class="purple-input"
                            type="date"
                            label="Fecha de Calculo"
                            :clearable="editable"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :disabled="ver || editable"
                          ></v-text-field>
                        </v-col>
                           <v-col cols="4" class="ma-0 pb-0" v-show="isNew">
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
                            label="Tipo de Pago"
                            persistent-hint
                            @change="OnchangeAmortization()"
                            :disabled="ver || editable"
                          ></v-select>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="view">
                          <v-text-field
                            dense
                            v-model="data_payment.pago_total"
                            label="Total Pagado"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :disabled="ver || editable"
                          ></v-text-field>
                        </v-col>
                          <v-col cols="4" v-show="!isNew" v-if="permissionSimpleSelected.includes('create-payment')" >
                          <v-text-field
                            v-model="data_payment.voucher_amount_total"
                            :outlined="editable"
                            :readonly="!editable"
                            label="Total Pagado en Tesoreria"
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="editable" v-if="permissionSimpleSelected.includes('create-payment')">
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
                            label="Tipo de Pago"
                            persistent-hint
                          ></v-select>
                        </v-col>
                        <v-col cols="4" v-show="!isNew" v-if="permissionSimpleSelected.includes('create-payment')" >
                          <v-text-field
                            v-model="data_payment.comprobante"
                            :outlined="editable"
                            :readonly="!editable"
                            label="Nro. de Comprobante"
                            dense
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4" v-show="permissionSimpleSelected.includes('create-payment')">
                          <v-text-field
                            dense
                            v-model="data_payment.voucher_date"
                            hint="Día/Mes/Año"
                            class="purple-input"
                            type="date"
                            label="Fecha"
                            :readonly="true"
                            :disabled="true"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="4" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment')">
                          <v-text-field
                            v-if="!ver"
                            v-model="data_payment.glosa_voucher"
                            :outlined="editable"
                            :readonly="!editable"
                            dense
                            label="Glosa"
                          ></v-text-field>
                        </v-col>
                         <v-col cols="5" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan')">
                          <v-text-field
                            v-show="isNew || editable" v-if="!ver"
                            v-model="data_payment.glosa"
                            :outlined=" isNew || editable "
                            :readonly="ver"
                            dense
                            label="Glosa"
                          ></v-text-field>
                        </v-col>
                          <v-col cols="3" class="ma-0 pb-0" v-show="permissionSimpleSelected.includes('create-payment-loan') && isNew" >
                          <v-select
                            dense
                            class="caption"
                            style="font-size: 10px;"
                            :Onchange="OnchangeAffiliate()"
                            v-model="data_payment.categori_id"
                            :outlined="isNew"
                            :readonly="!isNew"
                            :items="tipo_de_categoria"
                            item-text="name"
                            item-value="id"
                            label="Categoria "
                            persistent-hint
                            :disabled="ver || editable"
                          ></v-select>
                        </v-col>
                          <v-col cols="8" v-show="permissionSimpleSelected.includes('create-payment-loan')">
                        </v-col>
                         <v-col cols="10" v-show="isNew" class="py-0">
                        </v-col>
                        <v-col cols="2" v-show="isNew" class="py-0">
                          <v-btn
                            color="info"
                            @click="Calcular($route.query.loan_id)" v-show="!ver">
                            Calcular
                          </v-btn>
                        </v-col>
                        <AddPayment
                          :payment.sync="payment"/>
                        </v-row>
                        </template>
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
import AddPayment from '@/components/payment/AddPayment'
export default {
  name: "add-amortization",
  props: {
    data_payment: {
      type: Object,
      required: true
    },
  },
    components: {
    AddPayment
  },
  data: () => ({
    loan: {},
    radios:[],
    garante_show: false,
    tipo_tramite: [],
    regular:false,
    payment:{},
    garantes:{
      lenders:[],
      last_payment_validated:{},
      modality:{}
    },
    separa:[],
    tipo_de_amortizacion: [],
    tipo_afiliado:[],
    tipo_de_categoria:[],
    view:true,
    titular_show:true,
    code_initials:null,
    last_payment:false,
    loan_payment:{},
    payment_types:[
      {
        value:1,
        name:"Liquidar"
      },
      {
        value:0,
        name:"Regular"
      }
    ],
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
  //Metodo para obtener Permisos por rol
  permissionSimpleSelected () {
    return this.$store.getters.permissionSimpleSelected
  },
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
        if(this.$route.params.hash == 'edit')
    {
      this.getLoanPayment(this.$route.query.loan_payment)
    }
     if(this.$route.params.hash == 'view')
    {
      this.getLoanPayment(this.$route.query.loan_payment)
    }
    this.getTypeProcedure()
    this.getPymentTypes()
    this.getVoucherTypes()
    this.getCategori()
    if(this.isNew)
    {
      this.getLoan(this.$route.query.loan_id)
    }
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
    this.$forceUpdate()
  },
   watch: {
    'data_payment.payment_date': function(date) {
      this.formatDate('paymentDate', date)
    }
  },
  methods: {

    //Metodo para sacar todos los tipo de pago
      async OnchangeAmortization(){
      try {
        if(this.data_payment.affiliate_id=="G"){
          let res = await axios.get(`get_amount_payment`,{
            params:{
              loan: this.$route.query.loan_id,
              loan_payment_date: this.$moment(this.data_payment.payment_date).format('DD-MM-YYYY'),
              liquidate: this.data_payment.pago == 'Liquidar' ? 1 : 0,
              type: 'G'
            }
          })
          this.data_payment.pago_total=res.data.suggested_amount
        }else{
          let res = await axios.get(`get_amount_payment`,{
            params:{
              loan: this.$route.query.loan_id,
              loan_payment_date: this.$moment(this.data_payment.payment_date).format('DD-MM-YYYY'),
              liquidate: this.data_payment.pago == 'Liquidar' ? 1 : 0,
              type: 'T'
            }
          })
          this.data_payment.pago_total=res.data.suggested_amount
        }
         } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
      },
      //Metodo para escoger quien hace el tipo de pago "Titular o Garante"
      OnchangeAffiliate(){
      if(this.data_payment.affiliate_id=="G")
      {
        this.garante_show= true
        this.titular_show=false
      }
     else{
      this.garante_show= false
      this.titular_show=true
        for (let i = 0; i<  this.garantes.lenders.length; i++) {
          this.data_payment.affiliate_id_paid_by=this.garantes.lenders[0].id
          this.code_initials=this.garantes.lenders[0].type_initials
        }
      }
    },
    //Metodo que genera el codigo del garante
    generateGuarantorCode()
    {
      if(this.data_payment.affiliate_id=='G')
      {
        for (let i = 0; i<  this.garantes.borrowerguarantors.length; i++) {
        if(this.garantes.borrowerguarantors[i].id==this.radios)
        {
          this.data_payment.affiliate_id_paid_by=this.radios
          this.code_initials = this.garantes.borrowerguarantors[i].type_initials
        }
      }
      }else{
        this.data_payment.voucher=this.data_payment.voucher
      }
    },
      //Metodo para sacar datos del pago
     async getLoanPayment(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan_payment/${id}`)
        this.loan_payment = res.data
             this.garantes.lenders=[this.loan_payment.affiliate]
             this.garantes.code=this.loan_payment.loan.code
             this.garantes.disbursement_date=this.$moment(this.loan_payment.loan.disbursement_date).format("DD-MM-YYYY")
             this.garantes.amount_approved=this.loan_payment.loan.amount_approved
             this.garantes.loan_term=this.loan_payment.loan.loan_term
             this.garantes.estimated_quota=0
             this.garantes.balance=0
             this.garantes.last_payment_validated.previous_payment_date=0
             this.garantes.last_payment_validated.estimated_date=0
             this.garantes.modality.name = res.data.modality.name

        this.data_payment.code=this.loan_payment.code
        this.data_payment.payment_date= this.loan_payment.estimated_date
        this.data_payment.pago_total=this.loan_payment.estimated_quota
        this.data_payment.affiliate_id =this.loan_payment.paid_by
        this.data_payment.voucher=this.loan_payment.voucher
        this.data_payment.pago  =this.loan_payment.amortization_type_id
        this.data_payment.loan_id  =this.loan_payment.loan_id
        this.data_payment.validated =this.loan_payment.validated
        this.data_payment.glosa =this.loan_payment.description
        this.data_payment.procedure_modality_name =this.loan_payment.modality.procedure_type.name
        this.data_payment.procedure_id= this.loan_payment.modality.procedure_type_id
        this.data_payment.amortization=2
        this.data_payment.quota_number=this.loan_payment.quota_number
        this.data_payment.estimated_quota=this.loan_payment.estimated_quota
        this.data_payment.code=this.loan_payment.code
        this.data_payment.quota_number=this.loan_payment.quota_number
        this.data_payment.quota_number=this.loan_payment.quota_number
        this.data_payment.quota_number=this.loan_payment.quota_number
        if(this.data_payment.procedure_modality_name == 'Amortización Complemento Económico' ||
            this.data_payment.procedure_modality_name == 'Amortización Fondo de Retiro' ||
            this.data_payment.procedure_modality_name == 'Amortización por Ajuste' ||
            this.data_payment.procedure_modality_name == 'Amortización Automática')
          {
            this.data_payment.validar =true
          }else{
            if(this.data_payment.procedure_modality_name == 'Amortización Directa')
            {
              this.data_payment.validar =false
            }
          }
           this.tipo_afiliado.push(
              {
                name:"Titular",
                id:"T"
              },
              {
                name:"Garante",
                id:"G"
              })
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para cargar los tipos de amortizaciones de acuerdo al tipo de tramite
    Onchange(){
      if(this.data_payment.procedure_id!=null)
      {
         this.getTypeAmortization(this.data_payment.procedure_id)
      }
    },
    //Metodo para sacar el modulo y los tipos de tramite
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
    //Metodo para sacar las amortizaciones
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
    //Metodo para sacar la categoria del tramite
     async getCategori() {
      try {
        this.loading = true
        let res = await axios.get(`get_categorie_user`)
        this.tipo_de_categoria = res.data
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
        let res = await axios.get(`voucher_type`)
        this.payment_type_treasury = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Validaciones del paso 1
     async validatedStepOne() {
      try {
            if(this.data_payment.procedure_id)
            {
              if(this.data_payment.procedure_modality_id)
              {
                if(this.data_payment.affiliate_id)
                {
                  if(this.data_payment.pago)
                  {
                    if(this.data_payment.pago_total)
                    {
                      this.Calcular(this.$route.query.loan_id)
                    }else{
                      this.toastr.error('Debe introducir el total pagado')
                    }
                  }
                  else{
                      this.toastr.error('Debe introducir el tipo de pago')
                  }
                }else{
                  this.toastr.error('Debe seleccionar quien realiza el pago')
                }
              }else{
                this.toastr.error('Debe seleccionar el tipo de amortizacion')
              }
            }
            else{
              this.toastr.error('Debe seleccionar el tipo de tramite')
          }
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
      }},
    //Formateo de fechas
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
    //Metodo para sacar la siguiente cuota
    async Calcular(id) {
    try {
        let res = await axios.patch(`loan/${id}/payment`,{
          affiliate_id:this.data_payment.affiliate_id_paid_by,
          estimated_date:this.data_payment.payment_date,
          estimated_quota:this.data_payment.pago_total,
          liquidate : this.data_payment.liquidate,
          procedure_modality_id:this.data_payment.procedure_modality_id,
            categorie_id :this.data_payment.categori_id
        })
          this.payment = res.data
          this.payment.now_date= new Date().toISOString().substr(0, 10),
          this.$forceUpdate()
      }catch (e) {
        console.log(e)
      }finally {
        this.loading = false
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
    async getLoan(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}`)
        this.garantes=res.data
        console.log(this.garantes)
        console.log(this.garantes.modality.name)
        if(this.garantes.last_payment_validated==null)
        {
          this.garantes.last_payment_validated={}
          this.last_payment=false
        }
        else{
          this.last_payment=true
        }
        if(this.garantes.guarantors.length > 0)
        {
            this.tipo_afiliado.push(
              {
                name:"Titular",
                id:"T"
              },
              {
                name:"Garante",
                id:"G"
              })
        }else{
           this.tipo_afiliado.push(
              {
                name:"Titular",
                id:"T"
              })
              this.data_payment.affiliate_id="T"
              this.code_initials=this.garantes.lenders[0].type_initials
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>