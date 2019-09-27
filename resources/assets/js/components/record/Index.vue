<template>
  <v-container fluid>
    <v-toolbar dense flat color="tertiary">
      <v-toolbar-title>Registro de actividad</v-toolbar-title>
    </v-toolbar>
    <List/>
  </v-container>
</template>

<script>
import List from '@/components/record/List'

export default {
  name: 'record-index',
  components: {
    List
  }
}
</script>