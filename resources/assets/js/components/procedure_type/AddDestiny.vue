<template>
  <div>
    <v-tooltip top>
      <template v-slot:activator="{ on }">
        <v-btn
          fab
          dark
          x-small
          v-on="on"
          color="info"
          @click="dialog = true"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <span>Nuevo destino</span>
    </v-tooltip>
    <v-dialog
      v-model="dialog"
      width="500"
      persistent
    >
      <v-card>
        <ValidationObserver ref="observer" v-slot="{ invalid }">
          <v-form @submit.prevent="saveDestiny" lazy-validation>
            <v-toolbar dense flat color="tertiary">
              <v-toolbar-title>Añadir destino de préstamo</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon @click.stop="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-title></v-card-title>
            <v-card-text>
              <ValidationProvider v-slot="{ errors }" vid="name" name="Destino" rules="required|min:3|max:50">
                <v-text-field
                  :error-messages="errors"
                  v-model="destiny.name"
                  label="Destino"
                ></v-text-field>
              </ValidationProvider>
              <ValidationProvider v-slot="{ errors }" vid="description" name="Detalle" rules="required|min:3|max:255">
                <v-text-field
                  :error-messages="errors"
                  v-model="destiny.description"
                  label="Detalle"
                ></v-text-field>
              </ValidationProvider>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                type="submit"
                :disabled="invalid"
              >Guardar</v-btn>
            </v-card-actions>
          </v-form>
        </ValidationObserver>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: 'add-loan-destiny',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    destiny: {}
  }),
  mounted() {
    this.bus.$on('openDialog', (destiny) => {
      this.destiny = destiny
      this.dialog = true
    })
  },
  watch: {
    dialog(val) {
      if (!val) {
        this.destiny = {}
        this.$refs.observer.reset()
      }
    }
  },
  methods: {
    async saveDestiny() {
      try {
        if (await this.$refs.observer.validate()) {
          let res
          let flush = false
          if (this.destiny.hasOwnProperty('id')) {
            res = await axios.patch(`loan_destiny/${this.destiny.id}`, this.destiny)
          } else {
            flush = true
            res = await axios.post(`loan_destiny`, this.destiny)
          }
          this.toastr.success('Registrado exitosamente')
          this.bus.$emit('getDestinies', flush)
          this.dialog = false
        }
      } catch (e) {
        this.$refs.observer.setErrors(e)
      }
    }
  }
}
</script>