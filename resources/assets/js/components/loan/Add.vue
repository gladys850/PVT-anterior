<template>
  <div>
    {{ $route.params.hash }}
  </div>
</template>

<script>
export default {
  name: "loan-add",
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
  }
}
</script>