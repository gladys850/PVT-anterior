<template>
  <div>
    <v-tooltip top>
      <template v-slot:activator="{ on }">
        <v-btn
          fab
          dark
          x-small
          @click.stop="dialog = true"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <span class="caption">Añadir usuario</span>
    </v-tooltip>
    <v-dialog
      v-model="dialog"
      width="500"
      persistent
    >
      <v-card>
        <v-toolbar dense flat color="tertiary">
          <v-toolbar-title>Añadir usuario</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click.stop="close()">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-title></v-card-title>
        <v-card-text>
          <v-autocomplete
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
            validate-on-blur
            v-validate="'required'"
            name="usuario"
            :error-messages="errors.collect('usuario')"
          ></v-autocomplete>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="error"
            @click="saveUser()"
            :disabled="errors.any()"
          >Añadir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'ldap-list',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: true,
    dialog: false,
    userSelected: null,
    users: []
  }),
  mounted() {
    this.getUsers()
  },
  watch: {
    dialog(val) {
      if (!val) {
        this.clearForm()
      }
    }
  },
  methods: {
    clearForm() {
      this.userSelected = null
      this.$validator.reset()
    },
    close() {
      this.dialog = false
    },
    async saveUser() {
      try {
        if (await this.$validator.validateAll()) {
          this.loading = true
          let res = await axios.post(`user`, {
            first_name: this.userSelected.givenName,
            last_name: this.userSelected.sn,
            username: this.userSelected.uid,
            password: this.userSelected.uid,
            position: this.userSelected.title,
            city_id: 1,
            phone: 12345678
          })
          console.log(res.data)
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getUsers(params) {
      try {
        this.loading = true
        let res = await axios.get(`ldap`)
        this.users = res.data
        this.users.forEach((item) => {
          item.fullName = `${item.sn} ${item.givenName}`
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

