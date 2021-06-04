<template>
  <v-container fluid fill-height id="background-page">
    <v-row align="center" justify="center" v-if="!$store.getters.user">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12 pa-5">
          <v-img
            src="/img/logo.png"
            aspect-ratio="3.6"
            contain
          ></v-img>
          <v-card-title primary-title class="justify-center text-no-wrap">
            <div class="display-1 font-weight-thin text-md-center text-uppercase">
              <p>PLATAFORMA VIRTUAL</p>
              <p>DE TRÁMITES</p>
            </div>
          </v-card-title>
          <v-spacer></v-spacer>
          <v-card-text>
            <ValidationObserver ref="observer">
              <v-form>
                <ValidationProvider v-slot="{ errors }" vid="username" name="Usuario" rules="required|min:4|max:255">
                  <v-text-field
                    :error-messages="errors"
                    class="pl-5 pr-5"
                    @keyup.enter="focusPassword()"
                    v-model="auth.username"
                    prepend-icon="mdi-account"
                    label="Usuario"
                    autocomplete="on"
                    autofocus
                  ></v-text-field>
                </ValidationProvider>
                <ValidationProvider v-slot="{ errors }" name="Contraseña" :rules="isProduction">
                  <v-text-field
                    :error-messages="errors"
                    class="pl-5 pr-5 mb-3"
                    @keyup.enter="authenticate"
                    v-model="auth.password"
                    prepend-icon="mdi-key"
                    label="Contraseña"
                    type="password"
                    autocomplete="on"
                    ref="password"
                  ></v-text-field>
                </ValidationProvider>
              </v-form>
            </ValidationObserver>
          </v-card-text>
          <v-card-actions>
            <v-btn
              @click="authenticate"
              primary
              large
              block
              color="secondary"
            >Ingresar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "login",
  data() {
    return {
      auth: {
        username: "",
        password: ""
      },
      error: null
    };
  },
  computed: {
    isProduction() {
      if (process.env.NODE_ENV != 'production') {
        return ''
      } else {
        return 'required|min:4|max:255'
      }
    }
  },
  methods: {
    focusPassword() {
      if (process.env.NODE_ENV != 'production') {
        this.authenticate()
      } else {
        this.$refs.password.focus()
      }
    },
    async authenticate() {
      try {
        if (await this.$refs.observer.validate()) {
          let res = await axios.post(`auth`, this.auth)
          this.$store.dispatch('login', res.data)
        }
      } catch (e) {
        this.$refs.observer.setErrors(e)
        this.auth.password = ''
        if (process.env.NODE_ENV == 'production') {
          this.focusPassword()
        }
      }
    }
  }
}
</script>

<style>
#background-page {
  background: linear-gradient(to bottom, #263238 0%, #cfd8dc 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6ba872', endColorstr='#07540f', GradientType=0 );
}
</style>
