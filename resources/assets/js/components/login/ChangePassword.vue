<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="480">
      <template v-slot:activator="{ on }">
        <v-btn small color="primary" v-on="on">Cambiar contraseña</v-btn>
      </template>
      <template>
        <v-card flat>
          <template>
            <v-toolbar flat dense color="tertiary">
              <v-toolbar-title>Cambiar contraseña</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon @click.stop="close()">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
          </template>
          <template v-if="!loading">
            <v-card-title></v-card-title>
            <v-card-text>
              <form>
                <v-text-field
                  v-validate="'required|min:5|max:255'"
                  v-model="oldPassword"
                  label="Contraseña Anterior"
                  type="password"
                  autocomplete="off"
                  ref="oldPassword"
                  name="contraseña anterior"
                  :error-messages="errors.collect('contraseña anterior')"
                  @keyup.enter="$refs['newPassword'].focus()"
                ></v-text-field>
                <v-text-field
                  v-validate="'required|min:5|max:255'"
                  v-model="newPassword"
                  label="Contraseña Nueva"
                  type="password"
                  autocomplete="off"
                  ref="newPassword"
                  name="contraseña nueva"
                  :error-messages="errors.collect('contraseña nueva')"
                  @keyup.enter="$refs['confirmPassword'].focus()"
                ></v-text-field>
                <v-text-field
                  v-validate="'required|min:5|max:255'"
                  v-model="confirmPassword"
                  label="Repita la Contraseña"
                  type="password"
                  autocomplete="off"
                  ref="confirmPassword"
                  name="confirmación de contraseña"
                  :error-messages="errors.collect('confirmación de contraseña')"
                  @keyup.enter="updatePassword()"
                ></v-text-field>
              </form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" @click="updatePassword()">Cambiar contraseña</v-btn>
            </v-card-actions>
          </template>
          <Loading v-else/>
        </v-card>
      </template>
    </v-dialog>
  </v-row>
</template>

<script>
import Loading from '@/components/shared/Loading'

export default {
  name: 'change-password',
  components: {
    Loading
  },
  data: () => ({
    dialog: false,
    loading: false,
    oldPassword: null,
    newPassword: null,
    confirmPassword: null,
  }),
  watch: {
    dialog(val) {
      if (!val) {
        this.clearForm()
      }
    }
  },
  methods: {
    clearForm() {
      this.oldPassword = this.newPassword = this.confirmPassword = null
      this.$validator.reset()
    },
    close() {
      this.dialog = false
    },
    async updatePassword() {
      try {
        if (await this.$validator.validateAll()) {
          if (this.newPassword != this.confirmPassword) {
            this.newPassword = this.confirmPassword = null
            this.$refs.newPassword.focus()
            this.toast('Las contraseñas no coinciden', 'error')
          } else {
            this.loading = true
            if (this.newPassword != this.oldPassword) {
              await axios.patch(`user/${this.$store.getters.id}`, {
                old_password: this.oldPassword,
                password: this.newPassword
              })
            }
            this.toast('Contraseña actualizada correctamente', 'success')
            this.$store.dispatch("logout")
            this.$router.push("login")
          }
        }
      } catch (e) {
        this.clearForm()
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
