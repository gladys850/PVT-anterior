<template>
  <v-dialog
    v-model="dialog"
    width="600"
  >
    <v-card>
      <v-toolbar dense flat color="tertiary">
        <v-toolbar-title v-show="!observation.edit">Añadir Observacion</v-toolbar-title>
        <v-toolbar-title v-show="observation.edit">Editar Observacion</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text class="ma-0 pb-0">
        <v-container fluid class="ma-0 pb-0">
          <v-row justify="center" class="ma-0 pb-0">
            <v-col cols="12" class="ma-0 pb-0">
              <v-container class="py-0">
                <ValidationObserver ref="observer">
                  <v-form>
                    <v-row>
                      <v-col cols="4" class="ma-0 pb-0">
                        <label>Tipo de Observacion:</label>
                      </v-col>
                      <v-col cols="8" class="ma-0 pb-0">
                        <v-select
                          dense
                          :readonly="observation.edit"
                          v-model="observation.observation_type_id"
                          :items="observation_type"
                          item-text="name"
                          item-value="id"
                          persistent-hint
                        ></v-select>
                      </v-col>
                      <v-col cols="12" class="ma-0 pb-0">
                        <v-text-field
                          dense
                          :readonly="observation.edit"
                          v-model="observation.message"
                          label="Descripcion"
                          ></v-text-field>
                      </v-col>
                      <v-col cols="6" class="ma-0 pb-0" v-show="observation.edit">
                        <v-checkbox
                          class="ma-0 pb-0"
                          label="Subsanar Tramite"
                          v-model="observation.enabled"
                        ></v-checkbox>
                    </v-col>
                    </v-row>
                  </v-form>
                </ValidationObserver>
              </v-container>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions class="ma-0 pb-0">
        <v-spacer></v-spacer>
        <v-btn @click.stop="saveObservation()"
          color="error"
          >Guardar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>

export default {
  name: "flow-add-observation",
  props: {
    bus: {
      type: Object,
      required: true
    },
    loan: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    observation: {},
    observation_type: []
  }),
  beforeMount(){
    this.getObservationType()
  },
  mounted() {
    this.bus.$on('openDialog', (observation) => {
      this.observation = observation
      this.dialog = true
    })
  },
  methods: {
    async adicionar() {
      if (await this.$refs.observer.validate()) {
        this.saveObservation()
        this.bus.$emit('saveObservation', this.observation)
        this.close()
      }
    },
    close() {
      this.dialog = false
      this.observation = {}
    },
    async saveObservation() {
      try {
          if(this.observation.edit)
          {
            let res = await axios.patch(`loan/${this.loan.id}/observation`, {
              original: {
                user_id: this.observation.user_id,
                observation_type_id: this.observation.observation_type_id,
                message: this.observation.message,
                date: this.observation.date,
                enabled: false
              },
              update: {
                enabled: this.observation.enabled
                }
            });
            console.log("DATOS OBSERVACION " + this.loan.id)
            console.log(this.observation.user_id)
            console.log(this.observation.observation_type_id)
            console.log(this.observation.message)
            console.log(this.observation.date)
            console.log(this.observation.enabled)
            this.bus.$emit('emitSaveObservation', 'vacio');//emitir SAveObservation edit, para ObserverFlow
            console.log('entro al editar'+this.observation.enabled)
            console.log(this.observation)

          }else{
            let res = await axios.post(`loan/${this.loan.id}/observation`, {
            observation_type_id:this.observation.observation_type_id,
            message:this.observation.message});
            this.bus.$emit('emitSaveObservation', 'vacio');//emitir SAveObservation new, para ObserverFlow
            console.log('entro al entro al grabar')
          }
      
 this.dialog = false

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getObservationType() {
      try {
        this.loading = true
        let res = await axios.get(`module/${6}/observation_type`)
        this.observation_type = res.data
        console.log('estas son las observaciones'+this.observation_type)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>