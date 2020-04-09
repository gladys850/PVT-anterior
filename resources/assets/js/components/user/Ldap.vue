<template>
  <ValidationObserver ref="observer">
    <v-form>
      <ValidationProvider v-slot="{ errors }" name="usuario" rules="required">
        <v-autocomplete
          :error-messages="errors"
          v-model="userSelected"
          label="Usuario"
          :items="users"
          :loading="loading"
          autofocus
          clearable
          persistent-hint
          :hint="userSelected ? userSelected.title : ''"
          item-text="fullName"
          return-object
          open-on-clear
          name="usuario"
        ></v-autocomplete>
      </ValidationProvider>
    </v-form>
  </ValidationObserver>
</template>

<script>
export default {
  name: 'ldap-user-form',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: true,
    userSelected: null,
    users: []
  }),
  mounted() {
    this.getUsers()
    this.bus.$on('getUsers', () => {
      this.userSelected = null
      this.getUsers()
    })
  },
  watch: {
    userSelected(val) {
      if (val) {
        this.$emit('input', val)
      }
    }
  },
  methods: {
    async getUsers(params) {
      try {
        this.loading = true
        let res = await axios.get(`user/ldap/unregistered`)
        this.users = res.data
        this.users.forEach((item) => {
          item.fullName = `${item.sn} ${item.givenname}`
        })
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>