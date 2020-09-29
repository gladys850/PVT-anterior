<template>
  <v-container fluid>
    <ValidationObserver ref="observer">
      <v-form>
        <v-card class="px-3">
          <v-row justify="center">
            <v-col cols="12">
              <h3 class="text-uppercase text-center">INFORMACIÓN DEL INMUEBLE</h3>
            </v-col>
            <v-col cols="12" md="6" class="v-card-profile">
              <v-row justify="center">
                <v-col cols="12" md="6" class="py-0">
                  <small>
                    <v-text-field
                      dense
                      label="Nro de Lote de Terreno"
                      v-model="loan_property.land_lot_number"
                      outlined
                    ></v-text-field>
                  </small>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Unidad Vecinal"
                    v-model="loan_property.neighborhood_unit"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field dense label="Ubicación" v-model="loan_property.location" outlined></v-text-field>
                </v-col>
                <v-col cols="12" md="3" class="py-0">
                  <v-text-field dense label="Superficie" v-model="loan_property.surface" outlined></v-text-field>
                </v-col>
                <v-col cols="12" md="3" class="py-0">
                  <v-select
                    dense
                    :items="items_measurement"
                    item-text="name"
                    item-value="value"
                    label="Unidad de medida superficie"
                    v-model="loan_property.measurement"
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Código Catastral"
                    v-model="loan_property.cadastral_code"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="12" class="py-0">
                  <v-text-field dense label="Colindancias" v-model="loan_property.limit" outlined></v-text-field>
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12" md="6" class="v-card-profile">
              <v-row justify="center">
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Nro de Escritura Pública"
                    v-model="loan_property.public_deed_number"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Notaría de Fé Pública"
                    v-model="loan_property.lawyer"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Nro Matrícula Computarizada"
                    v-model="loan_property.registration_number"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="Nro Asiento Folio Real"
                    v-model="loan_property.real_folio_number"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-menu
                    v-model="dates.publicDeedDate.show"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    max-width="290px"
                    min-width="290px"
                  >
                    <template v-slot:activator="{ on }">
                      <v-text-field
                        dense
                        outlined
                        readonly
                        v-model="dates.publicDeedDate.formatted"
                        label="Fecha de Escritura Pública"
                        hint="Día/Mes/Año"
                        persistent-hint
                        append-icon="mdi-calendar"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="loan_property.public_deed_date"
                      no-title
                      @input="dates.publicDeedDate.show = false"
                    ></v-date-picker>
                  </v-menu>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-select
                    dense
                    :items="cities"
                    item-text="name"
                    item-value="id"
                    :loading="loading"
                    label="Ciudad de registro en derechos reales"
                    v-model="loan_property.real_city_id"
                    outlined
                  ></v-select>
                </v-col>
                <v-col cols="12" md="6" class="py-0">
                  <v-text-field
                    dense
                    label="VNR (Valor Neto Realizado)"
                    v-model="loan_property.net_realizable_value"
                    outlined
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" class="py-0"></v-col>

                <!--<v-col cols="12" md="12" class="py-0">
                     <v-data-table
                    :headers="headers"
                    :items="desserts"
                    sort-by="calories"
                    class="elevation-1"
                  >
                    <template v-slot:top>
                      <v-toolbar flat color="white">
                        <v-toolbar-title>Gravamen</v-toolbar-title>
                          <v-divider
                            class="mx-4"
                            inset
                            vertical
                          ></v-divider>
                          <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                fab
                                dark
                                x-small
                                v-on="on"
                                color="info"
                                @click.stop="bus.$emit('openDialog', { edit: true })"
                                v-bind="attrs"
                              ><v-icon>mdi-plus</v-icon>
                              </v-btn>
                            </template>
                            <v-card>
                              <v-card-title class="pa-0">
                                <v-toolbar dense flat color="tertiary">
                                  <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                                  <v-spacer></v-spacer>
                                  <v-btn icon @click.stop="close()">
                                    <v-icon>mdi-close</v-icon>
                                  </v-btn>
                                </v-toolbar>
                              </v-card-title>
                              <v-card-text class="py-0">
                                <v-container>
                                  <v-row>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.name" dense label="Entidad Financiera"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.calories" dense label="Monto Adeudado"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                      <v-text-field v-model="editedItem.fat" dense label="Descripcion"></v-text-field>
                                    </v-col>
                                  </v-row>
                                </v-container>
                              </v-card-text>
                              <v-card-actions class="pt-0">
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" dense text @click="close">Cancelar</v-btn>
                                <v-btn color="blue darken-1" dense text @click="save">Guardar</v-btn>
                              </v-card-actions>
                            </v-card>
                          </v-dialog>
                        </v-toolbar>
                      </template>
                      <template v-slot:item.actions="{ item }">
                        <v-icon
                          small
                          class="mr-2"
                          @click="editItem(item)"
                        >
                          mdi-pencil
                        </v-icon>
                        <v-icon
                          small
                          @click="deleteItem(item)"
                        >
                          mdi-delete
                        </v-icon>
                      </template>
                      <template v-slot:no-data>
                        <v-btn color="primary" @click="initialize">Reset</v-btn>
                      </template>
                    </v-data-table>
                </v-col>-->
              </v-row>
            </v-col>
          </v-row>
        </v-card>
      </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>
export default {
  name: "hipotecari-data",
  props: {
    modalidad: {
      type: Object,
      required: true
    },
    loan_property:{
      type:Object,
      required: true
    }
  },
  data: () => ({
    //editedIndex: -1,
    //loan_property: {},
    dates: {
      publicDeedDate: {
        formatted: null,
        picker: false
      }
    },
    cities: [],
    items_measurement: [
      { name: "Metros cuadrados", value: "Metros cuadrados" },
      { name: "Hectáreas", value: "Hectáreas" }
    ]
  }),
  mounted() {
    this.getCities();
    this.formatDate("publicDeedDate", this.loan_property.public_deed_date);
  },
  watch: {
    "loan_property.public_deed_date": function(date) {
      this.formatDate("publicDeedDate", date);
    }
  },
  methods: {
    formatDate(key, date) {
      if (date) {
        this.dates[key].formatted = this.$moment(date).format("L");
      } else {
        this.dates[key].formatted = null;
      }
    },
    async getCities() {
      try {
        this.loading = true;
        let res = await axios.get("city");
        this.cities = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },

  }
  /*data: () => ({

        headers: [
        { text: 'Entidad Financiera', value: 'calories' },
        { text: 'Monto Adeudado', value: 'fat' },
        { text: 'Descripcion', value: 'carbs' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],

  desserts: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },

    affiliateState: [],
    category: [],
    degree: [],
    pension_entity: [],
    dialog:false,
    dates: {
      dateEntry: {
        formatted: null,
        picker: false
      },
      dateDerelict: {
        formatted: null,
        picker: false
      }
    }
  }),
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nuevo Gravamen' : 'Editar Gravamen'
      },
    },

      watch: {
      dialog (val) {
        val || this.close()
      },
    },

 created () {
      this.initialize()
    },
 
  beforeMount() {
  },
    mounted() {
  },

  methods: {

  initialize () {
        this.desserts = [
          {
            name: 'Frozen Yogurt',
            calories: 159,
            fat: 6.0,
            carbs: 24,
            protein: 4.0,
          },
          {
            name: 'Ice cream sandwich',
            calories: 237,
            fat: 9.0,
            carbs: 37,
            protein: 4.3,
          },
        ]
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.desserts.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.desserts.splice(index, 1)
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      },    
   
  
    
  }*/
};
</script>