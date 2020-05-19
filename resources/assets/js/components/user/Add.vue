<template>
  <div>
    <v-tooltip top>
      <template v-slot:activator="{ on }">
        <v-btn
          fab
          dark
          x-small
          v-on="on"
          color="info"
          @click.stop="dialog = true"
        >
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <span>Añadir usuario</span>
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
        <ValidationObserver ref="observer"  v-slot="{ invalid }">
          <v-card-text>
              <Ldap v-if="$store.getters.ldapAuth" :bus="bus" @input="setUser($event)"/>
              <Form v-else :bus="bus" @input="setUser($event)"/>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              @click="saveUser()"
              :disabled="invalid"
            >Añadir</v-btn>
          </v-card-actions>
        </ValidationObserver>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import Ldap from '@/components/user/Ldap'
import Form from '@/components/user/Form'

export default {
  name: 'ldap-add',
  components: {
    Ldap,
    Form
  },
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    loading: true,
    dialog: false,
    userSelected: null
  }),
  watch: {
    dialog(val) {
      if (!val) {
        this.clearForm()
      } else {
        this.bus.$emit('getUsers')
      }
    },
  },
  methods: {
    setUser(val) {
      this.userSelected = val
    },
    clearForm() {
      this.userSelected = null
      this.$refs.observer.reset()
    },
    close() {
      this.dialog = false
      this.$emit('closeFab')
    },
    async saveUser() {
      try {
        if (await this.$refs.observer.validate()) {
          this.loading = true
          let res = await axios.post(`user`, this.userSelected)
          this.toastr.success('Usuario adicionado')
          this.bus.$emit('added', res.data)
          this.clearForm()
          this.close()
        } else {
          this.toastr.warning('Debe seleccionar un usuario')
        }
      } catch (e) {
        this.$refs.observer.setErrors(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>