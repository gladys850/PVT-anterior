<template>
  <div class="ma-3 pa-0">
    <v-data-table
      dense
      :headers="headers"
      :items="personal_codebtor"
      sort-by="identity_card"
      class="elevation-1 ma-0 pa-3"
      hide-default-footer
    >
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-toolbar-title>DATOS DEL CODEUDOR</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="600px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn fab dark x-small v-on="on" color="info" v-bind="attrs" @click="checkLimit()">
                <v-icon>mdi-plus</v-icon>
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
                      <v-text-field dense v-model="editedItem.first_name" label="Primer Nombre"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field dense v-model="editedItem.second_name" label="Segundo nombre"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field dense v-model="editedItem.last_name" label="Primer Apellido"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        dense
                        v-model="editedItem.mothers_last_name"
                        label="Segundo Apellido"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field dense v-model="editedItem.identity_card" label="CI"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="editedItem.city_identity_card_id"
                        dense
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        label="Ciudad de Expedición"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        dense
                        :items="genders"
                        item-text="name"
                        item-value="value"
                        v-model="editedItem.gender"
                       
                        label="Género"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        dense
                        
                        :items="civil_statuses"
                        item-text="name"
                        item-value="value"
                        label="Estado civil"
                        v-model="editedItem.civil_status"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="editedItem.city_birth_id"
                        dense
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        label="Ciudad de Nacimiento"
                      ></v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                      <v-text-field dense v-model="editedItem.phone_number" label="Teléfono"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field dense v-model="editedItem.cell_phone_number" label="Celular"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="12">
                      <v-text-field dense v-model="editedItem.address" label="Dirección"></v-text-field>
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
        <v-icon small class="mr-2" color="success" @click="editItem(item)">mdi-pencil</v-icon>
        <v-icon small color="error" @click="deleteItem(item)">mdi-delete</v-icon>
      </template>
      <template v-slot:no-data>
        <!--v-btn color="primary" @click="initialize">Reset</v-btn>-->
      </template>
    </v-data-table>
  </div>
</template>

<script>
export default {
  name: "loan-codebtor",
  props: {
    personal_codebtor: {
      type: Array,
      required: true
    },
    modalidad: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog: false,
    headers: [
      {
        text: "CI",
        align: "start",
        class: ["normal", "white--text"],
        sortable: false,
        value: "identity_card"
      },
      {
        text: "Ciudad de Expedición",
        class: ["normal", "white--text"],
        value: "city_identity_card_id"
      },
      {
        text: "Primer Nombre",
        class: ["normal", "white--text"],
        value: "first_name"
      },
      {
        text: "Segundo Nombre",
        class: ["normal", "white--text"],
        value: "second_name"
      },
      {
        text: "Primer Apellido",
        class: ["normal", "white--text"],
        value: "last_name"
      },
      {
        text: "Segundo Apellido",
        class: ["normal", "white--text"],
        value: "mothers_last_name"
      },
      {
        text: "Teléfono",
        class: ["normal", "white--text"],
        value: "phone_number"
      },
      {
        text: "Celular",
        class: ["normal", "white--text"],
        value: "cell_phone_number"
      },
      { text: "Dirección", class: ["normal", "white--text"], value: "address" },
      {
        text: "Actions",
        class: ["normal", "white--text"],
        value: "actions",
        sortable: false
      }
    ],

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
      address: null,
      civil_status: null,
      gender: null,
      city_birth_id: null
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
      address: null,
      civil_status: null,
      gender: null,
      city_birth_id: null
    },
    cities: [],
    //personal_codebtor: [],
    civil_statuses: [
      { name: "Soltero", value: "S" },
      { name: "Casado", value: "C" },
      { name: "Viudo", value: "V" },
      { name: "Divorciado", value: "D" }
    ],
    genders: [
      {
        name: "Femenino",
        value: "F"
      },
      {
        name: "Masculino",
        value: "M"
      }
    ]
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo" : "Editar";
    }
  },

  watch: {
    dialog(val) {
      console.log("entro aqui" + val);
      val || this.close();
    }
  },

  /*created () {
      this.initialize()
    },*/
  mounted() {
    this.getCities();
  },
  methods: {
    /*initialize () {
        this.personal_codebtor = [
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

    editItem(item) {
      this.editedIndex = this.personal_codebtor.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      const index = this.personal_codebtor.indexOf(item);
      this.personal_codebtor.splice(index, 1);
      this.toastr.success("El registro fue removido");
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(
          this.personal_codebtor[this.editedIndex],
          this.editedItem
        );
        console.log(this.editedIndex); //obtener el indice
        console.log(this.editedItem); //obtener el objeto
      } else {
        this.personal_codebtor.push(this.editedItem);
        console.log("nuevo editedIndex " + this.editedItem);
        console.log(this.editedItem);
      }
      this.close();
    },
    async getCities() {
      try {
        //this.loading = true;
        let res = await axios.get(`city`);
        this.cities = res.data;
      } catch (e) {
        this.dialog = false;
        console.log(e);
      } finally {
        //this.loading = false;
      }
    },
    /* async savePersonalReference() {
      try {
        let i
        let ids_codebtor=[]
        for (i = 0; i < this.personal_codebtor.length; i++) {
          let res = await axios.post(`personal_reference`, {
            city_identity_card_id: this.personal_codebtor[i]
              .city_identity_card_id,
            identity_card: this.personal_codebtor[i].identity_card,
            last_name: this.personal_codebtor[i].last_name,
            mothers_last_name: this.personal_codebtor[i].mothers_last_name,
            first_name: this.personal_codebtor[i].first_name,
            second_name: this.personal_codebtor[i].second_name,
            phone_number: this.personal_codebtor[i].phone_number,
            cell_phone_number: this.personal_codebtor[i].cell_phone_number,
            address: this.personal_codebtor[i].address,
            civil_status: this.personal_codebtor[i].civil_status,
            gender: this.personal_codebtor[i].gender,
            cosigner: false,
            city_birth_id: this.personal_codebtor[i].city_birth_id
          });
          ids_codebtor.push(res.data.id);
          console.log(this.personal_codebtor.length);
          console.log(ids_codebtor);
        }
        this.cosigners = ids_codebtor
        console.log(this.cosigners);
      } catch (e) {
        this.dialog = false;
        console.log(e);
      } finally {
        this.loading = false;
      }
    },*/
    checkLimit() {
      if (this.personal_codebtor.length < this.modalidad.max_cosigner) {
        console.log("no hacer nada");
      } else {
        this.dialog = false;
        this.toastr.error(
          "El número máximo de codeudores es: " + this.modalidad.max_cosigner
        );
      }
    }
    /*compareObj(a, b) {
      var aKeys = Object.keys(a).sort();
      var bKeys = Object.keys(b).sort();
      if (aKeys.length !== bKeys.length) {
        return false;
      }
      if (aKeys.join("") !== bKeys.join("")) {
        return false;
      }
      for (var i = 0; i < aKeys.length; i++) {
        if (a[aKeys[i]] !== b[bKeys[i]]) {
          return false;
        }
      }
      return true;
    }*/
  }
};
</script>