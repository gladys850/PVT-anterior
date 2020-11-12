<template>
  <v-flex xs12 class="px-3">
    <div class="text-center">BOLETAS DE PAGO DEL CODEUDOR AFILIADO</div>
    <v-form>
      <v-row justify="center">
        <v-col cols="12">
          <v-container class="py-0">
            <v-row>
              <v-col class="text-center" cols="12" md="8" v-show="exist_codebtor" v-if="ver">
                <h2 class="success--text">ES AFILIADO DE LA MUSERPOL</h2>
              </v-col>
              <v-col class="text-center" cols="12" md="8" v-show="!exist_codebtor" v-if="ver">
                <h2 class="error--text">NO ES AFILIADO DE LA MUSERPOL</h2>
              </v-col>
              <v-col class="text-center" cols="12" md="8" v-if="!ver"></v-col>
              <v-col cols="12" md="3">
                <v-text-field
                  dense
                  label="C.I. CODEUDOR"
                  v-model="affiliate_codebtor_ci"
                  class="py-0"
                  single-line
                  hide-details
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="1">
                <v-tooltip>
                  <template v-slot:activator="{ on }">
                    <v-btn fab dark x-small v-on="on" color="info" @click.stop="searchCodebtor()">
                      <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                  </template>
                </v-tooltip>
              </v-col>
              <v-col>
                <v-data-table
                  dense
                  :headers="headers1"
                  :items="contrib_codebtor"
                  sort-by
                  class="elevation-1 ma-0 pa-3 pb-6"
                  hide-default-footer
                >
                  <template v-slot:top>
                    <v-dialog v-model="dialog" max-width="500px">
                      <v-card>
                        <v-card-title class="pa-0">
                          <v-toolbar dense flat color="">
                            <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                            <v-spacer></v-spacer>
                          </v-toolbar>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="py-0">
                          <v-container>
                            <v-row>
                              <v-col cols="12" sm="12" md="12" style="color: teal">
                                <h3>CI: {{editedItem.identity_card}}</h3>
                                <h3>NOMBRE: {{editedItem.full_name}} </h3>
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.payable_liquid"
                                  dense
                                  label="Liquido Pagable"
                                ></v-text-field>
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.border_bonus"
                                  dense
                                  label="Bono Frontera"
                                ></v-text-field>
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.east_bonus"
                                  dense
                                  label="Bono Oriente"
                                ></v-text-field>
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.seniority_bonus"
                                  dense
                                  label="Bono Cargo"
                                ></v-text-field>
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.public_security_bonus"
                                  dense
                                  label="Bono Seguridad Ciudadana"
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-container>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions class="ma-0 pb-0">
                        <v-spacer></v-spacer>
                          <v-btn color="error" dense text @click.stop="close()">Cerrar</v-btn>
                          <v-btn color="success" dense text @click="save">Guardar</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </template>
                  <template v-slot:item.request_date="{ item }">{{ item.request_date | date }}</template>
                  <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2" color="blue" @click="editItem(item)">mdi-pencil</v-icon>
                    <v-icon small color="error" @click="deleteItem(item)">mdi-delete</v-icon>
                  </template>
                  <template v-slot:no-data>
                    <!--<v-btn color="primary" @click=" initialize">Reset</v-btn>-->
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
    </v-form>
  </v-flex>
</template>
<script>
export default {
  name: "ballots_hipotecary",
  props: {
    modalidad: {
      type: Object,
      required: true
    },
    contrib_codebtor: {
      type: Array,
      required: true
    },
    affiliate: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    affiliate_codebtor_ci: null,
    editar: true,
    bonos: {},
    payable_liquid: {},
    exist_codebtor: true,
    ver: false,
    dialog: false,
    headers1: [
      {
        text: "id",
        class: ["normal", "white--text"],
        sortable: false,
        value: "id_affiliate"
      },
      {
        text: "CI",
        class: ["normal", "white--text"],
        sortable: false,
        value: "identity_card"
      },
      {
        text: "Nombre Afiliado",
        class: ["normal", "white--text"],
        sortable: false,
        value: "full_name"
      },
      {
        text: "Liquido Pagable",
        class: ["normal", "white--text"],
        value: "payable_liquid"
      },
      {
        text: "Bono Frontera",
        class: ["normal", "white--text"],
        value: "border_bonus"
      },
      {
        text: "Bono Oriente",
        class: ["normal", "white--text"],
        value: "east_bonus"
      },
      {
        text: "Bono Cargo",
        class: ["normal", "white--text"],
        value: "seniority_bonus"
      },
      {
        text: "Bono Seguridad Ciudadana",
        class: ["normal", "white--text"],
        value: "public_security_bonus"
      },
      {
        text: "Actions",
        class: ["normal", "white--text"],
        value: "actions",
        sortable: false
      }
    ],
    editedIndex: -1,
    editedItem: {
      id_affiliate: null,
      identity_card: null,
      first_name:null,
      second_name:null,
      last_name: null,
      mothers_last_name: null,
      full_name:null,
      payable_liquid: 0,
      border_bonus: 0,
      east_bonus: 0,
      seniority_bonus: 0,
      public_security_bonus: 0
    },
    defaultItem: {
      id_affiliate: "por defecto",
      identity_card: null,
      first_name:null,
      second_name:null,
      last_name: null,
      mothers_last_name: null,
      full_name:null,
      payable_liquid: 0,
      border_bonus: 0,
      east_bonus: 0,
      seniority_bonus: 0,
      public_security_bonus: 0
    }
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Boleta" : "Editar Boleta"
    }
  },
  watch: {
    dialog(val) {
      val || this.close()
    }
  },
  beforeMount() {
    this.getBallots(this.$route.query.affiliate_id)
  },
  methods: {
    //Metodo para sacar boleta de un afiliado
    async getBallots(id) {
      try {
        let data_ballots = []
        let res = await axios.get(`affiliate/${id}/contribution`, {
          params: {
            city_id: this.$store.getters.cityId,
            sortBy: ["month_year"],
            sortDesc: [1],
            per_page: this.modalidad.quantity_ballots,
            page: 1
          }
        })
        console.log(res)
        data_ballots = res.data.data
        if (res.data.valid) {
          this.editar = false
          //console.log("RESULTADOS DE BALLOT")
          //console.log(this.modalidad.quantity_ballots)
          //console.log(res.data)
          //console.log(data_ballots)
          this.editedItem.id_affiliate = data_ballots[0].affiliate_id
          this.editedItem.payable_liquid = data_ballots[0].payable_liquid
          this.editedItem.border_bonus = data_ballots[0].border_bonus
          this.editedItem.east_bonus = data_ballots[0].east_bonus
          this.editedItem.seniority_bonus = data_ballots[0].seniority_bonus
          this.editedItem.public_security_bonus = data_ballots[0].public_security_bonus
        } else {
          this.editedItem.id_affiliate = data_ballots[0].affiliate_id
          this.editedItem.payable_liquid = 0
          this.editedItem.border_bonus = 0
          this.editedItem.east_bonus = 0
          this.editedItem.seniority_bonus = 0
          this.editedItem.public_security_bonus = 0
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

    editItem(item) {
      this.editedIndex = this.contrib_codebtor.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      const index = this.contrib_codebtor.indexOf(item)
      confirm("Esta seguro que?") && this.contrib_codebtor.splice(index, 1)
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.contrib_codebtor[this.editedIndex], this.editedItem)
      } else {
        this.contrib_codebtor.push(this.editedItem)
      }
      this.close()
    },

    async searchCodebtor() {
      try {
        if(this.affiliate_codebtor_ci != this.affiliate.identity_card) {
          if(this.contrib_codebtor.length < this.modalidad.max_lenders-1){
            let resp = await axios.get(`affiliate_existence`, {
              params: {
                identity_card: this.affiliate_codebtor_ci
              }
            })
            this.affiliate_codebtor = resp.data
            this.exist_codebtor = this.affiliate_codebtor.state
            let codebtor_information = this.affiliate_codebtor.information
            this.ver = true
            if (this.exist_codebtor){
              if(codebtor_information){
                this.getBallots(this.affiliate_codebtor.affiliate.id)
                //console.log("ID DEL AFILIADO " + this.affiliate_codebtor.affiliate.id)
                this.getAffiliate(this.affiliate_codebtor.affiliate.id)
                this.dialog = true
              }else{
                this.toastr.error("No se tiene la información actualizada del Codeudor. Por favor actualice sus datos.")
              }
            }
          }else{
            this.toastr.error("La cantidad máxima de codeudores afiliados es de " + (this.modalidad.max_lenders -1) +".")
          }
        }
        else{
          this.toastr.error("El Codeudor afiliado no puede tener el mismo carnet que el titular.")
        } 
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getAffiliate(id){
      try {
        let res= await axios.get(`affiliate/${id}`)
        let data_codebtor = res.data
        this.editedItem.identity_card = data_codebtor.identity_card
        this.editedItem.first_name = data_codebtor.first_name
        this.editedItem.second_name = data_codebtor.second_name
        this.editedItem.last_name = data_codebtor.last_name
        this.editedItem.mothers_last_name = data_codebtor.mothers_last_name
        this.editedItem.full_name = data_codebtor.first_name + " " + data_codebtor.last_name + " " +data_codebtor.mothers_last_name
        console.log(data_codebtor)
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>