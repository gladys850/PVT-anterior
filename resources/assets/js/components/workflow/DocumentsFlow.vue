<template>
  <v-container fluid >
    <v-toolbar-title  class="pb-2">DOCUMENTOS PRESENTADOS</v-toolbar-title>
    <v-form>
          <div v-if="$store.getters.userRoles.includes('PRE-revision-legal')">
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn
                fab
                dark
                x-small
                :color="'error'"
                top
                right
                absolute
                v-on="on"
                style="margin-right: 45px;"
                @click.stop="resetForm()"
                v-show="editable"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
            <div>
              <span>Cancelar</span>
            </div>
          </v-tooltip>
        </div>
        <v-tooltip top>
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
              style="margin-right: -9px;"
              @click.stop="validarDoc()"
            >
              <v-icon v-if="editable">mdi-check</v-icon>
              <v-icon v-else>mdi-check-all</v-icon>
            </v-btn>
          </template>
          <div>
            <span v-if="editable">Guardar</span>
            <span v-else>Validar documentos</span>
        </div>
        </v-tooltip>
        
      <v-toolbar-title>REQUERIDOS</v-toolbar-title>
       <v-progress-linear></v-progress-linear>
      <!--<v-card>-->
       <!-- <v-row>
          <v-col cols="12" >-->
            <!--<v-data-iterator :items="docsRequired" hide-default-footer>-->
              <!--<template>-->
                <v-row  class="py-3">
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
                              {{ req.name }}
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
                                     @change="createObjectDocuments(req.id, req.pivot.is_valid, req.pivot.comment)"
                                    :disabled="!editable"
                                  ></v-checkbox>
                                </div>
                                <v-spacer></v-spacer>
                              </v-col>
                            </v-list-item>
                          </v-list>
                        </v-col>
                      </v-row>
                      <v-row v-if="$store.getters.userRoles.includes('PRE-revision-legal')">
                        <v-col cols="12" class="ma-0 py-0 px-10">
                          <v-text-field
                            dense
                            outlined
                            color="success"
                            label="Comentario"
                            v-model="req.pivot.comment"
                             @change="createObjectDocuments(req.id, req.pivot.is_valid, req.pivot.comment)"
                            :disabled="!editable"
                          ></v-text-field>
                        </v-col>
                        <!--<v-col cols="1" class="ma-0 pa-0">
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
                        </v-col>-->
                      </v-row>
                    </v-card>
                  </v-col>
                </v-row>
              <!--</template>
            </v-data-iterator>
          </v-col>
        </v-row>-->
        <v-row>
          <v-col cols="12" v-if="docsOptional.length >0" >
           <!--<v-data-iterator :items="docsOptional" hide-default-footer>-->
              <template>
                <v-toolbar-title>ADICIONALES</v-toolbar-title>
                 <v-progress-linear></v-progress-linear>
                <v-row  class="py-3">
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
                                {{ opt.name }}
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
                                    @change="createObjectDocuments(req.id, req.pivot.is_valid, req.pivot.comment)"
                                    :disabled="!editable"
                                  ></v-checkbox>
                                </div>
                                <v-spacer></v-spacer>
                              </v-col>
                            </v-list-item>
                          </v-list>
                        </v-col>
                      </v-row>
                      <v-row v-if="$store.getters.userRoles.includes('PRE-revision-legal')">
                        <v-col cols="12" class="ma-0 py-0 pl-10 pr-2">
                          <v-text-field
                            dense
                            outlined
                            color="success"
                            label="Comentario"
                            v-model="opt.pivot.comment"
                            @change="createObjectDocuments(req.id, req.pivot.is_valid, req.pivot.comment)"
                            :disabled="!editable"
                          ></v-text-field>
                        </v-col>
                        <!--<v-col cols="1" class="ma-0 pa-0">
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
                        </v-col>-->
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
            <!--</v-data-iterator>-->
          </v-col>
        </v-row>
      <!--</v-card>-->
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
    editable: false,
    reload: false,
    documents:[]
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
    createObjectDocuments(id, is_valid, comment){
      let document = {}
      document.id = id,
      document.is_valid = is_valid,
      document.comment = comment
      console.log("mostar objeto")
      console.log(document)
     
          this.documents.push(document)
      
      console.log(this.documents)

    },
    async validarDoc() {
      try {
        if (!this.editable) {
          this.editable = true
        } else {
          for(let i=0; i < this.documents.length; i++){
            console.log(this.documents[i].id)
            let res = await axios.patch(`loan/${this.$route.params.id}/document/${this.documents[i].id}`,
            {
              is_valid: this.documents[i].is_valid,
              comment: this.documents[i].comment
            }
          )
          }
          this.toastr.success("Los documentos se validarón correctamente")
          this.editable = false
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    resetForm() {
      this.editable = false
      this.reload = true
      this.$nextTick(() => {
      this.reload = false
      })
    },
  }
}
</script>