<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="6" >
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12">
                    <v-toolbar-title>DATOS PERSONALES</v-toolbar-title>
                  </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                      v-model="affiliate.first_name"
                      label="Primer Nombre"
                      v-validate.initial="'required|min:1|max:250'"
                      :error-messages="errors.collect('primer nombre')"
                      data-vv-name="primer nombre"
                      :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                      v-model="affiliate.second_name"
                      label="Segundo Nombre"
                      data-vv-name="segundo nombre"
                      :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      v-model="affiliate.last_name"
                      label="Primer Apellido"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('primer apellido')"
                      data-vv-name="primer apellido"
                      :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      v-model="affiliate.mothers_last_name"
                      label="Segundo Apellido"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('segundo apellido')"
                      data-vv-name="segundo apellido"
                      :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        data-vv-name="Genero"
                        :items="gender"
                        item-text="name"
                        item-value="value"
                        :loading="loading"
                        label="Genero"
                        v-model="affiliate.gender"
                        :readonly="!editable"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        v-model="affiliate.identity_card"
                        label="Cedula de Identidad"
                        v-validate.initial="'required|numeric|min:1|max:50'"
                        :error-messages="errors.collect('cedula identidad')"
                        data-vv-name="cedula identidad"
                        :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        data-vv-name="Ciudad de Expedición"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        :loading="loading"
                        label="Ciudad de Expedición"
                        v-model="affiliate.city_identity_card_id"
                        :readonly="!editable"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-menu
                        v-model="menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                        :disabled="!editable"
                      >
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="affiliate.due_date"
                          label="Fecha Vencimiento CI"
                          append-icon="mdi-calendar"
                          readonly
                          v-on="on"
                        ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.due_date" @input="menu = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-menu
                        v-model="menu1"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                        :disabled="!editable"
                      >
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="affiliate.birth_date"
                          label="Fecha Nacimiento"
                          append-icon="mdi-calendar"
                          readonly
                          v-on="on"
                        ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.birth_date" @input="menu1 = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        :loading="loading"
                        data-vv-name="Ciudad de Nacimiento"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        name="nacimiento"
                        label="Lugar de Nacimiento"
                        v-model="affiliate.city_birth_id"
                        :readonly="!editable"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        :loading="loading"
                        data-vv-name="Estado Civil"
                        :items="civil"
                        item-text="name"
                        item-value="value"
                        label="Estado Civil"
                        name="estado_civil"
                        v-model="affiliate.civil_status"
                        :readonly="!editable"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-menu
                        v-model="menu2"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                        :disabled="!editable"
                      >
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="affiliate.date_death"
                          label="Fecha Fallecimiento"
                          append-icon="mdi-calendar"
                          readonly
                          v-on="on"
                        ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.date_death" @input="menu2 = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="affiliate.reason_death"
                        label="Causa Fallecimiento"
                        :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                </v-row>
              </v-container>
        </v-col>
          <v-col cols="12" md="5" >
                <v-container class="py-0">
                  <v-row>
                    <v-col cols="12">
                      <v-toolbar-title>TELÉFONOS</v-toolbar-title>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        v-model="affiliate.phone_number"
                        label="Telefono"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('telefono')"
                        data-vv-name="telefono"
                        :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        v-model="affiliate.cell_phone_number"
                        label="Celular"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('celular')"
                        data-vv-name="celular"
                        :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        v-model="affiliate.cell_phone_number"
                        label="Celular"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('celular')"
                        data-vv-name="celular"
                        :readonly="!editable"
                      ></v-text-field>
                    </v-col>
                      <v-col cols="12" md="6">
                    <v-toolbar-title>DIRECCIÓN DOMICILARIA</v-toolbar-title>
                  </v-col>
                  <v-col cols="12" md="3">
                    <template>
                    <v-btn
                      fab
                      dark
                      x-small
                      v-on="on"
                      color="info"
                      @click.stop="dialog = true"
                    >
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                    </template>
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
                      <AddStreet/>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        color="error"
                        :disabled="errors.any()"
                      >Añadir</v-btn>
                    </v-card-actions>
                  </v-card>
                  </v-dialog>
                  </v-col>
                  <v-col cols="12">
                  <v-data-table
                      :headers="headers"
                      :items="desserts"
                      hide-default-footer
                      class="elevation-1"
                  ></v-data-table>
                  </v-col>
                </v-row>
              </v-container>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
import AddStreet from '@/components/affiliate/AddStreet'
  export default {
  name: "affiliate-profile",
  props: {
    affiliate: {
      type: Object,
      required: true
    }
  },
  components: {
    AddStreet
  },
    editable: {
      type: Boolean,
      required: true
    },
  data: () => ({
    loading: true,
    dialog: false,
    cities: [],
    headers: [
          { text: 'Ciudad', align: 'left', value: 'city_address_id' },
          { text: 'Zona', align: 'left', value: 'zone' },
          { text: 'Calle', align: 'left', value: 'street' },
          { text: 'Nro', align: 'left', value: 'number_address' }
        ],
        desserts: [
          {
            city_address_id: 'La Paz',
            zone: 'Cristal I',
            street: 'Olmos',
            number_address: 24,
          },
          {
            city_address_id: 'La Paz',
            zone: 'Anexo 16 de Julio I',
            street: 'Olmos',
            number_address: 2364,
          },
          {
            city_address_id: 'La Paz',
            zone: 'Alto Lima I',
            street: 'Olmos',
            number_address: 224,
          },
        ],
    civil: [
      { name:"Soltero",
        value:"S"
      },
      { name:"Casado",
        value:"C"
      },
      { name:"Viudo",
        value:"V"
      },
      { name:"Divorciado",
        value:"D"
      }
    ],
    gender: [
      { name:"Femenino",
        value:"F"
      },
      { name:"Masculino",
        value:"M"
      }
    ],
    city: [],
    cityTypeSelected: null,
      date: null,
        menu: false,
        menu1: false,
        menu2: false,
      }),
  beforeMount() {
    this.getCities();
  },
  methods: {
    close() {
      this.dialog = false
      this.$emit('closeFab')
    },
    async getCities() {
    try {
      this.loading = true
      let res = await axios.get(`city`);
      this.cities = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  }
  }
  }
</script>