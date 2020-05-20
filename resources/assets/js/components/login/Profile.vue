<template>
  <v-card flat>
    <v-card-title>
      <v-toolbar dense color="tertiary">
        <v-toolbar-title>Perfil</v-toolbar-title>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <v-card>
        <v-card-text>
          <v-simple-table dense>
            <tbody>
              <tr>
                <td class="font-weight-black text-end pr-5">Usuario:</td>
                <td class="text-left">{{ $store.getters.user }}</td>
                <td class="font-weight-black text-end pr-5">Roles:</td>
                <td class="text-left">
                  <ul v-if="!loading">
                    <li
                      v-for="role in roles"
                      :key="role.id"
                    >
                      {{ role.display_name }}
                    </li>
                  </ul>
                  <Loading v-else/>
                </td>
                <td v-if="!$store.getters.ldapAuth || $store.getters.username == 'admin'">
                  <ChangePassword/>
                </td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-card-text>
  </v-card>
</template>

<script>
import ChangePassword from '@/components/login/ChangePassword'
import Loading from '@/components/shared/Loading'

export default {
  name: "Profile",
  data() {
    return {
      loading: true,
      roles: []
    }
  },
  components: {
    ChangePassword,
    Loading
  },
  mounted() {
    this.$store.getters.userRoles.forEach(role => {
      this.getRole(role)
    })
  },
  methods: {
    async getRole(name) {
      try {
        this.loading = true
        let res = await axios.get(`role`, {
          params: {
            name: name
          }
        })
        if (res.data.length) this.roles.push(res.data[0])
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
