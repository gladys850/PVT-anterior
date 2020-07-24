<template>
  <div>
    {{ $route.params.hash }}
  </div>
</template>

<script>
export default {
  name: "payment-add",
  computed: {
    isNew() {
      return this.$route.params.hash == 'new'
    }
  }
}
</script>