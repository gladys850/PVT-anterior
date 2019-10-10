<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" >
              <v-container class="py-0">
                <v-row>
                    <v-col cols="12" md="5" >
                      <v-select
                        data-vv-name="Ciudad"
                        :items="cities"
                        item-text="name"
                        item-value="id"
                        :loading="loading"
                        label="Ciudad"
                        v-model="addresses.city_address_id"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" md="7">
                      <v-text-field
                      v-model="addresses.zone"
                      label="Zona"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="zona"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="9">
                      <v-text-field
                      v-model="addresses.street"
                      label="Calle/Avenida"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="calle"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                      v-model="addresses.number_address"
                      label="Nro"
                      class="purple-input"
                      v-validate.initial="'min:1|max:20'"
                      :error-messages="errors.collect('celular')"
                      data-vv-name="nro"
                      ></v-text-field>
                    </v-col>
                </v-row>
              </v-container>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
  export default {
  name: "affiliate-addresses",
  data: () => ({
    loading: true,
    addresses: {
    zone: null,
    street:null,
    number_address: null,
    city_address_id:null,
    },
    cities: [],
      }),
  beforeMount() {
    this.getCities();
  },
  mounted() {
    if (this.$route.params.id != 'new') {
      this.getAffiliate(this.$route.params.id)
    }
  },
  methods: {
    async getCities() {
    try {
      this.loading = true
      let res = await axios.get(`city`);
      this.cities = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getAffiliate(id) {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${id}`)
        this.affiliate = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    }
  }
  }
</script>