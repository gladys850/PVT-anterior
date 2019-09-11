<template>
  <v-container fluid>
    <v-card>
      <v-card-text>
        <v-select
          :items="loanTypes"
          label="Tipo de préstamo"
          v-model="loanTypeSelected"
        ></v-select>
      </v-card-text>
      <div>
        <CortoPlazo v-if="loanTypeSelected == 'Corto plazo'"/>
        <LargoPlazo v-else-if="loanTypeSelected == 'Largo plazo'"/>
      </div>
    </v-card>
  </v-container>
</template>

<script>
import CortoPlazo from './CortoPlazo'
import LargoPlazo from './LargoPlazo'

export default {
  name: "dashboard-index",
  components: {
      CortoPlazo,
      LargoPlazo
  },
  data: () => ({
    loanTypes: [
      'Corto plazo',
      'Largo plazo'
    ],
    loanTypeSelected: null
  })
}
</script>