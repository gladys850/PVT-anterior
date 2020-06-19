<template>
  <v-text-field
    v-model="search"
    append-icon="mdi-magnify"
    label="Buscar"
    single-line
    hide-details
    clearable
  ></v-text-field>
</template>
<script>
export default {
  name: 'search',
  data: function() {
    return {
      search: ''
    }
  },
  watch: {
    search: _.debounce(function (newVal, oldVal) {
      if (newVal != oldVal) {
        this.$emit('search', newVal)
      }
    }, 1000)
  }
}
</script>