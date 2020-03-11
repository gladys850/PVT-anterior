<template>
<v-container class="pa-3 ma-0">
  <v-card dense color="grey lighten-4" class="px-3 ma-0">
     
    <v-row>
      <v-col cols="3">
        <small>
          <strong>Cédula de Identidad</strong>
        </small>
        <br />
        {{affiliate.identity_card}}
      </v-col>

      <v-col cols="2">
        <small>
          <strong>Categoría</strong>
        </small>
        <br />
        {{this.category_name}}
      </v-col>

      <v-col cols="3">
        <small>
          <strong>Estado</strong>
        </small>
        <br />
        {{this.state_name_type +' ('+ this.state_name_status+')'}}
      </v-col>

      <v-col cols="4">
        <small>
          <strong>Grado</strong>
        </small>
        <br />
        {{this.degree_name}}
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

  beforeMount() {
    this.getCategory_name(this.affiliate.id);
    this.getState_name(this.affiliate.id);
    this.getDegree_name(this.affiliate.id);
  },
  methods: {
    async getCategory_name(id) {
      try {
        this.loading = true;
        let res = await axios.get(`affiliate/${id}/category`);
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
        let res = await axios.get(`affiliate/${id}/degree`);
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