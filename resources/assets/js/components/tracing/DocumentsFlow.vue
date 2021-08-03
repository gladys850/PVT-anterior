<template>
  <v-container fluid class="py-0" >
    <v-row  class="py-0">
      <p >DOCUMENTOS REQUERIDOS</p>
        <v-progress-linear></v-progress-linear>
        <v-col v-for="(req,i) in docsRequired" :key="req.id" cols="12" class="py-1">
            <v-row>
              <v-col cols="12" class="py-0">
                <v-list dense class="py-0">
                  <v-list-item class="py-0">
                    <v-col cols="1" class="py-0">
                      <v-list-item-content class="align-end font-weight-light">
                        <div>
                          <h2>{{i+1}}</h2>
                        </div>
                      </v-list-item-content>
                    </v-col>
                    <v-col cols="10" class="py-0 ml-n8">
                    {{ req.name }}
                    </v-col>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>
        </v-col>
      </v-row>
        <v-row>
          <v-col cols="12"  >
            <template v-if="docsOptional.length >0">
                <p >DOCUMENTOS ADICIONALES</p>
                 <v-progress-linear></v-progress-linear>
                <v-row  class="py-3">
                  <v-col v-for="(opt,i) in docsOptional" :key="opt.id" cols="12" class="py-1">
                    <v-col cols="12" class="py-0">
                      <v-list dense class="py-0">
                        <v-list-item class="py-0">
                          <v-col cols="1" class="py-0">
                            <v-list-item-content class="align-end font-weight-light">
                              <div>
                                <h2>{{i+1}}</h2>
                              </div>
                            </v-list-item-content>
                          </v-col>
                          <v-col cols="8" class="py-0 ml-n8">
                            {{ opt.name }}
                          </v-col>
                        </v-list-item>
                      </v-list>
                    </v-col>
                  </v-col>
                </v-row>
              </template>
               <template v-if="notes.length >0">
                <p >OTROS DOCUMENTOS</p>
                 <v-progress-linear></v-progress-linear>
                <v-row  class="py-3">
                  <v-col v-for="(note, index)  in notes" :key="index" cols="12" class="py-1">
                    <v-col cols="12" class="py-0">
                      <v-list dense class="py-0">
                        <v-list-item class="py-0">
                          <v-col cols="1" class="py-0">
                            <v-list-item-content class="align-end font-weight-light">
                              <div>
                                <h2>{{index+1}}</h2>
                              </div>
                            </v-list-item-content>
                          </v-col>
                          <v-col cols="8" class="py-0 ml-n8">
                            {{ note.message }}
                          </v-col>
                        </v-list-item>
                      </v-list>
                    </v-col>
                  </v-col>
                </v-row>
              </template>
          </v-col>
        </v-row>
  </v-container>
</template>
<script>
export default {
  name: "documents-flow",
  data: () => ({

    docsRequired: [],
    docsOptional: [],
    notes: []
  }),
  beforeMount() {
    this.getDocumentsSubmitted(this.$route.params.id)
    this.getNotes(this.$route.params.id)
  },
  methods: {
    //Metodo para obtener los documentos requeridos y adicionales
    async getDocumentsSubmitted(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/document`)
        this.docsRequired = res.data.required
        this.docsOptional = res.data.optional
       } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    //Metodo para obtener otros documentos
    async getNotes(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/note`)
        this.notes = res.data
       } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>