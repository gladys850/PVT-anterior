<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
                      <v-tooltip top >
          <template v-slot:activator="{ on }">
            <v-btn
            
              fab
              dark
              x-small
              :color="editable ? 'danger' : 'success'"
              top
              right
              absolute
              v-on="on"
       
              @click.stop="saveLoan()"
            >
              <v-icon v-if="editable">mdi-check</v-icon>
              <v-icon v-else>mdi-pencil</v-icon>
            </v-btn>
          </template>
          <div>
            <span v-if="editable">Guardar</span>
            <span v-else>Editar</span>
        </div>
        </v-tooltip>
          <v-row justify="center">
            <v-col cols="12">
              <v-container class="py-0">
                <v-row>
                  <slot name="title"></slot>
                  <br />
                  <v-col cols="12" md="4">
                    <v-layout row wrap>
                      <v-flex xs12 class="px-2">
                        <fieldset class="pa-3">
                          <v-toolbar-title>PRÉSTAMO</v-toolbar-title>
                          <br>
                          <p>PLAZO EN MESES: {{ calculos.plazo}}</p>
                          <p>MONTO SOLICITADO: {{ calculos.montos }}</p>
                          <p>PROMEDIO LIQUIDO PAGABLE: {{ calculos.payable_liquid_calculated}}</p>
                          <p>TOTAL BONOS: {{ calculos.bonus_calculated }}</p>
                          <p>LIQUIDO PARA CALIFICACION: {{ calculos.liquid_qualification_calculated}}</p>
                          <p>CALCULO DE CUOTA: {{ calculos.quota_calculated }}</p>
                          <p>INDICE DE ENDEUDAMIENTO: {{calculos.indebtedness_calculated }}</p>
                          <p>MONTO MAXIMO SUGERIDO : {{calculos.amount_maximum_suggested}}</p>
                        </fieldset>
                      </v-flex>
                    </v-layout>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <v-toolbar-title>GARANTE</v-toolbar-title>
                              <ul style="list-style: none" class="pa-0">
                              <li v-for="(guarantor,i) in loan.guarantors" :key="i" >
                                <br>
                                <p></p>
                                <p>CÉDULA DE IDENTIDAD: {{guarantor.identity_card}}</p> 
                                <p>NOMBRE COMPLETO: {{guarantor.first_name +" "+ guarantor.last_name}}</p>
                                <p>PORCENTAJE DE PAGO: {{guarantor.pivot.payment_percentage}} %</p>
                               
                                </li>
                              </ul>
                               <p>{{loan.guarantors}}</p>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                  <v-col cols="12" md="4" >
                    <v-card-text class="py-0">
                      <v-layout row wrap>
                        <v-flex xs12 class="px-2">
                          <fieldset class="pa-3">
                            <v-toolbar-title>DESEMBOLSO</v-toolbar-title>
                            <br>
                      <v-menu
                        v-model="dates.disbursementDate.show"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                       
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            dense
                            v-model="dates.disbursementDate.formatted"
                            label="FECHA"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"                            
                            
                          ></v-text-field> 

                        </template>
                        <v-date-picker v-model="loan.disbursement_date" no-title @input="dates.disbursementDate.show = false"></v-date-picker>
                      </v-menu><br>
                            <v-select
                              dense
                              :items="tipo_desembolso"
                              item-text="name"
                              item-value="value"
                              label="TIPO"
                              v-model="loan.payment_type_id"
                             ></v-select>
                            <v-text-field
                              :label=" loan.payment_type_id=='1'? 'NRO DE CUENTA':loan.payment_type_id=='2'? 'NRO DE CHEQUE':loan.payment_type_id=='3'?'NRO DE RECIBO':'OTRO'"
                              v-model="loan.number_payment_type"
                            ></v-text-field>
                          </fieldset>
                        </v-flex>
                      </v-layout>
                    </v-card-text>
                  </v-col>
                </v-row>
              </v-container>

            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>
export default {
  name: "specific-data-loan",
  props: {
    datos: {
      type: Object,
      required: true
    },
    bonos: {
      type: Array,
      required: true
    },
    payable_liquid: {
      type: Array,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
    calculos: {
      type: Object,
      required: true
    }
  },
   data: () => ({
    editable: false,
    reload: false,
    tipo_desembolso:[
      {
      name:'Cheque',
      value:1
      },
      {
      name:'Depósito Bancario',
      value:2
      },
      {
      name:'Efectivo',
      value:3
      }
    ],
    loan:{},
    disbursement_date: null,
    dates: {
      disbursementDate:{
        formatted: null,
        picker: false
      }
    }
  }),
  mounted() {
    this.getLoanData()
    this.formatDate('disbursementDate', this.disbursement_date)
  },
  watch:{
    'disbusersement_date':function(date){
      this.formatDate('disbursementDate', date)
    }
  },
  methods:{
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
     async getLoanData() {
      try {
        this.loading = true
        let res = await axios.get(`loan/${this.$route.params.id}`)
        this.loan = res.data
        console.log("cantidad "+ this.loan.guarantors.length)

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async saveLoan(){
      try {
          let res = await axios.patch(`loan/${this.$route.params.id}`, {
           
          payment_type_id: this.loan.payment_type_id,
          number_payment_type: this.loan.number_payment_type,
          validated: this.loan.number_payment_type

        })
        this.toastr.success('Registro guardado correctamente')
      } catch (e) {
        console.log(e)
      }

    }
  }
}
</script>