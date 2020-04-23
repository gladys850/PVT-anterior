<template>
  <v-container fluid>
    <v-card>
      <v-data-iterator :items="items" hide-default-footer>
        <template v-slot:header>
          <v-toolbar class="mb-0" color="ternary" dark flat>
            <v-toolbar-title>REQUISITOS PARA ANTICIPO</v-toolbar-title>
          </v-toolbar>
          <v-row>
            <v-col v-for="(group,i) in items" :key="i" cols="12" class="py-1">
              <v-card dense>
                <v-col cols="12" class="py-0" v-for="(doc,j) in group" :key="doc.id">
                  <v-list dense class="py-0">
                    <v-list-item class="py-0">
                      <!--{{'Lon='+group.length}} {{'ID='+ doc.id}}-->
                      <v-col cols="1" class="py-0">
                        <v-list-item-content class="align-end font-weight-light">
                          <div v-if="group.length == 1">
                            <h1>{{i+1}}</h1>
                          </div>
                          <div v-else>
                            <h3>{{(i+1) +'.'+(j+1)}}</h3>
                          </div>
                        </v-list-item-content>
                      </v-col>
                      <v-col cols="10" class="py-0">
                        <v-list-item-content class="align-end font-weight-light py-0">{{doc.name}}</v-list-item-content>
                      </v-col>
                      <v-col cols="1" class="py-0">
                        <div v-if="group.length == 1" class="py-0">
                          <v-checkbox
                            class="py-0"
                            color="info"
                            v-model="selected"
                            :value="doc.id"
                            @change="selectDoc1(doc.id,j,i)"
                          ></v-checkbox>
                        </div>
                        <div v-if="group.length > 1" class="py-0">
                          <v-radio-group :mandatory="false" v-model="radios[i]" class="py-0">
                            <v-radio
                              color="info"
                              :value="doc.id"
                              @change="selectDoc1(doc.id,j,i)"
                              class="py-0"
                            ></v-radio>
                          </v-radio-group>
                        </div>
                      </v-col>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-card>
            </v-col>
            <v-col cols="12" md="1" class="ma-0 pa-0">
              <!--v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    small
                    :color="'secundary'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 10px; margin-left: 6px; margin-top:-640px;"
                    @click.stop="getRequirementPrint()"
                  >
                    <v-icon>mdi-printer-settings</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Imprimir Requisitos</span>
                </div>
              </v-tooltip-->
              <!--v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    small
                    :color="'info'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 5px; margin-left: 6px; margin-top:-600px; "
                    @click.stop="getFormPrint()"
                  >
                    <v-icon>mdi-book-open-page-variant</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Gerenar Formulario</span>
                </div>
              </v-tooltip>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    small
                    :color="'success'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 5px; margin-left: 6px; margin-top:-520px;"
                    @click.stop="saveLoan()"
                  >
                    <v-icon>mdi-content-save-all</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Guardar Tramite</span>
                </div>
              </v-tooltip>
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    small
                    :color="'danger'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 5px; margin-left: 6px; margin-top:-510px;"
                    @click.stop="getContractPrint()"
                  >
                    <v-icon>mdi-file-download</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Generar Contrato</span>
                </div>
              </v-tooltip-->
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>

      <v-data-iterator :items="optional" hide-default-footer>
        <template>
          <v-toolbar-title class="align-end font-weight-black text-center ma-0 pa-0 pt-5">
            <h3>Documentos Adicionales</h3>
          </v-toolbar-title>
          <v-row>
            <v-col cols="12" class="ma-0 px-10">
              <v-autocomplete
                dense
                filled
                label="Búsque y elija el documento"
                v-model="selectedValue"
                :items="optional"
                item-text="name"
                item-value="id"
                @change="addOptionalDocument(selectedValue)"
              ></v-autocomplete>
              <div class="align-end font-weight-light">
                <div v-for="(idDoc, index) of selectedOpc" :key="index">
                  <div>
                    {{index+1 + ". "}} {{(optional.find((item) => item.id === idDoc)).name}}
                    <v-btn text icon color="error" @click="deleteOptionalDocument(index)">
                      <h2>X</h2>
                      <!--<v-icon>mdi-marker-cancel</v-icon>-->
                    </v-btn>
                    <v-divider></v-divider>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </template>
        <template>
          <v-toolbar-title class="align-end font-weight-black text-left ma-0 pl-8 pt-5">
            <h5>Otros Documentos</h5>
          </v-toolbar-title>
          <v-row>
          <v-col cols="11" class="ma-0 px-10">
              <v-text-field
                label="Registrar documento"
                v-model="newOther"
                @keyup.enter="addOtherDocument"
              ></v-text-field>
              <!--<v-btn color="primary" @click.stop="addOtherDocument">NuevoOtro</v-btn>-->
            </v-col>
            <v-col cols="1" class="ma-0 pr-10">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    small
                    :color="'success'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 0px; margin-left: 0px; margin-top:10px; "
                    @click.stop="addOtherDocument"
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Agregar documento</span>
                </div>
              </v-tooltip>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="ma-0 px-10">
              <div
                class="align-end font-weight-light ma-0 pa-0"
                v-for="(otherDoc, index) of otherDocuments"
                :key="index"
              >
                {{index+1 +". "}} {{otherDoc}}
                <v-btn text icon color="error" @click.stop="deleteOtherDocument(index)">X</v-btn>
                <v-divider></v-divider>
              </div>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>
    </v-card>
    <v-row>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-spacer></v-spacer>
      <v-col class="py-0">
        <v-btn 
        text 
        @click="beforeStepBus(5)">Atras</v-btn>
        <v-btn 
        color="primary" 
        @click.stop="saveLoan()">Finalizar</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import { Validator } from "vee-validate";
export default {
  inject: ["$validator"],
  name: "loan-requirement",
  data: () => ({
    itemsPerPage: 10,
    items: [],
    optional: [],
    requirement: [],
    index: [],
    prueba: null,
    cont: 0,
    checks: [],
    selectedOpc: [],
    selected: [],
    radios: [],
    selectedValue: null,
    idRequirements: [],
    otherDocuments: [],
    newOther: null
  }),
  props: {
    datos: {
      type: Object,
      required: true
    },
    formulario: {
      type: Array,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    },
    intervalos: {
      type: Object,
      required: true
    },
    calculos: {
      type: Object,
      required: true
    },
    bus: {
      type: Object,
      required: true
    }
  },
  beforeMount() {
    this.getRequirement(33);
  },
  methods: {
    beforeStepBus(val) {
      this.bus.$emit("beforeStepBus", val)
    },
    selectDoc1(id) {
      setTimeout(() => {
        //console.log("ID=" + id + " J=" + j + " I=" + i);
        //console.log(this.selected + "=>vector ckeck");
        //console.log(this.radios.filter(Boolean) + "=>vector radio");
      }, 500);
    },
    async getRequirement(id) {
      try {
        this.loading = true;
        let res = await axios.get(`procedure_modality/${id}/requirement`);
        this.requirement = res.data;
        this.items = this.requirement.required;
        this.optional = this.requirement.optional;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getRequirementPrint() {
      try {
        let res = await axios.get(`loan/print/requirements`, {
          params: {
            lenders: [this.$route.query.affiliate_id],
            procedure_modality_id: this.modalidad.id,
            city_id: this.$store.getters.cityId,
            amount_requested: this.datos.monto,
            loan_term: this.datos.plazo
          },
          responseType: "arraybuffer"
        });
        let blob = new Blob([res.data], {
          type: "application/pdf"
        });
        printJS(window.URL.createObjectURL(blob));
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getFormPrint() {
      try {
        let res = await axios.get(`loan/${8}/print/form`, {
          params:{
            copies: 2
          },
          responseType: 'arraybuffer'
        })
        let blob = new Blob([res.data], {
          type: "application/pdf"
        })
        printJS(window.URL.createObjectURL(blob))
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async saveLoan() {
      try {
        this.idRequirements = this.selected.concat(this.radios.filter(Boolean))
        if (this.idRequirements.length === this.items.length) {
          let res = await axios.post(`loan`, {
            copies: 2,
            responseType: "arraybuffer",
            lenders: [this.$route.query.affiliate_id],
            guarantors: [],
            disbursable_id: this.$route.query.affiliate_id,
            disbursable_type: "affiliates",
            procedure_modality_id: this.modalidad.id,
            amount_requested: this.calculos.montos,
            city_id: this.$store.getters.cityId,
            loan_term: this.calculos.plazo,
            disbursement_type_id: this.formulario[0],
            account_number: this.formulario[1],
            loan_destiny_id: this.formulario[2],
            documents: this.selectedOpc.concat(this.selected.concat(this.radios.filter(Boolean))),
            notes: this.otherDocuments
          });
          printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            base64: true
          })
          this.$router.push('/loan')
        } else {
          this.toastr.error("Falta seleccionar requisitos, todos los requisitos deben ser presentados."
          )
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getContractPrint() {
      try {
        let res1 = await axios.get(`loan/${8}/print/contract`, {
          responseType: 'arraybuffer'
        })
        let res2 = await axios.get(`loan/${8}/print/documents`, {
          responseType: 'arraybuffer'
        })
        let blob1 = new Blob([res1.data], {
          type: "application/pdf"
        })
        let blob2 = new Blob([res2.data], {
          type: "application/pdf"
        })
        printJS(window.URL.createObjectURL(blob1))
        printJS(window.URL.createObjectURL(blob2))
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    addOptionalDocument(i) {
      //Verifica si no encuentra el valor repetido

      if (this.selectedOpc.indexOf(i) === -1) {
        this.selectedOpc.push(i);
        //console.log("I= " + i);
        //console.log("selectedOpc " + this.selectedOpc);
      }
      this.selectedValue = " ";
    },
    deleteOptionalDocument(i) {
      this.selectedOpc.splice(i, 1);
    },
    addOtherDocument() {
      if (this.newOther) {
        this.otherDocuments.push(this.newOther);
        console.log("other " + this.otherDocuments);
        this.newOther = "";
      } else {
        console.log("elemento vacio");
      }
    },
    deleteOtherDocument(i) {
      this.otherDocuments.splice(i, 1);
      console.log("other " + this.otherDocuments);
    }
  }
};
</script>