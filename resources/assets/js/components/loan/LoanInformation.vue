<template>
  <v-container fluid >
    <v-card>
    <v-row justify="center">
      <v-col cols="12"  >
        <v-container class="py-0">
          <v-row>
            <v-col cols="12" md="4" class="py-0">
              Modalidad Prestamo
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              Monto Solicitado
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              Plazo Meses
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              <v-select
                dense
                data-vv-name="modalities"
                :items="modalities"
                item-text="name"
                item-value="value"
              ></v-select>
            </v-col>
            <v-col cols="12" md="4" class="py-0">
              <v-text-field
                dense
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('monto')"
                data-vv-name="monto"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-0" >
              <v-text-field
                dense
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('plazo')"
                data-vv-name="plazo"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-col>
    </v-row>
    </v-card>
  </v-container>
</template>
<script>
import { Validator } from 'vee-validate'
  export default {
  inject: ['$validator'],
  name: "loan-information",
  data: () => ({
    modalities: [
      { name:"Anticipo",
        value:"1"
      },
      { name:"Corto Plazo",
        value:"2"
      },
      { name:"Largo Plazo",
        value:"3"
      },
      { name:"Hipotecario",
        value:"4"
      }
    ],
  }),
  computed: {
  },
  beforeMount() {
  },
  mounted() {
  },
  methods: {
  }
  }
</script>