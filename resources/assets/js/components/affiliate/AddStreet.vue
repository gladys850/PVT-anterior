<template>
  <v-dialog
    v-model="dialog"
    width="500"
  >
    <v-card>
      <v-toolbar dense flat color="tertiary">
        <v-toolbar-title>Añadir Dirección</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="close()">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-title></v-card-title>
      <v-card-text>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" >
              <v-container class="py-0">
                <v-row>
                    <v-col cols="12" md="5" >
                      <v-select
                        data-vv-name="Ciudad"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        label="Ciudad"
                        v-model="address.city_address_id"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="7">
                      <v-text-field
                      v-model="address.zone"
                      label="Zona"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="zona"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="9">
                      <v-text-field
                      v-model="address.street"
                      label="Calle/Avenida"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="calle"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                      v-model="address.number_address"
                      label="Nro"
                      class="purple-input"
                      v-validate.initial="'required|numeric|min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="nro"
                      ></v-text-field>
                    </v-col>
                </v-row>
            </v-container>
        </v-col>
      </v-row>
  </v-container>
  </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click.stop="close()"
          color="error"
          :disabled="errors.any()"
        >Guardar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
  name: "affiliate-addresses",
  props: {
    bus: {
      type: Object,
      required: true
    },
    cities: {
      type: Array,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    address: {},
  }),
  mounted() {
    this.bus.$on('openDialog', (address) => {
      this.address = address
      this.dialog = true
    })
  },
  methods: {
    close() {
      this.saveAddress()
      this.bus.$emit('saveAddress', this.address)
      this.dialog = false
      this.address = {}
  },
  saveAddress() {
      if (this.address.id) {
        console.log('PATCH editando')
      } else {
        console.log('POST creando')
      }
    }
  }
  }
</script>