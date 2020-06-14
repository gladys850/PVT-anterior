<template>
  <v-container fluid>
    <v-toolbar-title class="ma-0 pb-4 pl-8">DOCUMENTOS PRESENTADOS</v-toolbar-title>
    <v-form>
      <v-card> 
        <v-row>
          <v-col cols="12">
            <v-data-iterator :items="docsRequired" hide-default-footer>
              <template v-slot:header>
                <v-toolbar-title class="font-weight-light align-end ma-0 pa-4 pl-8 pt-0">
                  <h5>Documentos Requeridos</h5>
                </v-toolbar-title>
                <v-row>
                  <v-col v-for="(req,i) in docsRequired" :key="req.id" cols="12" class="py-1">
                    <v-card dense>
                      <v-row>
                        <v-col cols="12" class="py-0">
                          <v-list dense class="py-0">
                            <v-list-item class="py-0">
                              <v-col cols="1" class="py-0">
                                <v-list-item-content class="align-end font-weight-light">
                                  <div>
                                    <h1>{{i+1}}</h1>
                                  </div>
                                </v-list-item-content>
                              </v-col>
                              <v-col cols="8" class="py-0 ml-n8">
                                <v-list-item-content
                                  class="align-end font-weight-light py-0"
                                >{{ req.name }}</v-list-item-content>
                              </v-col>
                              <v-col cols="3" class="py-0 my-0">
                                <div
                                  class="py-0"
                                  v-if="$store.getters.userRoles.includes('PRE-revision-legal')"
                                >
                                  <v-checkbox
                                    class="py-0"
                                    color="success"
                                    v-model="req.pivot.is_valid"
                                    :disabled="editable"
                                  ></v-checkbox>
                                </div>
                                <v-spacer></v-spacer>
                              </v-col>
                            </v-list-item>
                          </v-list>
                        </v-col>
                      </v-row>
                      <v-row v-if="$store.getters.userRoles.includes('PRE-revision-legal')">
                        <v-col cols="11" class="ma-0 pl-10 pr-2">
                          <v-text-field
                            dense
                            outlined
                            color="success"
                            label="Comentario"
                            v-model="req.pivot.comment"
                            :disabled="editable"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="1" class="ma-0 pa-0">
                          <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                v-on="on"
                                icon
                                color="info"
                                class="m0-0 pt-4"
                                @click="validarDoc(req.id, req.pivot.is_valid, req.pivot.comment)"
                              >
                                <v-icon>mdi-content-save</v-icon>
                              </v-btn>
                            </template>
                            <span>Guardar validación de documento</span>
                          </v-tooltip>
                        </v-col>
                      </v-row>
                    </v-card>
                  </v-col>
                </v-row>
              </template>
            </v-data-iterator>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" v-if="docsOptional.length >0" >
            <v-data-iterator :items="docsOptional" hide-default-footer>
              <template v-slot:header>
                <v-toolbar-title class="align-end font-weight-light ma-0 pa-4 pl-8 pt-0">
                  <h5>Documentos Adicionales </h5>
                </v-toolbar-title>
                <v-row>
                  <v-col v-for="(opt,i) in docsOptional" :key="opt.id" cols="12" class="py-1">
                    <v-card dense>
                      <v-row>
                        <v-col cols="12" class="py-0">
                          <v-list dense class="py-0">
                            <v-list-item class="py-0">
                              <v-col cols="1" class="py-0">
                                <v-list-item-content class="align-end font-weight-light">
                                  <div>
                                    <h1>{{i+1}}</h1>
                                  </div>
                                </v-list-item-content>
                              </v-col>
                              <v-col cols="8" class="py-0 ml-n8">
                                <v-list-item-content
                                  class="align-end font-weight-light py-0"
                                >{{ opt.name }}</v-list-item-content>
                              </v-col>
                              <v-col cols="3" class="py-0 my-0">
                                <div
                                  class="py-0"
                                  v-if="$store.getters.userRoles.includes('PRE-revision-legal')"
                                >
                                  <v-checkbox
                                    class="py-0"
                                    color="success"
                                    v-model="opt.pivot.is_valid"
                                    :disabled="editable"
                                  ></v-checkbox>
                                </div>
                                <v-spacer></v-spacer>
                              </v-col>
                            </v-list-item>
                          </v-list>
                        </v-col>
                      </v-row>
                      <v-row v-if="$store.getters.userRoles.includes('PRE-revision-legal')">
                        <v-col cols="11" class="ma-0 pl-10 pr-2">
                          <v-text-field
                            dense
                            outlined
                            color="success"
                            label="Comentario"
                            v-model="opt.pivot.comment"
                            :disabled="editable"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="1" class="ma-0 pa-0">
                          <v-tooltip top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                v-on="on"
                                icon
                                color="info"
                                class="m0-0 pt-4"
                                @click="validarDoc(opt.id, opt.pivot.is_valid, opt.pivot.comment)"
                              >
                                <v-icon>mdi-content-save</v-icon>
                              </v-btn>
                            </template>
                            <span>Guardar validación de documento</span>
                          </v-tooltip>
                        </v-col>
                      </v-row>
                    </v-card>
                  </v-col>
                </v-row>
              </template>
              <template>
                <v-toolbar-title class="align-end font-weight-black text-left ma-0 pa-4 pl-8">
                  <h5 v-if="notes.length >0">Otros Documentos</h5>
                </v-toolbar-title>
                <v-row>
                  <v-col cols="12" class="ma-0 px-10">
                    <div
                      class="align-end font-weight-light ma-0 pa-0 pl-2"
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
          </v-col>
        </v-row>
      </v-card>
    </v-form>
  </v-container>
</template>

<script>
export default {
  name: "documents-flow",
  data: () => ({
    docsRequired: [],
    docsOptional: [],
    notes: [],
    editable: false
  }),

  beforeMount() {
    this.getDocumentsSubmitted(this.$route.params.id)
    this.getNotes(this.$route.params.id)
  },

  methods: {
    async getDocumentsSubmitted(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/document`)
        this.docsRequired = res.data.required
        this.docsOptional = res.data.optional
        console.log(this.docsRequired + " " + this.docsOptional)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getNotes(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/note`)
        this.notes = res.data
        console.log("NOTES  " + this.notes)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async validarDoc(id, is_valid, comment) {
      try {
        this.loading = true
        let res = await axios.patch(
          `loan/${this.$route.params.id}/document/${id}`,
          {
            is_valid: is_valid,
            comment: comment
          }
        )
        this.toastr.success("El documento se valido correctamente")
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>