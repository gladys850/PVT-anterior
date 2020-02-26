<template>
  <v-container fluid>
    <v-card>
      <v-data-iterator :items="items" hide-default-footer>
        <template v-slot:header>
          <v-toolbar class="mb-0" color="ternary" dark flat>
            <v-toolbar-title>
              REQUISITOS PARA ANTICIPO
              <v-btn @click.stop="saveRequirement()" color="success">Guardar Requisitos</v-btn>
            </v-toolbar-title>
          </v-toolbar>
        </template>
        <template>
          <v-row>
            <v-col v-for="(group,i) in items" :key="i" cols="12" class="py-1">
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
          </v-row>
        </template>
      </v-data-iterator>

      <v-toolbar-title class="align-end font-weight-black text-center my-2" >
        <h3>Documentos Opcionales</h3>
      </v-toolbar-title>
      <v-divider></v-divider>
      <v-data-iterator
        :items="optional"
        :search="search"
      >
        <!--filter- -->
        <template v-slot:header>
          <v-toolbar color="ternary" info flat class="mb-1">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-search-web"
              label="Buscar"
            ></v-text-field>
          </v-toolbar>
        </template>

        <!--<template v-slot:default="props">
          <v-row>
            <v-col v-for="item in props.items" :key="item.name" cols="12" sm="6" md="4" lg="3">
              <v-card>
                <v-card-title class="subheading font-weight-bold">{{ item.id }}</v-card-title>

                <v-divider></v-divider>
              </v-card>
            </v-col>
          </v-row>
        </template>-->
        <!--fin filter -->
        <template v-slot:default="props">
          <v-row>
            <v-col v-for="(item,index) in props.items" :key="index" cols="12" class="py-1">
              <v-card>
                <v-list dense class="py-0">
                  <v-list-item class="py-0">
                    <v-col cols="1" class="py-0">
                      <v-list-item-content class="align-end font-weight-light">
                        <h4>{{item.id}}</h4>
                      </v-list-item-content>
                    </v-col>

                    <v-col cols="10" class="py-0">
                      <v-list-item-content class="align-end font-weight-light">{{item.name}}</v-list-item-content>
                    </v-col>

                    <v-col cols="1" class="py-0">
                      <v-checkbox
                        color="info"
                        v-model="selectedOpc"
                        :value="item.id"
                        @change="selectDoc2(item.id)"
                      ></v-checkbox>
                    </v-col>
                  </v-list-item>
                </v-list>
              </v-card>
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

    search: "",
    filter: []
  }),
  beforeMount() {
    this.getRequirement(6);
  },
  methods: {
    selectDoc1(id) {
      setTimeout(() => {
        //console.log("ID=" + id + " J=" + j + " I=" + i);
        //console.log(this.selected + "=>vector ckeck");
        console.log(this.radios.filter(Boolean) + "=>vector radio");
        console.log(
          this.selectedOpc.concat(
            this.selectedOpc.concat(this.selected.concat(this.radios.filter(Boolean)))
          )
        );
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
    async saveRequirement() {
      try {
        this.loading = true;
        await axios.post(`loan/${6}/document`, {
          documents: this.selectedOpc.concat(
            this.selected.concat(this.radios.filter(Boolean))
          )
        });
        this.toastr.success("Se guardó satisfactoriamente los requisitos");
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>