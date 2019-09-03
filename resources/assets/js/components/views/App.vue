<template>
  <v-app>
    <Navbar :expanded.sync="expandNavbar" v-if="$store.getters.user"/>
    <Appbar :expanded.sync="expandNavbar" v-if="$store.getters.user"/>
    <v-content>
      <router-view></router-view>
    </v-content>
    <Footer/>
  </v-app>
</template>

<script>
import Footer from './Footer'
import Navbar from './Navbar'
import Appbar from './Appbar'

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
  methods: {
    async getDate() {
      try {
        let res = await axios.get(`/date`)
        this.$store.commit("setDate", res.data.now)
      } catch (e) {
        console.log(e)
        this.$store.commit("setDate", this.$moment().format("YYYY-MM-DD"))
      }
    }
  },
  created() {
    this.getDate()
  }
};
</script>

<style>
.copyleft {
  display: inline-block;
  transform: rotate(180deg);
}
</style>
