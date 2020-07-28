<template>
  <v-dialog
    v-model="dialog"
    width="900"
  >
    <v-card>
      <v-toolbar dense flat color="tertiary">
        <v-toolbar-title v-show="!observation.edit && observation.accion=='devolver'">Ver Pago</v-toolbar-title>
        <v-toolbar-title v-show="!observation.edit && observation.accion=='anular'">Nuevo Pago</v-toolbar-title>
        <v-toolbar-title v-show="observation.edit">Editar Pago</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text class="ma-0 pb-0">
        <v-container fluid class="ma-0 pb-0">
          <v-row justify="center" class="ma-0 pb-0">
            <v-col cols="12" class="ma-0 pb-0">
              <v-container class="py-0">
                <ValidationObserver ref="observer">
                  <v-form>
                    <template>
                    <v-row>
                       <v-col cols="9" class="ma-0 pb-0">
                        </v-col>
                       <v-col cols="3" class="ma-0 pb-0">
                        <label><strong>Nro de Comprobante : 0001</strong></label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                        <label>TIPO DE AMORTIZACION:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                        <v-select
                          dense
                          :readonly="observation.edit"
                          v-model="observation.observation_type_id"
                          :items="tipo_de_amortizacion"
                          item-text="name"
                          item-value="id"
                          persistent-hint
                        ></v-select>
                      </v-col>
                       <v-col cols="1">
                      </v-col>
                        <v-col cols="4">
                          <label> <strong>INTERES DE DIAS: 258931</strong></label>
                      </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                        <label>TIPO DE PAGO:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                        <v-select
                          dense
                          :readonly="observation.edit"
                          v-model="observation.observation_type_id"
                          :items="tipo_de_pago"
                          item-text="name"
                          item-value="id"
                          persistent-hint
                        ></v-select>
                      </v-col>
                         <v-col cols="1">
                      </v-col>
                      <v-col cols="4">
                        <label><strong> CAPITAL: 258931</strong></label>
                      </v-col>
                        <v-col cols="3" class="ma-0 pb-0">
                          <label>NRO DE COMPROBANTE:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                         <v-text-field
                          dense
                          :readonly="observation.edit"
                          v-model="observation.message"
                          ></v-text-field>
                       </v-col>
                      <v-col cols="1">
                      </v-col>
                      <v-col cols="4" class="ma-0 pb-0">
                        <label> <strong>PENALES DE DIAS: 0.00</strong></label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                        <label> FECHA DE PAGO:</label>
                      </v-col>
                      <v-col cols="3" class="ma-0 pb-0">
                         <v-menu
                              v-model="dates.disbursementDate.show"
                              :close-on-content-click="false"
                              transition="scale-transition"
                              offset-y
                              max-width="290px"
                              min-width="290px"
                              :disabled="!editable"

                            >
                            <template v-slot:activator="{ on }">
                              <v-text-field
                                dense
                                v-model="dates.disbursementDate.formatted"
                                 hint="Día/Mes/Año"
                                persistent-hint
                                append-icon="mdi-calendar"
                                v-on="on"

                              ></v-text-field>
                            </template>
                            <v-date-picker v-model="loan.disbursement_date" no-title @input="dates.disbursementDate.show = false"></v-date-picker>
                            </v-menu>
                      </v-col>
                         <v-col cols="1">
                      </v-col>
                      <v-col cols="4">
                        <label><strong> CAPITAL: 258931</strong></label>
                      </v-col>

                      <v-col cols="4" class="ma-0 pb-0">
                        <v-text-field
                          dense
                          :readonly="observation.edit"
                          v-model="observation.message"
                          label="Total Pagado"
                          ></v-text-field>
                      </v-col>
                      <v-col cols="8" class="ma-0 pb-0">
                        <v-text-field
                          dense
                          :readonly="observation.edit"
                          v-model="observation.message"
                          label="Glosa"
                          ></v-text-field>
                      </v-col>
                      <v-col cols="6" class="ma-0 pb-0" v-show="observation.edit">
                        <v-checkbox
                          class="ma-0 pb-0"
                          label="Subsanar Tramite"
                          v-model="observation.enabled"
                        ></v-checkbox>
                    </v-col>
                    </v-row>
                    </template>
                  </v-form>
                </ValidationObserver>
              </v-container>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions class="ma-0 pb-0">
        <v-spacer></v-spacer>
        <v-btn @click.stop="saveObservation($route.params.id)"
          color="success"
          >Guardar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>

export default {
  name: "payment-add-amortizacion",
  props: {
    bus: {
      type: Object,
      required: true
    },
    loan: {
      type: Object,
      required: true
    },
  },
  data: () => ({
    dialog: false,
    observation: {},
    tipo_de_pago: [
      {name:"Efectivo",
      id:1
      },
      {name:"Dep. en Cuenta",
      id:2
      },
      {name:"Cheque",
      id:3
      },
      {name:"Garante",
      id:4
      }
    ],
    tipo_de_amortizacion: [
      {name:"Regular",
      id:1
      },
      {name:"Total",
      id:2
      },
      {name:"Garante",
      id:3
      }
    ],
    dates: {
      disbursementDate:{
            formatted: null,
            picker: false
          }
      },
    observation_type: [],
    flow: {},
    valArea: [],
    areas: []
  }),
  beforeMount(){
    this.getObservationType()
    this.getFlow(this.$route.params.id)
  },
  mounted() {
    this.formatDate('disbursementDate', this.loan.disbursement_date)
    this.bus.$on('openDialog', (observation) => {
      this.observation = observation
      this.dialog = true
      console.log("resultado")
      console.log(observation)
    })
  },
  watch: {
    'loan.disbursement_date': function(date) {
      this.formatDate('disbursementDate', date)
    }
  },
  methods: {
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
    async adicionar() {
      if (await this.$refs.observer.validate()) {
        this.saveObservation(this.$route.params.id)
        this.bus.$emit('saveObservation', this.observation)
        this.close()
      }
    },
    close() {
      this.dialog = false
      this.observation = {}
    },
    async saveObservation(id) {
      try {
          if(this.observation.edit)
          {
            let res = await axios.patch(`loan/${id}/observation`, {
              original: {
                user_id: this.observation.user_id,
                observation_type_id: this.observation.observation_type_id,
                message: this.observation.message,
                date: this.observation.date,
                enabled: false
              },
              update: {
                enabled: this.observation.enabled
                }
            })
            this.bus.$emit('emitSaveObservation', 'vacio')//emitir SAveObservation edit, para ObserverFlow
          }else{
            if(this.observation.accion=='validar'){
                let res = await axios.patch(`loan/${id}`, {
                validated: true
              })
              this.toastr.success("Se validó el trámite correctamente.")
            }else{
              let res = await axios.post(`loan/${id}/observation`, {
              observation_type_id:this.observation.observation_type_id,
              message:this.observation.message})
              if(this.observation.accion=='devolver'){
                let res1 = await axios.patch(`loan/${id}`, {
                role_id: this.valArea
                })
                 this.toastr.success("Se devolvio el tramite correctamente.")
              }else{
                let res2 = await axios.delete(`loan/${id}`)
                let code = res2.data.code
                this.toastr.success("El trámite " + code + " fue anulado correctamente.")
              }
            }
             this.$router.push("/workflow")
          }
          this.dialog = false
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getObservationType() {
      try {
        this.loading = true
        let res = await axios.get(`module/${this.$store.getters.module.id}/observation_type`)
        this.observation_type = res.data
        console.log('estas son las observaciones'+this.observation_type)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getFlow(id) {
      try {
        let res = await axios.get(`loan/${id}/flow`)
        this.flow = res.data
        this.areas = this.$store.getters.roles.filter(o =>
          this.flow.previous.includes(o.id)
        )
      } catch (e) {
        console.log(e)
      }
    },
  }
}
</script>