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
        <Anticipo v-if="loanTypeSelected == 'Anticipo'"/>
        <CortoPlazo v-if="loanTypeSelected == 'Corto plazo'"/>
        <LargoPlazo v-else-if="loanTypeSelected == 'Largo plazo'"/>
        <LargoPlazo v-else-if="loanTypeSelected == 'Hipotecario'"/>
      </div>
    </v-card>
  </v-container>
</template>

<script>
import Anticipo from './Anticipo'
import CortoPlazo from './CortoPlazo'
import LargoPlazo from './LargoPlazo'


export default {
  name: "dashboard-index",
  components: {
      Anticipo,
      CortoPlazo,
      LargoPlazo
  },
  data: () => ({
    loanTypes: [
      'Anticipo',
      'Corto plazo',
      'Largo plazo',
      'Hipotecario'
    ],
    loanTypeSelected: null
  })
}
</script>