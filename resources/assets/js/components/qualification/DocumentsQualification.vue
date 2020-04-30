<template>
  <v-container fluid>
    <v-card>
      <v-data-iterator :items="typesRequired" hide-default-footer>
        <template v-slot:header>
          <v-toolbar class="mb-0" color="ternary" dark flat>
            <v-toolbar-title>DOCUMENTOS PRESENTADOS</v-toolbar-title>
          </v-toolbar>
          <v-toolbar-title class="align-end font-weight-black text-center ma-0 pa-0 pt-5">
            <h3>Documentos Requeridos</h3>
          </v-toolbar-title>
          <v-row>
            <v-col v-for="(req,i) in typesRequired" :key="i" cols="12" class="py-1">
              <v-card dense>
                <v-col cols="12" class="py-0">
                  <v-list dense class="py-0">
                    <v-list-item class="py-0">
                      <v-col cols="1" class="py-0">
                        <v-list-item-content class="align-end font-weight-light">
                          <div>
                            <h3>{{i+1}}</h3>
                          </div>
                        </v-list-item-content>
                      </v-col>
                      <v-col cols="10" class="py-0 ml-n8">
                        <v-list-item-content
                          class="align-end font-weight-light py-0"
                        >{{ (documentsList.find((item) => item.id === req)).name }}</v-list-item-content>
                      </v-col>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-card>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>

      <v-data-iterator :items="typesOptional" hide-default-footer>
        <template v-slot:header>
          <v-toolbar-title class="align-end font-weight-black text-center ma-0 pa-0 pt-5">
            <h3>Documentos Adicionales</h3>
          </v-toolbar-title>
          <v-row>
            <v-col v-for="(opt,i) in typesOptional" :key="i" cols="12" class="py-1">
              <v-card dense>
                <v-col cols="12" class="py-0">
                  <v-list dense class="py-0">
                    <v-list-item class="py-0">
                      <v-col cols="1" class="py-0">
                        <v-list-item-content class="align-end font-weight-light">
                          <div>
                            <h3>{{i+1}}</h3>
                          </div>
                        </v-list-item-content>
                      </v-col>
                      <v-col cols="10" class="py-0 ml-n8">
                        <v-list-item-content
                          class="align-end font-weight-light py-0"
                        >{{ (documentsList.find((item) => item.id === opt)).name }}</v-list-item-content>
                      </v-col>
                    </v-list-item>
                  </v-list>
                </v-col>
              </v-card>
            </v-col>
          </v-row>
        </template>
        <template>
          <v-toolbar-title class="align-end font-weight-black text-left ma-0 pl-8 pt-5">
            <h5>Otros Documentos</h5>
          </v-toolbar-title>
          <v-row>
            <v-col cols="12" class="ma-0 px-10">
              <div
                class="align-end font-weight-light ma-0 pa-0"
                v-for="(note, index) of notes"
                :key="index"
              >
                {{index+1 +". "}} {{note.message}}
                <v-divider></v-divider>
              </div>
            </v-col>
          </v-row>
        </template>
      </v-data-iterator>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: "DocumentsQualification",
  data: () => ({
    documentsList: [],
    modality_id: null,
    typesRequired: [],
    typesOptional: [],
    notes: []
  }),

  beforeMount() {
    this.getDocumentsSubmitted(this.$route.params.id);
    this.getLoan(this.$route.params.id);
    this.getNotes(this.$route.params.id);
  },

  methods: {
    async getDocumentsSubmitted(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}/document`);
        this.documentsList = res.data;
        console.log(this.documentsList);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getLoan(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}`);
        this.modality_id = res.data.procedure_modality_id;
        console.log("ID_MODALIDAD " + this.modality_id);
        this.getRequirement(this.modality_id);
        //this.typeReq()
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getRequirement(id) {
      try {
        this.loading = true;
        let res = await axios.get(`procedure_modality/${id}/requirements`);
        console.log("el id es " + id);
        this.requirement = res.data;
        this.required = this.requirement.required;
        this.optional = this.requirement.optional;
        this.classifyRequirement();
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getNotes(id) {
      try {
        this.loading = true;
        let res = await axios.get(`loan/${id}/note`);
        this.notes = res.data;
        console.log("NOTES  " + this.notes);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    classifyRequirement() {
      let requeridos = [];
      let opcionales = [];
      for (let i = 0; i < this.documentsList.length; i++) {
        let currentId = this.documentsList[i].id;
        console.log("CURRENTID" + currentId);
        this.optional.find(item =>
          item.id === currentId
            ? opcionales.push(currentId)
            : requeridos.push(currentId)
        );
        this.typesRequired = requeridos;
        this.typesOptional = opcionales;
        console.log("OPCIONALES" + this.typesOptional);
        console.log("REQURFRIDOS" + this.typesRequired);
      }
    }
  }
};
</script>