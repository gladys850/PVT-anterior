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
                        dense
                      v-model="affiliate.first_name"
                      label="Primer Nombre"
                      v-validate.initial="'required|min:1|max:250'"
                      :error-messages="errors.collect('primer nombre')"
                      data-vv-name="primer nombre"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                        dense
                      v-model="affiliate.second_name"
                      label="Segundo Nombre"
                      data-vv-name="segundo nombre"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        dense
                      v-model="affiliate.last_name"
                      label="Primer Apellido"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('primer apellido')"
                      data-vv-name="primer apellido"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        dense
                      v-model="affiliate.mothers_last_name"
                      label="Segundo Apellido"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('segundo apellido')"
                      data-vv-name="segundo apellido"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        dense
                        data-vv-name="Genero"
                        :items="gender"
                        item-text="name"
                        item-value="value"
                        :loading="loading"
                        label="Genero"
                        v-model="affiliate.gender"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        dense
                        v-model="affiliate.identity_card"
                        label="Cedula de Identidad"
                        v-validate.initial="'required|numeric|min:1|max:50'"
                        :error-messages="errors.collect('cedula identidad')"
                        data-vv-name="cedula identidad"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        dense
                        data-vv-name="Ciudad de Expedición"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        :loading="loading"
                        label="Ciudad de Expedición"
                        v-model="affiliate.city_identity_card_id"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-menu
                        v-model="dates.dueDate.show"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                        :disabled="!editable || !permission.secondary"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            dense
                            v-model="dates.dueDate.formatted"
                            label="Fecha Vencimiento CI"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"
                            :outlined="editable && permission.secondary"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.due_date" no-title @input="dates.dueDate.show = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-menu
                        v-model="dates.birthDate.show"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                        :disabled="!editable || !permission.primary"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            dense
                            v-model="dates.birthDate.formatted"
                            label="Fecha Nacimiento"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"
                            :outlined="editable && permission.primary"
                            :disabled="editable && !permission.primary"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.birth_date" no-title @input="dates.birthDate.show = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        dense
                        :loading="loading"
                        data-vv-name="Ciudad de Nacimiento"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        name="nacimiento"
                        label="Lugar de Nacimiento"
                        v-model="affiliate.city_birth_id"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-select
                        dense
                        :loading="loading"
                        data-vv-name="Estado Civil"
                        :items="civil"
                        item-text="name"
                        item-value="value"
                        label="Estado Civil"
                        name="estado_civil"
                        v-model="affiliate.civil_status"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-menu
                        v-model="dates.dateDeath.show"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                        :disabled="!editable || !permission.secondary"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            dense
                            v-model="dates.dateDeath.formatted"
                            label="Fecha Fallecimiento"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"
                            :outlined="editable && permission.secondary"
                            :disabled="editable && !permission.secondary"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="affiliate.date_death" no-title @input="dates.dateDeath.show = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        dense
                        v-model="affiliate.reason_death"
                        label="Causa Fallecimiento"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
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
                        dense
                        v-model="affiliate.phone_number"
                        label="Telefono"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('telefono')"
                        data-vv-name="telefono"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        dense
                        v-model="affiliate.cell_phone_number"
                        label="Celular"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('celular')"
                        data-vv-name="celular"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                        dense
                        v-model="affiliate.cell_phone_number"
                        label="Celular"
                        v-validate.initial="'min:1|max:20'"
                        :error-messages="errors.collect('celular')"
                        data-vv-name="celular"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-text-field>
                    </v-col>
                      <v-col cols="12" md="6">
                    <v-toolbar-title>DIRECCIÓN DOMICILARIA</v-toolbar-title>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-tooltip top v-if="editable && permission.secondary">
                      <template v-slot:activator="{ on }">
                        <v-btn
                          fab
                          dark
                          x-small
                          v-on="on"
                          color="info"
                          @click.stop="bus.$emit('openDialog', {})"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </template>
                      <span>Añadir Direccion</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="12">
                  <v-data-table
                    :headers="headers"
                    :items="desserts"
                    hide-default-footer
                    class="elevation-1"
                    v-if="cities.length > 0"
                  >
                    <template v-slot:item="props">
                      <tr>
                        <td>{{ cities.find(o => o.id == props.item.city_address_id).name }}</td>
                        <td>{{ props.item.zone }}</td>
                        <td>{{ props.item.street }}</td>
                        <td>{{ props.item.number_address }}</td>
                        <td v-show="editable && permission.secondary">
                          <v-btn text icon color="info" @click.stop="bus.$emit('openDialog', props.item)">
                            <v-icon>mdi-pencil</v-icon>
                          </v-btn>
                          <v-btn text icon color="error" @click.stop="bus.$emit('openRemoveDialog', `address/${props.item.id}`)">
                            <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </td>
                      </tr>
                    </template>
                  </v-data-table>
                  </v-col>
                </v-row>
              </v-container>
        </v-col>
      </v-row>
      <AddStreet :bus="bus" :cities="cities"/>
      <RemoveItem :bus="bus"/>
  </v-container>
</template>

<script>
import RemoveItem from '@/components/shared/RemoveItem'
import AddStreet from '@/components/affiliate/AddStreet'
  export default {
  name: "affiliate-profile",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
      editable: {
      type: Boolean,
      required: true
    },
    permission: {
      type: Object,
      required: true
    }
  },
  components: {
    AddStreet,
    RemoveItem
  },
  data: () => ({
    loading: true,
    dialog: false,
    cities: [],
    headers: [
          { text: 'Ciudad', align: 'left', value: 'city_address_id' },
          { text: 'Zona', align: 'left', value: 'zone' },
          { text: 'Calle', align: 'left', value: 'street' },
          { text: 'Nro', align: 'left', value: 'number_address' },
          { text: 'Acciones', align: 'center' }
        ],
        desserts: [
          {
            id: 1,
            city_address_id: 1,
            zone: 'Cristal I',
            street: 'Olmos',
            number_address: 24,
          },
          {
            id: 2,
            city_address_id: 2,
            zone: 'Anexo 16 de Julio I',
            street: 'Olmos',
            number_address: 2364,
          },
          {
            id: 3,
            city_address_id: 3,
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
    dates: {
      dueDate: {
        formatted: null,
        picker: false
      },
      birthDate: {
        formatted: null,
        picker: false
      },
      dateDeath: {
        formatted: null,
        picker: false
      }
    },
    bus: new Vue()
  }),
  beforeMount() {
    this.getCities();
  },
  mounted() {
    this.bus.$on('saveAddress', (address) => {
      let index = this.desserts.findIndex(o=> o.id == address.id)
      if (index == -1) {
        this.desserts.unshift(address)
      } else {
        this.desserts[index] = address
      }
    })
  },
  watch: {
    'affiliate.due_date': function(date) {
      if (date) this.dates.dueDate.formatted = this.$moment(date).format('L')
    },
    'affiliate.birth_date': function(date) {
      if (date) this.dates.birthDate.formatted = this.$moment(date).format('L')
    },
    'affiliate.date_death': function(date) {
      if (date) this.dates.dateDeath.formatted = this.$moment(date).format('L')
    }
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