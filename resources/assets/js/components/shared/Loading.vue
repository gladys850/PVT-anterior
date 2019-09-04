<template>
  <v-container fluid fill-height class="my-5 py-5">
    <v-layout row align-center justify-center>
      <v-progress-circular
        :size="100"
        :width="8"
        color="primary"
        indeterminate
      ></v-progress-circular>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  name: 'loading'
}
</script>
