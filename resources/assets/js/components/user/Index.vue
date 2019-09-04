<template>
  <v-container fluid>
    <v-toolbar>
      <v-toolbar-title v-if="$store.getters.ldapAuth">
        <v-select
          :items="['Usuarios', 'LDAP']"
          v-model="viewType"
          class="title font-weight-medium"
        ></v-select>
      </v-toolbar-title>
      <v-toolbar-title v-else>Usuarios</v-toolbar-title>
      <v-spacer></v-spacer>
      <template v-if="viewType == 'Usuarios'">
        <v-btn  @click="status = 'active'" :class="this.status == 'active' ? 'primary' : 'normal'" class="mr-0">
          <div class="font-weight-regular subheading pa-2 white--text">ACTIVOS</div>
        </v-btn>
        <v-btn  @click="status = 'inactive'" :class="this.status == 'inactive' ? 'primary' : 'normal'" class="mr-3">
          <div class="font-weight-regular subheading pa-2 white--text">INACTIVOS</div>
        </v-btn>
      </template>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-flex xs2>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Buscar"
          single-line
          hide-details
          full-width
          clearable
        ></v-text-field>
      </v-flex>
    </v-toolbar>
    <v-card>
      <v-card-text>
        <LdapUsers v-if="viewType == 'LDAP'" :bus="bus"/>
        <DatabaseUsers v-else :bus="bus"/>
      </v-card-text>
    </v-card>
  </v-container>
</template>
<script>
import Vue from 'vue'
import DatabaseUsers from '@/components/user/DatabaseUsers'
import LdapUsers from '@/components/user/LdapUsers'
import _ from 'lodash'

export default {
  name: "userIndex",
  components: {
    DatabaseUsers,
    LdapUsers
  },
  data: () => ({
    viewType: 'Usuarios',
    search: '',
    bus: new Vue(),
    status: 'active'
  }),
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
    status: function() {
      this.bus.$emit('status', this.status)
    }
  }
}
</script>
