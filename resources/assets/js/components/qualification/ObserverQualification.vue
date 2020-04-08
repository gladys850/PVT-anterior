<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="11" class="v-card-profile" >
              <v-row justify="center">
              <v-col cols="12">
                <v-toolbar-title>OBSERVACIONES</v-toolbar-title>
              </v-col>
                <v-col cols="12">
                <v-toolbar-title>DERIVACIONES</v-toolbar-title>
              </v-col>
            <v-col cols="12" md="4" >
              <v-select
                dense
                :items="affiliateState"
                data-vv-name="Estado"
                item-text="name"
                item-value="id"
                label="Estado"
                persistent-hint
              ></v-select>
            </v-col>
          
          </v-row>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
import { Validator } from 'vee-validate'

export default {
  inject: ['$validator'],
  name: "observer-qualification",

  data: () => ({
    affiliateState: [],
    category: [],
    degree: [],
    pension_entity: [],
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
  
  beforeMount() {
  },
  
  methods: {
  
  }
  }
</script>