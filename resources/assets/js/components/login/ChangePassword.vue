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
            <ValidationObserver ref="observer" v-slot="{ invalid }">
              <v-form>
                <v-card-text>
                  <ValidationProvider v-slot="{ errors }" vid="oldPassword" name="contraseña anterior" rules="required|min:5|max:255">
                    <v-text-field
                      :error-messages="errors"
                      v-model="oldPassword"
                      label="Contraseña Anterior"
                      type="password"
                      autocomplete="off"
                      ref="oldPassword"
                      name="contraseña anterior"
                      @keyup.enter="$refs['newPassword'].focus()"
                    ></v-text-field>
                  </ValidationProvider>
                  <ValidationProvider v-slot="{ errors }" vid="newPassword" name="contraseña nueva" rules="required|min:5|max:255">
                    <v-text-field
                      :error-messages="errors"
                      v-model="newPassword"
                      label="Contraseña Nueva"
                      type="password"
                      autocomplete="off"
                      ref="newPassword"
                      name="contraseña nueva"
                      @keyup.enter="$refs['confirmPassword'].focus()"
                    ></v-text-field>
                  </ValidationProvider>
                  <ValidationProvider v-slot="{ errors }" vid="confirmPassword" name="confirmación de contraseña" rules="required|min:5|max:255|confirmed:newPassword" mode="aggressive">
                    <v-text-field
                      :error-messages="errors"
                      v-model="confirmPassword"
                      label="Repita la Contraseña"
                      type="password"
                      autocomplete="off"
                      ref="confirmPassword"
                      name="confirmación de contraseña"
                      @keyup.enter="updatePassword()"
                    ></v-text-field>
                  </ValidationProvider>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn :disabled="invalid" color="error" @click="updatePassword()">Cambiar contraseña</v-btn>
                </v-card-actions>
              </v-form>
            </ValidationObserver>
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
    },
    close() {
      this.dialog = false
    },
    async updatePassword() {
      try {
        if (await this.$refs.observer.validate()) {
          if (this.newPassword != this.confirmPassword) {
            this.newPassword = this.confirmPassword = null
            this.$refs.newPassword.focus()
            this.toastr.error('Las contraseñas no coinciden')
          } else {
            this.loading = true
            if (this.newPassword != this.oldPassword) {
              await axios.patch(`user/${this.$store.getters.id}`, {
                old_password: this.oldPassword,
                password: this.newPassword
              })
            }
            this.toastr.success('Contraseña actualizada correctamente')
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
