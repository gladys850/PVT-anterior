<template>
<v-container class="pa-3 ma-0">
  <v-card dense color="grey lighten-4" class="px-3 ma-0">
     
    <v-row>
      <v-col cols="3">
        <small>
          Cédula de Identidad
        </small>
        <br />
        <strong>{{affiliate.identity_card}}</strong>
      </v-col>

      <v-col cols="2">
        <small>
          Categoría
        </small>
        <br />
        <strong>{{this.category_name}}</strong>
      </v-col>

      <v-col cols="3">
        <small>
          Estado
        </small>
        <br />
        <strong>{{this.state_name_type +' ('+ this.state_name_status+')'}}</strong>
      </v-col>

      <v-col cols="4">
        <small>
          Grado
        </small>
        <br />
        <strong>{{this.degree_name}}</strong>
      </v-col>
    </v-row>
  
  </v-card>   </v-container>
</template>
<script>
export default {
  name: "informationData",
  props: {
    affiliate: {
      type: Object,
      required: true
    }
  },

  data: () => ({
    loading: true,
    category_name: null,
    state_name_type: null,
    state_name_status: null,
    degree_name: null
  }),
  watch: {
    affiliate(newVal, oldVal) {
      if (oldVal != newVal) {
        if (newVal.hasOwnProperty('category_id')) this.getCategory_name(newVal.category_id)
        if (newVal.hasOwnProperty('degree_id')) this.getDegree_name(newVal.degree_id)
      }
    }
  },
  beforeMount() {
    this.getState_name(this.affiliate.id);
  },
  methods: {
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`category/${id}`);
        this.category_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getState_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/state`);
        this.state_name = res.data;
        this.state_name_type = this.state_name.affiliate_state_type.name;
        this.state_name_status = this.state_name.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getDegree_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`degree/${id}`);
        this.degree_name = res.data.name;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>