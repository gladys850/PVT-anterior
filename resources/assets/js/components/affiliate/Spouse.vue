<template>
  <v-container fluid >
    <ValidationObserver ref="observer">
    <v-form>
      <v-row justify="center">
        <v-col cols="12" md="7" >
              <v-container class="py-0">
                <v-row>
                  <v-col cols="12">
                    <v-toolbar-title>INFORMACION CONYUGE</v-toolbar-title>
                  </v-col>
                    <v-col cols="12" md="6" >
                      <ValidationProvider v-slot="{ errors }" vid="first_name" name="primer nombre" rules="required|min:1|max:250">
                      <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="spouse.first_name"
                      class="purple-input"
                      label="Primer Nombre"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="6" >
                      <v-text-field
                      dense
                      v-model="spouse.second_name"
                      label="Segundo Nombre"
                      class="purple-input"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="last_name" name="primer apellido" rules="min:1|max:250">
                      <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="spouse.last_name"
                      label="Primer Apellido"
                      class="purple-input"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="mothers_last_name" name="segundo apellido" rules="min:1|max:250">
                      <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="spouse.mothers_last_name"
                      label="Segundo Apellido"
                      class="purple-input"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="surname_husband" name="apellido casado" rules="min:1|max:250">
                      <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="spouse.surname_husband"
                      label="Apellido Casada"
                      class="purple-input"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="4" >
                      <ValidationProvider v-slot="{ errors }" vid="identity_card" name="cédula identidad" rules="required|min:1|max:50">
                      <v-text-field
                        :error-messages="errors"
                        dense
                        v-model="spouse.identity_card"
                        class="purple-input"
                        label="Cedula de Identidad"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                    <v-col cols="12" md="5" >
                      <v-select
                        dense
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
                    <v-col cols="12" md="3">
                      <v-checkbox
                        v-model="spouse.is_duedate_undefined"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                        :label="`Indefinido`"
                      ></v-checkbox>
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
                    <v-col cols="12" md="4">
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
                    <v-col cols="12" md="4">
                      <v-text-field
                        dense
                        v-model="spouse.reason_death"
                        label="Causa del Fallecimiento"
                        class="purple-input"
                        :readonly="!editable || !permission.secondary"
                        :outlined="editable && permission.secondary"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                      <ValidationProvider v-slot="{ errors }" vid="death_certificate_number" name="cert. de defunción" rules="min:1|max:20">
                      <v-text-field
                      :error-messages="errors"
                      dense
                      v-model="spouse.death_certificate_number"
                      label="Cert. de Defunción"
                      class="purple-input"
                      :readonly="!editable || !permission.secondary"
                      :outlined="editable && permission.secondary"
                      ></v-text-field>
                      </ValidationProvider>
                    </v-col>
                </v-row>
              </v-container>
        </v-col>
        <v-col cols="12" md="4" class="v-card-profile" >
        <v-col cols="12">
            <v-toolbar-title>INFORMACION DE SERECI</v-toolbar-title>
          </v-col>
          <v-col cols="12"  >
            <ValidationProvider v-slot="{ errors }" vid="official" name="oficialía" rules="min:1|max:250">
            <v-text-field
              :error-messages="errors"
              dense
              v-model="spouse.official"
              label="Oficialia"
              class="purple-input"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="12" >
            <ValidationProvider v-slot="{ errors }" vid="book" name="libro" rules="min:1|max:250">
            <v-text-field
              :error-messages="errors"
              dense
              v-model="spouse.book"
              label="Libro"
              class="purple-input"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
            </ValidationProvider>
          </v-col>
          <v-col cols="12" >
            <ValidationProvider v-slot="{ errors }" vid="departure" name="partida" rules="min:1|max:250">
            <v-text-field
              :error-messages="errors"
              dense
              v-model="spouse.departure"
              label="Partida"
              class="purple-input"
              :readonly="!editable || !permission.secondary"
              :outlined="editable && permission.secondary"
            ></v-text-field>
            </ValidationProvider>
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
    </v-form>
    </ValidationObserver>
  </v-container>
</template>
<script>

export default {
  name: "affiliate-spouse",
  props: {
    spouse: {
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
  data: () => ({
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
    this.getCities()
  },
  mounted(){
    if(this.spouse.id){
      this.formatDate('birthDate', this.spouse.birth_date),
      this.formatDate('dateDeath', this.spouse.date_death),
      this.formatDate('marriageDate', this.spouse.marriage_date)
    }
  },
  watch: {
    'spouse.birth_date': function(date) {
     this.formatDate('birthDate', date)
    },
    'spouse.date_death': function(date) {
      this.formatDate('dateDeath', date)
    },
    'spouse.marriage_date': function(date) {
      this.formatDate('marriageDate', date)
    }
  },
  methods: {
    formatDate(key, date){
      if(date){
        this.dates[key].formatted = this.$moment(date).format('L')
      }else{
        this.dates[key].formatted = null
      }
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
  },
  }
  }
</script>