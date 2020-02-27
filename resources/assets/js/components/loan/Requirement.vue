<template>
  <v-container fluid>
    <v-card>
      <v-data-iterator :items="items" hide-default-footer>
        <template v-slot:header>
          <v-toolbar class="mb-0" color="ternary" dark flat>
            <v-toolbar-title>REQUISITOS PARA ANTICIPO</v-toolbar-title>
          </v-toolbar>
        </template>
        <template>
          <v-row>
            <v-col v-for="(group,i) in items" :key="i" cols="11" class="py-1">
              <v-card>
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
                        <v-list-item-content class="align-end font-weight-light">{{doc.name}}</v-list-item-content>
                      </v-col>

                      <v-col cols="1" class="py-0">
                        <div v-if="group.length == 1">
                          <v-checkbox
                            color="info"
                            v-model="selected"
                            :value="doc.id"
                            @change="selectDoc1(doc.id,j,i)"
                          ></v-checkbox>
                        </div>

                        <div v-if="group.length > 1">
                          <v-radio-group :mandatory="false" v-model="radios[i]">
                            <v-radio color="info" :value="doc.id" @change="selectDoc1(doc.id,j,i)"></v-radio>
                          </v-radio-group>
                        </div>
                      </v-col>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-card>
            </v-col>
            <v-col cols="12" md="1" class="ma-0 pa-0">
              <v-tooltip top>
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
              </v-tooltip>
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
                    style="margin-right: 5px; margin-left: 6px; margin-top:-600px; "
                    @click.stop="getRequirementPrint()"
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
                    style="margin-right: 5px; margin-left: 6px; margin-top:-560px;"
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
                    @click.stop="getRequirementPrint()"
                  >
                    <v-icon>mdi-file-download</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Gerenar Contrato</span>
                </div>
              </v-tooltip>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>

      <v-toolbar-title class="align-end font-weight-black text-center my-2">
        <h3>Documentos Opcionales</h3>
      </v-toolbar-title>

      <v-data-iterator :items="optional" hide-default-footer>
        <template>
          <v-row>
            <v-col cols="11" class="ma-5 ma-5">
              <v-autocomplete
                dense
                filled
                label="Busqué escoja una opción"
                v-model="selectedValue"
                :items="optional"
                item-text="name"
                item-value="id"
                @change="addOptionalDocument(selectedValue)"
              ></v-autocomplete>

              <v-divider></v-divider>
              <div class="align-end font-weight-light">
                <div v-for="(idDoc, index) of selectedOpc" :key="index">
                  <div>
                    <div>
                      <div>
                        {{index+1 + ". "}} {{(optional.find((item) => item.id === idDoc)).name}}
                        <v-btn text icon color="error" @click="deleteOptionalDocument(index)">
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </div>
                      <v-divider></v-divider>
                    </div>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>
    </v-card>
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
    selectedValue: null
  }),
  props: {
    modality: {
      type: Object,
      required: true
    },
    datos: {
      type: Array,
      required: true
    }
  },
  beforeMount() {
    this.getRequirement(33);
  },
  methods: {
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
        let res = await axios.get(`procedure_modality/${id}/requirements`);
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
            procedure_modality_id: this.modality.id,
            city_id: this.$store.getters.cityId,
            amount_request: this.datos[1],
            loan_term: this.datos[2]
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
    async saveLoan() {
      try {
        let res = await axios.post(`loan`, {
          lenders: [this.$route.query.affiliate_id],
          guarantors: [],
          disbursable_id: this.$route.query.affiliate_id,
          disbursable_type: "affiliates",
          procedure_modality_id: this.modality.id,
          amount_request: 3000,
          city_id: this.$store.getters.cityId,
          loan_term: 3,
          disbursement_type_id: 1,
          amount_disbursement: 3000
        });
        this.loan = res.data;
        await axios.post(`loan/${this.loan.id}/document`, {
          documents: this.selected.concat(this.radios.filter(Boolean))
        });
        this.toastr.success("Se guardó satisfactoriamente el grabado");
        console.log(
          this.selectedOpc.concat(
            this.selected.concat(this.radios.filter(Boolean))
          )
        );
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },

    addOptionalDocument(i) {
      if (this.selectedOpc.indexOf(i) === -1) {
        this.selectedOpc.push(i);
        //console.log("I= " + i);
        //console.log("selectedOpc " + this.selectedOpc);
      }
      this.selectedValue = " ";
    },
    deleteOptionalDocument(i) {
      this.selectedOpc.splice(i, 1);
    }
  }
};
</script>