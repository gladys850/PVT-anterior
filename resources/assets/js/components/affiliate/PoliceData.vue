<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="11" class="v-card-profile" >
              <v-row justify="center">
              <v-col cols="12">
                <v-toolbar-title>INFORMACION POLICIAL</v-toolbar-title>
              </v-col>
            <v-col cols="12" md="7" >
              <v-select
                :loading="loading"
                :items="affiliateState"
                data-vv-name="Estado"
                item-text="name"
                item-value="id"
                label="Estado"
                v-model="affiliate.affiliate_state_id"
                hint="Activo"
                persistent-hint
              ></v-select>
            </v-col>
            <v-col cols="12" md="5" >
              <v-menu
                v-model="menu3"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="affiliate.date_entry"
                  label="Fecha Ingreso a la Institucion Policial"
                  append-icon="mdi-calendar"
                  readonly
                  v-on="on"
                ></v-text-field>
                </template>
                <v-date-picker v-model="affiliate.date_entry" @input="menu3 = false"></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :loading="loading"
                data-vv-name="Categoria"
                :items="category"
                item-text="name"
                item-value="id"
                label="Categoria"
                name="categoria"
                v-model="affiliate.category_id"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="affiliate.service_years"
                label="Años de Servicio"
                class="purple-input"
                v-validate.initial="'numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-text-field
                v-model="affiliate.service_months"
                label="Meses de Servicio"
                class="purple-input"
                v-validate.initial="'numeric|min:1|max:100'"
                :error-messages="errors.collect('nro')"
                data-vv-name="nro"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" >
              <v-select
                :loading="loading"
                data-vv-name="Grado"
                :items="degree"
                item-text="name"
                item-value="id"
                label="Grado"
                name="Grado"
                v-model="affiliate.degree_id"
                ></v-select>
              </v-col>
            <v-col cols="12" >
              <v-select
                :loading="loading"
                data-vv-name="Gestor"
                :items="pension_entity"
                item-text="name"
                item-value="id"
                label="Ente Gestor"
                name="Grado"
                v-model="affiliate.pension_entity_id"
            ></v-select>
            </v-col>
            <v-col cols="12">
              <v-menu
                v-model="menu4"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
              <template v-slot:activator="{ on }">
                <v-text-field
                v-model="affiliate.date_derelict"
                label="Fecha Desvinculacion"
                append-icon="mdi-calendar"
                readonly
                v-on="on"
                ></v-text-field>
                </template>
                <v-date-picker v-model="affiliate.date_derelict" @input="menu4 = false"></v-date-picker>
              </v-menu>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
import List from '@/components/affiliate/List'
  export default {
  data: () => ({
    fingerprintCapture: false,
    fingerprintSaved: false,
    fingerprintSucess: null,
    affiliate: {
    first_name: null,
    second_name:null,
    last_name: null,
    mothers_last_name:null,
    identity_card:null,
    birth_date:null,
    date_death:null,
    reason_death:null,
    date_entry:null,
    phone_number:null,
    cell_phone_number:null,
    service_years:null,
    service_months:null,
    city_identity_card_id:null,
    date_derelict:null
    },
    loading: true,
    cities: [],
    Genero: [
      'Femenino',
      'Masculino'
    ],
    gender: [
      { name:"Femenino",
        value:"F"
      },
      { name:"Masculino",
        value:"M"
      }
    ],
    affiliateState: [],
    category: [],
    degree: [],
    city: [],
    pension_entity: [],
    cityTypeSelected: null,
      date: null,
        menu: false,
        menu1: false,
        modal: false,
        menu2: false,
        menu3: false,
        menu4: false,
        titulo:null
      }),
  components: {
  List,
  },
  watch: {
    search: _.debounce(function () {
      this.bus.$emit('search', this.search)
    }, 1000),
  },
  destroyed() {
    Echo.leave('fingerprint')
  },
  beforeMount() {
    this.getCategory();
    this.getCities();
    this.getDegree();
    this.getPensionEntity();
    this.getAffiliateState();
    if (this.$route.params.id != 'new') {
      Echo.channel('fingerprint').listen('.saved', (msg) => {
        if (msg.data.affiliate_id == this.affiliate.id && msg.data.user_id == this.$store.getters.id) {
          this.fingerprintCapture = false
          this.fingerprintSaved = true
          this.fingerprintSucess = JSON.parse(msg.data.success)
        }
      })
    }
  },
  mounted() {
    (this.$route.params.id=='new')?this.titulo='Nuevo Afiliado':this.titulo='Editar Afiliado'
    if (this.$route.params.id != 'new') {
      this.getAffiliate(this.$route.params.id)
    }
  },
  methods: {
    async saveAffiliate() {
    try {
      if (await this.$validator.validateAll()) {
        this.loading = true
        if (this.$route.params.id != 'new') {
          let res = await axios.patch(`affiliate/${this.affiliate.id}`, this.affiliate)
          console.log(res.data)
          this.$router.push({
          name: "affiliateIndex"
          });
          this.toast('Afiliado modificado', 'success')
        } else {
          let res = await axios.post(`affiliate`, this.affiliate)
          this.toast('Afiliado adicionado', 'success')
          this.$router.push({
          name: "affiliateIndex"
          });console.log(res.data)
        }
      }
    } catch (e) {
      console.log(e)
    } finally {
      this.loading = false
    }
    },
    async fingerprintCaptureStart() {
      try {
        this.fingerprintCapture = true
        await axios.patch(`affiliate/${this.affiliate.id}/fingerprint`)
      } catch (e) {
        console.log(e)
        this.toast('Error al comunicarse con el dispositivo de captura de huellas', 'error')
        this.fingerprintCapture = false
      }
    },
    async getCategory() {
    try {
      this.loading = true
      let res = await axios.get(`category`);
      this.category = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getAffiliateState() {
    try {
      this.loading = true
      let res = await axios.get(`affiliateState`);
      this.affiliateState = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
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
    async getDegree() {
    try {
      this.loading = true
      let res = await axios.get(`degree`);
      this.degree = res.data;
    } catch (e) {
      this.dialog = false;
      console.log(e);
    }finally {
        this.loading = false
      }
  },
    async getPensionEntity() {
      try {
      this.loading = true
      let res = await axios.get(`pensionEntity`);
      this.pension_entity = res.data;
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