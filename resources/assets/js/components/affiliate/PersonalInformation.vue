<template>
  <v-container fluid >
    <v-row justify="center">
      <v-col cols="12" md="8" >
        <v-container  class="ma-0 pa-0" >
          <v-card >
            <v-row  class="ma-0 pa-0">
              <v-col cols="6" >
                <v-toolbar-title>DOMICILIO</v-toolbar-title>
              </v-col>
              <v-col cols="3"  >
                <v-tooltip top >
                  <template v-slot:activator="{ on }">
                    <v-btn
                      fab
                      dark
                      x-small
                      color="info"
                      bottom
                      right
                      v-on="on"
                      style="margin-right: -9px;"
                      @click.stop="bus.$emit('openDialog', { edit: true })"
                    >
                      <v-icon >mdi-plus</v-icon>
                    </v-btn>
                  </template>
                    <div>
                      <span>Añadir Direccion</span>
                    </div>
                    <div v-if="grabado">
                      <v-text-field disabled>{{updateTelefono()}} </v-text-field>
                    </div>
                  </v-tooltip>
              </v-col>
              <v-col cols="12" class="ma-0 pa-0">
                <v-data-table
                    :headers="headers"
                    :items="addresses"
                    hide-default-footer
                    class="elevation-1"
                    v-if="cities.length > 0"
                >
                  <template v-slot:item="props">
                    <tr>
                      <td>{{ cities.find(o => o.id == props.item.city_address_id).name }}</td>
                      <td>{{ props.item.zone }}</td>
                      <td>{{ props.item.street }}</td>
                      <td>{{ props.item.number_address }}</td>
                      <td>
                        <v-btn text icon color="warning" @click.stop="bus.$emit('openDialog', {...props.item, ...{edit: true}})">
                          <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn text icon color="error" @click.stop="bus.$emit('openRemoveDialog', `address/${props.item.id}`)">
                          <v-icon>mdi-delete</v-icon>
                        </v-btn>
                      </td>
                      <td >
                        <!--<v-btn v-if="props.item.latitude && props.item.longitude" text icon color="info" @click.stop="bus.$emit('openDialog', {...props.item, ...{edit: false}})">
                          <v-icon>mdi-google-maps</v-icon>
                        </v-btn>-->
                      </td>
                    </tr>
                  </template>
                </v-data-table>
              </v-col>
            </v-row>
          </v-card>
        </v-container>
      </v-col>
      <v-col cols="12" md="3">
        <v-container  class="ma-0 pa-0">
          <v-card>
            <v-col cols="12" class="py-2" >
              <v-toolbar-title>TELÉFONOS</v-toolbar-title>
            </v-col>
            <v-col cols="12"  class="py-0" >
              <v-text-field
                dense
                v-model="getTelefono[0]"
                label="Celular 1"
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('celular1')"
                data-vv-name="celular1"
              ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0" >
              <v-text-field class = "text-right"
                dense
                v-model="getTelefono[1]"
                label="Celular 2"
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('celular')"
                data-vv-name="celular"
              ></v-text-field>
            </v-col>
            <v-col cols="12" class="py-0" >
              <v-text-field
                dense
                v-model="affiliate.phone_number"
                label="Fijo"
                v-validate.initial="'min:1|max:20'"
                :error-messages="errors.collect('telefono')"
                data-vv-name="telefono"
              ></v-text-field>
            </v-col>
          </v-card>
        </v-container>
      </v-col>
      <v-col cols="12" md="1" class="ma-0 pa-0">
        <v-tooltip top >
          <template v-slot:activator="{ on }">
            <v-btn
              fab
              dark
              small
              color="success"
              bottom
              right
              v-on="on"
              style="margin-right: -9px;"
              @click.stop="saveAffiliate()"
            >
              <v-icon>mdi-pencil</v-icon>
             </v-btn>
          </template>
          <div>
            <span >Guardar</span>
         </div>
        </v-tooltip>
      </v-col>
    </v-row>
    <AddStreet :bus="bus" :cities="cities"/>
    <RemoveItem :bus="bus"/>
  </v-container>
</template>
<script>
import RemoveItem from '@/components/shared/RemoveItem'
import AddStreet from '@/components/affiliate/AddStreet'
  export default {
  name: "affiliate-personalInformation",
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    addresses: {
      type: Array,
      required: true
    }
  },
  components: {
    AddStreet,
    RemoveItem
  },
  data: () => ({
    loading: true,
    dialog: false,
    telefono:[null,null],
    cities: [],
    headers: [
          { text: 'Ciudad', align: 'left', value: 'city_address_id' },
          { text: 'Zona', align: 'left', value: 'zone' },
          { text: 'Calle', align: 'left', value: 'street' },
          { text: 'Nro', align: 'left', value: 'number_address' },
          { text: 'Acciones', align: 'center' }
        ],
    city: [],
    cityTypeSelected: null,
    bus: new Vue()
  }),
  computed: {
    getTelefono(){
      if(this.affiliate.cell_phone_number==null)
      {
        return 0
      }
      else
      {
        let array=this.affiliate.cell_phone_number.split(',');
      return array
      }
  }
  },
  beforeMount() {
    this.getCities();
    this.updateTelefono();

  },
  mounted() {
      this.bus.$on('saveAddress', (address) => {
        if (address.id) {
          let index = this.addresses.findIndex(o=> o.id == address.id)
          if (index == -1) {
            this.addresses.unshift(address)
          } else {
            this.addresses[index] = address
          }
        }
    })
  },
  methods: {
    close() {
      this.dialog = false
      this.$emit('closeFab')
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
    updateTelefono(){
      this.telefono[0]= this.getTelefono[0];
      this.telefono[1]= this.getTelefono[1];
      let celular=this.telefono.join(',')
      this.affiliate.cell_phone_number=celular
      return  this.affiliate.cell_phone_number
    },
  }
  }
</script>