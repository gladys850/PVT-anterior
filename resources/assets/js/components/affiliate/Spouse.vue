<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="6" >
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12">
                    <v-toolbar-title>INFORMACION CONYUGE</v-toolbar-title>
                  </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                      dense
                      v-model="spouse.first_name"
                      class="purple-input"
                      label="Primer Nombre"
                      v-validate.initial="'required|min:1|max:250'"
                      :error-messages="errors.collect('primer nombre')"
                      data-vv-name="primer nombre"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                      dense
                      v-model="spouse.second_name"
                      label="Segundo Nombre"
                      class="purple-input"
                      data-vv-name="segundo nombre"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      dense
                      v-model="spouse.last_name"
                      label="Primer Apellido"
                      class="purple-input"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('primer apellido')"
                      data-vv-name="primer apellido"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      dense
                      v-model="spouse.mothers_last_name"
                      label="Segundo Apellido"
                      class="purple-input"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('segundo apellido')"
                      data-vv-name="segundo apellido"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      dense
                      v-model="spouse.surname_husband"
                      label="Apellido Casada"
                      class="purple-input"
                      v-validate.initial="'min:1|max:250'"
                      :error-messages="errors.collect('apellido casado')"
                      data-vv-name="apellido casado"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <v-text-field
                      dense
                        v-model="spouse.identity_card"
                        class="purple-input"
                        label="Cedula de Identidad"
                        v-validate.initial="'required|numeric|min:1|max:50'"
                        :error-messages="errors.collect('cedula identidad')"
                        data-vv-name="cedula identidad"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
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
                        v-model="spouse.city_identity_card_id"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                      dense
                      v-model="spouse.death_certificate_number"
                      label="Cert. de Defunción"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('cert. de defunción')"
                      data-vv-name="cert. de defunción"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
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
                          <v-text-field
                            dense
                            v-model="dates.birthDate.formatted"
                            label="Fecha Nacimiento"
                            hint="Día/Mes/Año"
                            persistent-hint
                            append-icon="mdi-calendar"
                            readonly
                            v-on="on"
                            :outlined="editable && permission.secondary"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="spouse.birth_date" no-title @input="dates.birthDate.show = false"></v-date-picker>
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
                        v-model="spouse.city_birth_id"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
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
                        v-model="spouse.civil_status"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
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
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="spouse.date_death" no-title @input="dates.dateDeath.show = false"></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        dense
                        v-model="spouse.reason_death"
                        label="Causa del Fallecimiento"
                        class="purple-input"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                </v-row>
              </v-container>
        </v-col>
        <v-col cols="12" md="5" class="v-card-profile" >
        <v-col cols="12">
            <v-toolbar-title>INFORMACION DE SERECI</v-toolbar-title>
          </v-col>
          <v-col cols="12"  >
            <v-text-field
              dense
              v-model="spouse.official"
              label="Oficialia"
              class="purple-input"
              v-validate.initial="'min:1|max:250'"
              :error-messages="errors.collect('oficialia')"
              data-vv-name="oficialia"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
          </v-col>
          <v-col cols="12" >
            <v-text-field
              dense
              v-model="spouse.book"
              label="Libro"
              class="purple-input"
              v-validate.initial="'min:1|max:250'"
              :error-messages="errors.collect('libro')"
              data-vv-name="libro"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
          </v-col>
          <v-col cols="12" >
            <v-text-field
              dense
              v-model="spouse.departure"
              label="Partida"
              class="purple-input"
              v-validate.initial="'min:1|max:250'"
              :error-messages="errors.collect('partida')"
              data-vv-name="partida"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
          </v-col>
          <v-col cols="12"  >
            <v-menu
              v-model="dates.marriageDate.show"
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
                  v-model="dates.marriageDate.formatted"
                  label="Fecha Matrimonio"
                  hint="Día/Mes/Año"
                  persistent-hint
                  append-icon="mdi-calendar"
                  readonly
                  v-on="on"
                  :outlined="editable && permission.secondary"
                ></v-text-field>
              </template>
              <v-date-picker v-model="spouse.marriage_date" no-title @input="dates.marriageDate.show = false"></v-date-picker>
            </v-menu>
          </v-col>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
  export default {
  name: "affiliate-spouse",
  props: {
    editable: {
      type: Boolean,
      required: true
    },
    permission: {
      type: Object,
      required: true
    }
  },
  data: () => ({
  spouse: {
    first_name: null,
    second_name:null,
    last_name: null,
    mothers_last_name:null,
    identity_card:null,
    birth_date:null,
    date_death:null,
    reason_death:null,
    phone_number:null,
    cell_phone_number:null,
    city_identity_card_id:null,
    },
    loading: true,
    cities: [],
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
    city: [],
    dates: {
      birthDate: {
        formatted: null,
        picker: false
      },
      dateDeath: {
        formatted: null,
        picker: false
      },
      marriageDate: {
        formatted: null,
        picker: false
      }
    }
  }),
  beforeMount() {
    this.getCities();
  },
  mounted() {
    if (this.$route.params.id != 'new') {
      this.getSpouse(this.$route.params.id)
    }
  },
  watch: {
    'spouse.birth_date': function(date) {
      if (date) this.dates.birthDate.formatted = this.$moment(date).format('L')
    },
    'spouse.date_death': function(date) {
      if (date) this.dates.dateDeath.formatted = this.$moment(date).format('L')
    },
    'spouse.marriage_date': function(date) {
      if (date) this.dates.marriageDate.formatted = this.$moment(date).format('L')
    }
  },
  methods: {
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
  },
    async getSpouse(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}/spouse`)
        this.spouse = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
  }
</script>