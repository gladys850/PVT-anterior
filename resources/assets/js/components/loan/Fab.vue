<template>
  <v-tooltip left>
    <template v-slot:activator="{ on }">
      <v-btn
        fab
        dark
        small
        color="success"
        bottom
        right
        absolute
        v-on="on"
        style="margin-right: -9px;"
        :to="{ name: 'affiliateAdd', params: { id:'new'} }"
      >
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </template>
    <span>Nuevo trámite</span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'loan-fab'
}
</script>