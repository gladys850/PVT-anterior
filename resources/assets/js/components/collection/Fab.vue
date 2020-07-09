<template>
  <div>
    {{ $route.params.hash }}
  </div>
</template>

<script>
export default {
  name: "collection-add",
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
  }
}
</script>