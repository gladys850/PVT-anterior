<template>
  <v-container fluid >
    <ValidationObserver ref="observer">
    <v-form>
      <v-row justify="center">
        <v-col cols="12">
                <h3 class="text-uppercase text-center">INFORMACION DEL INMUEBLE</h3>
              </v-col>
          <v-col cols="12" md="6" class="v-card-profile" >
              <v-row justify="center">
            <v-col cols="12" md="6" class="py-0">
              <small>
              <v-text-field
                dense
                label="Nro de Lote de Terreno"
                readonly
                outlined
              ></v-text-field>
              </small>
            </v-col>
             <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Unidad Vecinal"
                readonly
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0" >
               <v-text-field
                dense
                label="Urbanizacion"
                readonly
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Superficie"
                readonly
                outlined
              ></v-text-field>
            </v-col>
             <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Codigo Catastral"
                readonly
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0">
               <v-text-field
                dense
                label="Colindancias"
                readonly
                outlined
              ></v-text-field>
            </v-col>
                <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Nro de Escritura Publica"
                readonly
                outlined
              ></v-text-field>
            </v-col>
             <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Fecha de Escritura Publica"
                readonly
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0" >
               <v-text-field
                dense
                label="Nro Matricula Computarizada"
                readonly
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="py-0">
              <v-text-field
                dense
                label="Ciudad de Registro en Folio Real"
                readonly
                outlined
              ></v-text-field>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" md="6" class="v-card-profile" >
              <v-row justify="center">
       
           
            <v-col cols="12" md="6" class="py-0">
               <v-text-field
                dense
                label="Nro Asiento Folio Real"
                readonly
                outlined
              ></v-text-field>
            </v-col>
             <v-col cols="12" md="6" class="py-0">
            </v-col>
            <v-col cols="12" md="12" class="py-0">
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
            </v-col>
          </v-row>
        </v-col>
        
      </v-row>
    </v-form>
    </ValidationObserver>
  </v-container>
</template>

<script>
export default {

  name: "hipotecari-data",
  data: () => ({

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
   
  
    
  }}
</script>