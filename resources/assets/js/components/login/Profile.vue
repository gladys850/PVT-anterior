<template>
  <v-container fluid>
    <v-toolbar dense color="tertiary">
      <v-toolbar-title>Perfil</v-toolbar-title>
    </v-toolbar>
    <v-card>
      <v-card-title></v-card-title>
      <v-card-text>
        <v-simple-table dense>
          <tbody>
            <tr>
              <td class="font-weight-black text-end pr-5">Usuario:</td>
              <td class="text-left">{{ $store.getters.user }}</td>
              <td class="font-weight-black text-end pr-5">Roles:</td>
              <td class="text-left">{{ $store.getters.roles.join(', ') }}</td>
              <td v-if="!$store.getters.ldapAuth || $store.getters.user == 'admin'">
                <ChangePassword/>
              </td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import ChangePassword from '@/components/login/ChangePassword'

export default {
  name: "Profile",
  components: {
    ChangePassword
  }
}
</script>
