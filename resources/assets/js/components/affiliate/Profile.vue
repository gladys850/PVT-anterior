<template>
  <v-container fluid >
    <ValidationObserver ref="observer">
    <v-form>
      <v-row justify="center">
        <v-col cols="12" md="6" >
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12">
                    <v-toolbar-title>DATOS PERSONALES</v-toolbar-title>
                  </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="first_name" name="Primer Nombre" rules="required|alpha_spaces|min:3|max:20">
                      <v-text-field
                      
                      :error-messages="errors"
                        dense
                      v-model="affiliate.first_name"
                      label="Primer Nombre"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="second_name" name="Segundo Nombre" rules="alpha_spaces|min:3|max:20">
                      <v-text-field
                      :error-messages="errors"
                        dense
                      v-model="affiliate.second_name"
                      label="Segundo Nombre"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="last_name" name="Apellido Paterno" rules="required|alpha_spaces|min:3|max:20">
                      <v-text-field
                      :error-messages="errors"
                        dense
                      v-model="affiliate.last_name"
                      label="Apellido Paterno"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="mothers_last_name" name="Apellido Materno" rules="alpha_spaces|min:3|max:20">
                      <v-text-field
                      :error-messages="errors"
                        dense
                      v-model="affiliate.mothers_last_name"
                      label="Apellido Materno"
                      :readonly="!editable || !permission.primary"
                      :outlined="editable && permission.primary"
                      :disabled="editable && !permission.primary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="gender" name="Género" rules="required|oneOf:M,F">
                      <v-select
                        :error-messages="errors"
                        dense
                        :items="genders"
                        item-text="name"
                        item-value="value"
                        :loading="loading"
                        label="Género"
                        v-model="affiliate.gender"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-select>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="identity_card" name="Cédula de Identidad" rules="required|alpha_dash|min:5|max:15">
                      <v-text-field
                        :error-messages="errors"
                        dense
                        v-model="affiliate.identity_card"
                        label="Cédula de Identidad"
                        :readonly="!editable || !permission.primary"
                        :outlined="editable && permission.primary"
                        :disabled="editable && !permission.primary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="city_identity_card_id" name="Ciudad de Expedición" rules="required|integer|min:1">
                      <v-select
                        :error-messages="errors"
                        dense
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        :loading="loading"
                        label="Ciudad de Expedición"
                        v-model="affiliate.city_identity_card_id"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-select>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="5" v-if="affiliate.is_duedate_undefined==false">
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
                    <v-col cols="12" md="3">
                      <v-checkbox
                        v-model="affiliate.is_duedate_undefined"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                        :label="`Indefinido`"
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="civil_status" name="Estado Civil" rules="oneOf:C,D,S,V">
                      <v-select
                        :error-messages="errors"
                        dense
                        :loading="loading"
                        :items="civil_statuses"
                        item-text="name"
                        item-value="value"
                        label="Estado Civil"
                        v-model="affiliate.civil_status"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-select>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-menu
                        v-model="dates.birthDate.show"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                        :disabled="!editable || !permission.secondary"
                      >
                        <template v-slot:activator="{ on }">
                          <ValidationProvider v-slot="{ errors }" vid="birth_date" name="Fecha Nacimiento" rules="required">
                          <v-text-field
                            :error-messages="errors"
                            dense
                            v-model="dates.birthDate.formatted"
                            label="Fecha Nacimiento"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"
                            :outlined="editable && permission.secondary"
                            :disabled="editable && !permission.secondary"
                          ></v-text-field>
                          </ValidationProvider>
                        </template>
                        <v-date-picker v-model="affiliate.birth_date" no-title @input="dates.birthDate.show = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="city_birth_id" name="Ciudad de Nacimiento" rules="integer|min:1">
                      <v-select
                        :error-messages="errors"
                        dense
                        :loading="loading"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        label="Ciudad de Nacimiento"
                        v-model="affiliate.city_birth_id"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-select>
                      </ValidationProvider>
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
                     <ValidationProvider v-slot="{ errors }" name="celular1" rules="min:1|max:12">
                      <v-text-field
                        :error-messages="errors" 
                        dense
                        v-model="cel[0]"
                        label="Celular 1"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                        @change="updateCelular()"
                      ></v-text-field>
                      </ValidationProvider> 
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" name="celular" rules="min:1|max:12">
                      <v-text-field
                        :error-messages="errors"
                        dense
                        v-model="cel[1]"
                        label="Celular 2"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                        @change="updateCelular()"
                      ></v-text-field>
                      </ValidationProvider> 
                    </v-col>
                    
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" name="telefono" rules="min:1|max:12">
                      <v-text-field
                        :error-messages="errors"
                        dense
                        v-model="affiliate.phone_number"
                        label="Fijo"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :disabled="editable && !permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                      <v-col cols="12" md="6">
                    <v-toolbar-title>DOMICILIO</v-toolbar-title>
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
                          @click.stop="bus.$emit('openDialog', { edit: true })"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </template>
                      <span>Añadir Dirección</span>
                    </v-tooltip>
                  </v-col>
                  <v-col cols="12">
                  <v-data-table
                      :headers="headers"
                      :items="addresses"
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
                        <v-btn text icon color="warning" @click.stop="bus.$emit('openDialog', {...props.item, ...{edit: true}})">
                          <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn text icon color="error" @click.stop="bus.$emit('openRemoveDialog', `address/${props.item.id}`)">
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </td>
                      <td v-show="!editable">
                        <v-btn v-if="props.item.latitude && props.item.longitude" text icon color="info" @click.stop="bus.$emit('openDialog', {...props.item, ...{edit: false}})">
                          <v-icon>mdi-google-maps</v-icon>
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
    </v-form>
    </ValidationObserver>
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
    addresses: {
      type: Array,
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
  data() {
    return {
      loading: true,
      dialog: false,
      cel:[null,null],
      cities: [],
      headers: [
            { text: 'Ciudad', align: 'left', value: 'city_address_id' },
            { text: 'Zona', align: 'left', value: 'zone' },
            { text: 'Calle', align: 'left', value: 'street' },
            { text: 'Nro', align: 'left', value: 'number_address' },
            { text: 'Acciones', align: 'center' }
          ],
      civil_statuses: [
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
      genders: [
        {
          name:"Femenino",
          value:"F"
        },
        {
          name:"Masculino",
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
    }
  },
  beforeMount() {
    this.getCities()
  },
  mounted() {
    if (this.affiliate.id) {
      this.formatDate('dueDate', this.affiliate.due_date)
      this.formatDate('birthDate', this.affiliate.birth_date)
      this.formatDate('dateDeath', this.affiliate.date_death)
      this.getCelular()
    }
      this.bus.$on('saveAddress', (address) => {
        if (address.id) {
          let index = this.addresses.findIndex(o=> o.id == address.id)
          if (index == -1) {
            this.addresses.unshift(address)
          } else {
            this.addresses[index] = address
          }
        }
    })
  },
  watch: {
    'affiliate.due_date': function(date) {
      this.formatDate('dueDate', date)
    },
    'affiliate.birth_date': function(date) {
      this.formatDate('birthDate', date)
    },
    'affiliate.date_death': function(date) {
      this.formatDate('dateDeath', date)
    }
  },
  methods: {
    close() {
      this.dialog = false
      this.$emit('closeFab')
    },
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format('L')
      } else {
        this.dates[key].formatted = null
      }
    },
    async getCities() {
    try {
      this.loading = true
      let res = await axios.get(`city`)
      this.cities = res.data
    } catch (e) {
      this.dialog = false
      console.log(e)
    }finally {
        this.loading = false
      }
  },
    getCelular(){
      
      let part = []
      if(this.affiliate.cell_phone_number!==null){
        part=this.affiliate.cell_phone_number.split(',')
        this.cel[0]= part[0]
        this.cel[1]= part[1]
      }
    },
    updateCelular(){
      let count = 0
      let val = 0
      if(this.cel[0]){
        if(this.cel[0].trim() !== ''){
          this.cel[0]=this.cel[0].trim()
          count++
          val = 0
        }        
      }
      if(this.cel[1]){
        if(this.cel[1].trim() !== ''){
          this.cel[1]=this.cel[1].trim()
          count++
          val = 1
        }        
      }
      if(count == 0){
        this.affiliate.cell_phone_number=null
      } else if(count == 1){
        this.affiliate.cell_phone_number=this.cel[val]
      } else {
        this.affiliate.cell_phone_number=this.cel.join(',')
      }
    }
  }
  }
</script>
