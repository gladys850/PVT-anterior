<template>
  <v-flex xs12 class="px-3">
    <v-form>
      <v-row justify="center">
        <v-toolbar-title>BOLETAS DE PAGO DEL CODEUDOR</v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
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
              <v-data-table
                :headers="headers1"
                :items="contrib_codebtor"
                sort-by
                class="elevation-1"
              >
                <template v-slot:top>
                  <v-toolbar flat color="white">
                    <v-dialog v-model="dialog" max-width="500px">
                      <!--<template v-slot:activator="{ on, attrs }">
                              <v-btn
                                fab
                                dark
                                x-small
                                v-on="on"
                                color="info"
                                v-bind="attrs"
                              ><v-icon>mdi-plus</v-icon>
                              </v-btn>
                      </template>-->
                      <v-card>
                        <v-card-title class="pa-0">
                          <v-toolbar dense flat color="tertiary">
                            <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn icon @click.stop="close()">
                              <v-icon>mdi-close</v-icon>
                            </v-btn>
                          </v-toolbar>
                        </v-card-title>
                        <v-card-text class="py-0">
                          <v-container>
                            <v-row>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.id_affiliate"
                                  dense
                                  label="Nombre"
                                ></v-text-field>
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
                        <v-card-actions class="pt-0">
                          <v-spacer></v-spacer>
                          <v-btn color="blue darken-1" dense text @click="close">Cancelar</v-btn>
                          <v-btn color="blue darken-1" dense text @click="save">Guardar</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </v-toolbar>
                </template>
                <template v-slot:no-data>
                  <!--<v-btn color="primary" @click=" initialize">Reset</v-btn>-->
                </template>
              </v-data-table>
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
  props:{
    contributions1: {
      type: Array,
      required: true
    },
    contrib_codebtor:{
      type: Array,
      required: true
    }
  },
  data: () => ({
    affiliate_codebtor_ci: null,
    //editar:true,
    //monto:null,
    //plazo:null,contrib_codebtor
    //interval:[],
    //contributions1:[],
    //loanTypeSelected:null,
    //visible:false,
    //num_type:9,
    //contributions1:{},
    modalidad: {},
    bonos: {},
    payable_liquid: {},
    //modalities: {},
    //prueba: {},
    //intervalos: {},
    exist_codebtor: true,
    ver: false,
    dialog: false,
    ballots: [],
    //contrib_codebtor: [],
    //contrib_codebtor_aux: [],

    headers1: [
      {
        text: "Nombre Afiliado",
        align: "start",
        sortable: false,
        value: "id_affiliate"
      },
      { text: "Liquido Pagable", value: "payable_liquid" },
      { text: "Bono Frontera", value: "border_bonus" },
      { text: "Bono Oriente", value: "east_bonus" },
      { text: "Bono Cargo", value: "seniority_bonus" },
      { text: "Bono Seguridad Ciudadana", value: "public_security_bonus" },
      { text: "Actions", value: "actions", sortable: false }
    ],
    //desserts: [],
    editedIndex: -1,
    editedItem: {
      id_affiliate: "",
      payable_liquid: 0,
      border_bonus: 0,
      east_bonus: 0,
      seniority_bonus: 0,
      public_security_bonus: 0
    },
    defaultItem: {
      id_affiliate: "por defecto",
      payable_liquid: 0,
      border_bonus: 0,
      east_bonus: 0,
      seniority_bonus: 0,
      public_security_bonus: 0
    }
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Boleta" : "Editar Boleta";
    }
  },

  watch: {
    dialog(val) {
      val || this.close();
    }
  },
  /*created () {
      this.getBallots(id)
    },*/
  beforeMount() {
    this.getBallots(this.$route.query.affiliate_id);
  },
  mounted() {
    this.getLoanModality(this.$route.query.affiliate_id);
  },
  methods: {
    //Metodo para sacar boleta de un afiliado
    async getBallots(id) {
      try {
        let i, j;
        let res = await axios.get(`affiliate/${id}/contribution`, {
          params: {
            city_id: this.$store.getters.cityId,
            sortBy: ["month_year"],
            sortDesc: [1],
            per_page: 1,//this.modalidad.quantity_ballots,
            page: 1
          }
        });
        console.log(res)
         this.ballots = res.data.data;
        if (res.data.valid) {
          //this.editar=false
          console.log("RESULTADOS DE BALLOT")
          console.log(this.modalidad.quantity_ballots)
          console.log(res.data);         
          console.log(this.ballots);
          //for (i = 0; i < this.ballots.length; i++) {
              (this.editedItem.id_affiliate = this.ballots[0].affiliate_id),
              (this.editedItem.payable_liquid = this.ballots[0].payable_liquid),
              (this.editedItem.border_bonus = this.ballots[0].border_bonus),
              (this.editedItem.east_bonus = this.ballots[0].east_bonus),
              (this.editedItem.seniority_bonus = this.ballots[0].seniority_bonus),
              (this.editedItem.public_security_bonus = this.ballots[0].public_security_bonus);
          //}
          /*for (j = 0; j < this.ballots.length; j++) {
            this.contrib_codebtor[j].id_affiliate = this.ballots[j].affiliate_id;
            this.contrib_codebtor[j].payable_liquid = this.ballots[j].payable_liquid;

            if (j == 0) {
              (this.contrib_codebtor[j].border_bonus = this.ballots[j].border_bonus),
              (this.contrib_codebtor[j].east_bonus = this.ballots[j].east_bonus),
              (this.contrib_codebtor[j].seniority_bonus = this.ballots[j].seniority_bonus),
              (this.contrib_codebtor[j].public_security_bonus = this.ballots[j].public_security_bonus);
            } else {
              (this.contrib_codebtor[j].border_bonus = 0),
              (this.contrib_codebtor[j].east_bonus = 0),
              (this.contrib_codebtor[j].seniority_bonus = 0),
              (this.contrib_codebtor[j].public_security_bonus = 0);
            }
          }*/
        } else{
              (this.editedItem.id_affiliate = this.ballots[0].affiliate_id),
              (this.editedItem.payable_liquid = 0),
              (this.editedItem.border_bonus = 0),
              (this.editedItem.east_bonus = 0),
              (this.editedItem.seniority_bonus = 0),
              (this.editedItem.public_security_bonus = 0)
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    /*initialize () {
    this.desserts = []
  },*/

    editItem(item) {
      this.editedIndex = this.contrib_codebtor.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      const index = this.contrib_codebtor.indexOf(item);
      confirm("Esta seguro que?") && this.contrib_codebtor.splice(index, 1);
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.contrib_codebtor[this.editedIndex], this.editedItem);
      } else {
        this.contrib_codebtor.push(this.editedItem);
      }
      //this.formatear()
      this.close();
    },

    async searchCodebtor() {
      try {
        /*     if(this.affiliate_codebtor_ci==this.affiliate.identity_card) //TODO verificar y/o adicionar validaciones
        {
          this.toastr.error("El garante no puede tener el mismo carnet que el titular.")
        }
        else{*/
        let resp = await axios.get(`affiliate_existence`, {
          params: {
            identity_card: this.affiliate_codebtor_ci
          }
        });
        this.affiliate_codebtor = resp.data;
        this.exist_codebtor = this.affiliate_codebtor.state;
        this.ver = true;
        console.log("Entro al metodo de codeudor" + this.affiliate_codebtor + "==>" +this.affiliate_codebtor_ci
        );
        /*}*/
        if (this.exist_codebtor) {
          this.getBallots(this.affiliate_codebtor.affiliate.id);
          console.log("ID DEL AFILIADO " + this.affiliate_codebtor.affiliate.id
          );
          this.dialog = true;
        }
        
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    /*formatear() {
      let nuevoArray = [];
      let i;
      for (i = 0; i < this.contrib_codebtor.length; i++) {
        nuevoArray[i] = {
          affiliate_id: this.contrib_codebtor[i].id_affiliate,
          contributions: {
            payable_liquid: this.contrib_codebtor[i].payable_liquid,
            border_bonus: this.contrib_codebtor[i].border_bonus,
            east_bonus: this.contrib_codebtor[i].east_bonus,
            seniority_bonus: this.contrib_codebtor[i].seniority_bonus,
            public_security_bonus: this.contrib_codebtor[i].public_security_bonus
          }
        };
        console.log("FORMATEAR");
        console.log(nuevoArray);
      }
      //this.contrib_codebtor_aux = { liquid_calification: nuevoArray };
      this.contrib_codebtor_aux = nuevoArray;
    }*/
  }
};
</script>