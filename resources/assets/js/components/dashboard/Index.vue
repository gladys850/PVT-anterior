<template>
<v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Préstamos</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
      <v-card-text>
  <v-container fluid class="py-0 px-0">
    <ValidationObserver ref="observer">
      <v-form>
        <!--v-card-->
        <v-row justify="center" >
            <v-col cols="12" class="py-0 px-0">
              <v-container class="py-0  ">
                <v-row class="py-0">
                  <v-col cols="12" class="py-0">
                    <v-tabs dark active-class="secondary">
                      <v-tab>Información de Préstamos</v-tab>
                        <v-tab-item>
                          <DataLoanInformation/>
                        </v-tab-item>
                      <v-tab>CALCULADORA</v-tab>
                        <v-tab-item >
                          <DataLoanAffiliate></DataLoanAffiliate>
                      </v-tab-item>
                    </v-tabs>
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
        <!--/v-card-->
      </v-form>
    </ValidationObserver>
  </v-container>
    </v-card-text>
  </v-card>
</template>
<script>
import DataLoanInformation from '@/components/dashboard/DataLoanInformation'
import DataLoanAffiliate from '@/components/dashboard/DataLoanAffiliate'

export default {
  name: "index",
  components: {
    DataLoanInformation,
    DataLoanAffiliate
  },

   data: () => ({
     civil_statuses: [
      { name: "Soltero", value: "S" },
      { name: "Casado", value: "C" },
      { name: "Viudo", value: "V" },
      { name: "Divorciado", value: "D" }
    ],
    items_measurement: [
      { name: "Metros cuadrados", value: "METROS CUADRADOS" },
      { name: "Hectáreas", value: "HECTÁREAS" }
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
    ],
      dialog: false,
      dialog1: false,
      editedIndex: -1,
      editedItem: {},
      defaultItem: {},
      editedItem1: {},
      defaultItem1: {},
      headers: [
        {
          text: 'PRIMER NOMBRE',
          align: 'start',
          sortable: false,
          value: 'first_name',
        },
        { text: 'SEGUNDO NOMBRE',  value: 'second_name' },
        { text: 'PRIMER APELLIDO ', value: 'last_name' },
        { text: 'SEGUNDO APELLIDO ', value: 'mothers_last_name' },
        { text: 'TELEFONO', value: 'phone_number' },
        { text: 'CELULAR', value: 'cell_phone_number' },
        { text: 'DIRECCION ', value: 'address' },
        {
      text: "Actions",
      value: "actions",
      sortable: false
    }
      ],

    editable1: false,
    editable: false,
    reload: false,
    payment_types:[],
    city: [],
    entity: [],
    entities:null,
  }),

 
 
  methods:{

  }
}
</script>