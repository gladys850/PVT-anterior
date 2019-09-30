<template>
  <div style="margin-right: -20px;" class="mt-3">
    <v-speed-dial
      v-model="fab"
      direction="left"
      transition="slide-x-reverse-transition"
      top
      right
    >
      <template v-slot:activator>
        <v-btn
          v-model="fab"
          color="info"
          dark
          fab
          small
        >
          <v-icon v-if="fab">mdi-close</v-icon>
          <v-icon v-else>mdi-account-circle</v-icon>
        </v-btn>
      </template>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn
            fab
            dark
            x-small
            v-on="on"
            color="success"
            @click.stop="dialog = true; fab = false"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>Añadir usuario</span>
      </v-tooltip>
      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn
            fab
            dark
            x-small
            color="danger"
            v-on="on"
            @click.stop="fab = false"
          >
            <v-icon>mdi-sync</v-icon>
          </v-btn>
        </template>
        <span>Sincronizar con LDAP</span>
      </v-tooltip>
    </v-speed-dial>
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
          <Ldap :bus="bus" @input="userSelected = $event"/>
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
import Ldap from '@/components/user/Ldap'

export default {
  name: 'ldap-list',
  components: {
    Ldap
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
    fab: false,
    userSelected: null
  }),
  watch: {
    dialog(val) {
      if (!val) {
        this.clearForm()
      } else {
        this.bus.$emit('getUsers')
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
            position: this.userSelected.title
          })
          this.toast('Usuario adicionado', 'success')
          this.bus.$emit('added', res.data)
          this.clearForm()
          this.close()
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

