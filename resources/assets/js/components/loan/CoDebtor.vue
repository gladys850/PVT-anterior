<template>
<div class="ma-3 pa-0">
  <v-data-table
    :headers="headers"
    :items="personal_references"
    sort-by="identity_card"
    class="elevation-1 ma-0 pa-3"
  >
    <template v-slot:top>
      <v-toolbar flat color="white">
        <v-toolbar-title>DATOS DEL CODEUDOR</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              fab
              dark
              x-small
              v-on="on"
              color="info"
              v-bind="attrs"
            ><v-icon>mdi-plus</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.identity_card" label="CI"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.city_identity_card_id" label="Ciudad de Expedición"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.first_name" label="Primer Nombre"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.second_name" label="Segundo nombre"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.last_name" label="Primer Apellido"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.mothers_last_name" label="Segundo Apellido"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.phone_number" label="Teléfono"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="3">
                    <v-text-field v-model="editedItem.cell_phone_number" label="Celular"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="12">
                    <v-text-field v-model="editedItem.address" label="Dirección"></v-text-field>
                  </v-col>
                  
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close">Cancelar</v-btn>
              <v-btn color="blue darken-1" text @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon
        small
        class="mr-2"
        color="success"
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        color="error"
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <!--v-btn color="primary" @click="initialize">Reset</v-btn>-->
    </template>
  </v-data-table>
  {{personal_references}}
</div>
</template>

<script>
  export default {
    name: "loan-codebtor",
    props:{
      references:{
        type: Boolean,
        requiered: true
      }
    },
    data: () => ({
      dialog: false,
      headers: [
        { text: 'CI', align: 'start', sortable: false, value: 'identity_card'},
        { text: 'Ciudad de Expedición', value: 'city_identity_card_id' },
        { text: 'Primer Nombre', value: 'first_name' },
        { text: 'Segundo Nombre', value: 'second_name' },
        { text: 'Primer Apellido', value: 'last_name' },
        { text: 'Segundo Apellido', value: 'mothers_last_name' },
        { text: 'Teléfono', value: 'phone_number' },
        { text: 'Celular', value: 'cell_phone_number' },
        { text: 'Dirección', value: 'address' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      personal_references: [],
      editedIndex: -1,
      editedItem: {
        identity_card: null,
        city_identity_card_id: null,
        first_name: null,
        second_name: null,
        last_name: null,
        mothers_last_name: null,
        phone_number: null,
        cell_phone_number: null,
        address: null
      },
      defaultItem: {
        identity_card: null,
        city_identity_card_id: null,
        first_name: null,
        second_name: null,
        last_name: null,
        mothers_last_name: null,
        phone_number: null,
        cell_phone_number: null,
        address: null
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nuevo' : 'Editar'
      },
    },

    watch: {
      dialog (val) {
        console.log("entro aqui"+ val)
        val || this.close()
      },
    },

    /*created () {
      this.initialize()
    },*/

    methods: {
      /*initialize () {
        this.personal_references = [
          /*{
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
          }
        ]
      },*/

      editItem (item) {
        this.editedIndex = this.personal_references.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.personal_references.indexOf(item)
       this.personal_references.splice(index, 1)
       this.toastr.success( 'El registro fue removido')
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
          Object.assign(this.personal_references[this.editedIndex], this.editedItem)
          console.log( this.editedIndex )//obtener el indice
          console.log( this.editedItem)//obtener el objeto
        } else {
          this.personal_references.push(this.editedItem)
          this.references = this.personal_references
          console.log("nuevo editedIndex "+this.editedItem)
          console.log(this.editedItem)
        }
        this.close()
      },
    },
  }
</script>