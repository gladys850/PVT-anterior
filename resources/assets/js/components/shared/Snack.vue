<template>
  <v-snackbar
    v-model="display"
    :color="color"
    :timeout="4000"
    right
    top
    multi-line
  >
    {{ message }}
    <v-btn icon @click.stop="display = false">
      <v-icon>mdi-close</v-icon>
    </v-btn>
  </v-snackbar>
</template>

<script>
import { setTimeout } from 'timers';
export default {
  name: 'snack',
  props: {
    display: {
      type: Boolean,
      default: false
    },
    message: {
      type: String,
      default: ''
    },
    color: {
      type: String,
      default: 'success'
    }
  }
}
</script>