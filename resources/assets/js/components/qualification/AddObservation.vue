<template>
  <v-dialog
    v-model="dialog"
    width="600"
  >
    <v-card>
      <v-toolbar dense flat color="tertiary">
        <v-toolbar-title v-show="!observation.edit">Añadir Observacion</v-toolbar-title>
        <v-toolbar-title v-show="observation.edit">{{observation}}Editar Observacion</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" >
              <v-container class="py-0">
                <ValidationObserver ref="observer">
                  <v-form>
                    <v-row>
                      <v-col cols="4">
                      <label>Tipo de Observacion:{{observation}}</label>
                    </v-col>
                    <v-col cols="8">
                      <v-select
                        dense
                        v-model="observation.observation_type_id"
                        :items="observation_type"
                        item-text="name"
                        item-value="id"
                        persistent-hint
                      ></v-select>
                    </v-col>
                    <v-col cols="12" >
                      <v-text-field
                        dense
                        v-model="observation.message"
                        label="Descripcion"
                      ></v-text-field>
                    </v-col>                   
                  
                    
                    </v-row>
                  </v-form>
                </ValidationObserver>
            </v-container>
        </v-col>
      </v-row>
  </v-container>
  </v-card-text>
      <v-card-actions >
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
  name: "qualification-add-observation",
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  
  data: () => ({
    dialog: false,
    observation: {}
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
      let res = await axios.post(`loan/${1}/observation`, {
      observation_type_id:this.observation.observation_type_id,
      message:this.observation.message
    });
      console.log('se grabo el mensaje')
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