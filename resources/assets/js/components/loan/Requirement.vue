<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
    <v-form>
    <v-card>
      <v-data-iterator :items="items" hide-default-footer>
        <template v-slot:header>
          <v-toolbar class="mb-0" color="ternary" dark flat>
            <v-toolbar-title>REQUISITOS{{reference.id}}</v-toolbar-title>
          </v-toolbar>
          <v-row>
            <v-col v-for="(group,i) in items" :key="i" cols="12" class="py-1">
              <v-card dense>
                <v-col cols="12" class="py-0" v-for="(doc,j) in group" :key="doc.id">
                  <v-list dense class="py-0">
                    <v-list-item class="py-0">
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
                      <v-col cols="10" class="py-0 ml-n8">
                        <v-list-item-content class="align-end font-weight-light py-0">{{doc.name}}</v-list-item-content>
                      </v-col>
                      <v-col cols="1" class="py-0 my-n1">
                        <div v-if="group.length == 1" class="py-0">
                          <v-checkbox
                            class="py-0"
                            color="info"
                            v-model="selected"
                            :value="doc.id"
                          ></v-checkbox>
                        </div>
                        <div v-if="group.length > 1" class="py-0 my-n1">
                          <v-radio-group :mandatory="false" v-model="radios[i]" class="py-0">
                            <v-radio
                              color="info"
                              :value="doc.id"
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
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>

      <v-data-iterator :items="optional" hide-default-footer>
        <template>
          <v-toolbar-title class="align-end font-weight-black text-center ma-0 pa-0 pt-5">
            <h5>Documentos Adicionales</h5>
          </v-toolbar-title>
          <v-row>
            <v-col cols="12" class="ma-0 px-10">
              <v-autocomplete
                dense
                filled
                outlined
                shaped
                label="Búsque y elija el documento"
                 v-model="selectedOpc"
                :items="newOptional"
                item-text="name"
                item-value="id"
                @change="addOptionalDocument(selectedOpc)"
              ></v-autocomplete>
              <div class="align-end font-weight-light">
                <div v-for="(idDoc, index) of itemsOpc" :key="index">
                  <div>
                    {{index+1 + ". "}} {{(optional.find((item) => item.id === idDoc)).name}}
                    <v-btn text icon color="error" @click="deleteOptionalDocument(index,idDoc)">
                      <h2>X</h2>
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
          <v-col cols="12" class="ma-0 px-10">
            <ValidationProvider v-slot="{ errors }" name="Registrar el documento" rules="min:3">
              <v-text-field
              :error-messages="errors"
                dense
                outlined
                color="info"
                append-outer-icon="mdi-text-box-plus"
                @click:append-outer="addOtherDocument()"
                label="Registre el documento"
                v-model="newOther"
                @keyup.enter="addOtherDocument()"
              ></v-text-field>
              </ValidationProvider>
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
        @click="beforeStepBus(6)">Atras</v-btn>
        <v-btn 
        color="primary" 
        @click.stop="saveLoan()">Finalizar</v-btn>
      </v-col>
    </v-row>
    </v-form>
  </ValidationObserver>
  </v-container>
</template>
<script>

export default {
  
  name: "requirement",
  data: () => ({
    itemsPerPage: 10,
    items: [],
    optional: [],
    newOptional: [],
    requirement: [],
    index: [],
    prueba: null,
    cont: 0,
    checks: [],
    itemsOpc: [],
    selected: [],
    radios: [],
    selectedOpc: null,
    idRequirements: [],
    otherDocuments: [],
    newOther: null
  }),
  props: {
    datos: {
      type: Object,
      required: true
    },
    garantes: {
      type: Array,
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
    modalidad_id: {
      type: Number,
      required: true,
      default: 0
    },
    intervalos: {
      type: Object,
      required: true
    },
    calculos: {
      type: Object,
      required: true
    },
    reference: {
      type: Object,
      required: true
    },
    bus: {
      type: Object,
      required: true
    }
  },
  watch: {
    modalidad_id () {
      this.getRequirement(this.modalidad_id)
    }
  },
  methods: {
    beforeStepBus(val) {
      this.bus.$emit("beforeStepBus", val)
    },
    async getRequirement(id) {
      try {
        this.loading = true;
        let res = await axios.get(`procedure_modality/${id}/requirement`);
        this.requirement = res.data;
        this.items = this.requirement.required;
        this.optional = this.requirement.optional;
        this.newOptional = this.requirement.optional;
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
            procedure_modality_id:this.modalidad.id,
            amount_requested: this.calculos.montos,
            city_id: this.$store.getters.cityId,
            loan_term: this.calculos.plazo,
            payment_type_id:this.formulario[0],
            lenders:[this.$route.query.affiliate_id],
            payable_liquid_calculated:this.calculos.payable_liquid_calculated,
            bonus_calculated:this.calculos.bonus_calculated,
            liquid_qualification_calculated:this.calculos.liquid_qualification_calculated,
            indebtedness_calculated:this.calculos.indebtedness_calculated,
            guarantors: this.garantes,
            personal_reference_id: this.reference.id,
            account_number:this.formulario[1],
            destiny_id: this.formulario[2],
            documents: this.itemsOpc.concat(this.selected.concat(this.radios.filter(Boolean))),
            notes: this.otherDocuments
          });
          printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            base64: true
          })
          this.$router.push('/workflow')
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
    addOptionalDocument(i) {
      //Verifica si no encuentra el valor repetido
      if (this.itemsOpc.indexOf(i) === -1) {
        this.itemsOpc.push(i)
         //filtrar en newOptional el item agregado y generar uno array nuevo sin el item
        this.newOptional = this.newOptional.filter(item => item.id !== i)
        //console.log("I= " + i);
        //console.log("selectedOpc " + this.selectedOpc);
      }

    },
    deleteOptionalDocument(i, idDoc) {
      let itemDelete = []
      this.itemsOpc.splice(i, 1)
      this.selectedOpc = " ";
      console.log("delete "+i)
      console.log("delete "+idDoc)
      //obtener el item borrado desde optional
      itemDelete = this.optional.find(item => item.id === idDoc)
      console.log(itemDelete)
      //insertarlo en newOptional
      this.newOptional.push(itemDelete)
     
    },
    addOtherDocument() {
      //verificar si existe algun dato
      if (this.newOther) {
         //desde otherDocuments filtrar si existe un dato registrado igual a uno guardado en newOher
        if(!(this.otherDocuments.filter(item => item === this.newOther)).length > 0){
          //si no existe repetido insertar item
          this.otherDocuments.push(this.newOther);
          console.log("other " + this.otherDocuments);
          this.newOther = ""          
        }else{  
          this.toastr.error("El documento ya existe")  
        }
      } else {
        this.toastr.error("No registró ningún documento")
      }
    },
    deleteOtherDocument(i) {
      this.otherDocuments.splice(i, 1);
      console.log("other " + this.otherDocuments);
    }
  }
};
</script>