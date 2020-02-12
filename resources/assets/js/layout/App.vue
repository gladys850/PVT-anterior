<template>
  <v-app>
    <Navbar :expanded.sync="expandNavbar" v-if="$store.getters.user"/>
    <Appbar :expanded.sync="expandNavbar" v-if="$store.getters.user"/>
    <v-content>
      <router-view></router-view>
      <div ref="container"></div>
    </v-content>
    <Footer/>
  </v-app>
</template>

<script>
import Footer from '@/layout/Footer'
import Navbar from '@/layout/Navbar'
import Appbar from '@/layout/Appbar'
import Config from '@/services/ConfigService'

export default {
  name: 'app-index',
  components: {
    Navbar,
    Appbar,
    Footer
  },
  data: () => ({
    expandNavbar: false,
  }),
  name: "app-index",
  created() {
    const config = new Config()
    config.get().then((data) => {
      this.$store.commit("setDate", data.date)
    }).catch(() => {
      this.$store.commit("setDate", this.$moment().format("YYYY-MM-DD"))
    })
  }
}
</script>

<style>
.copyleft {
  display: inline-block;
  transform: rotate(180deg);
}
</style>
