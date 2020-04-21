<template>
  <v-container fluid >
      <v-row justify="center">
        <v-col cols="12" md="11" class="v-card-profile" >
              <v-row justify="center">
                <v-col cols="12">
                <v-toolbar-title>HISTORIAL DEL TRAMITE</v-toolbar-title>
                <v-data-table
                      :headers="headers"
                      hide-default-footer
                      class="elevation-1"
                    >
                  
                  <template >
                  <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </template>
                  </v-data-table>
              </v-col>
            
                  
              <v-col cols="6">
                <v-toolbar-title>OBSERVACIONES</v-toolbar-title>
              </v-col>
              <v-col cols="6" class="ma-0 pr-10">
              <v-tooltip top>
                <template v-slot:activator="{ on }">
                  <v-btn
                    fab
                    dark
                    x-small
                    :color="'success'"
                    bottom
                    right
                    v-on="on"
                    style="margin-right: 0px; margin-left: 0px; margin-top:10px; "
                    @click.stop=""
                  >
                    <v-icon>mdi-plus</v-icon>
                  </v-btn>
                </template>
                <div>
                  <span>Agregar Observacion</span>
                </div>
              </v-tooltip>
            </v-col>
               <v-col cols="12">
                <v-data-table
                      :headers="headers1"
                      hide-default-footer
                      class="elevation-1"
                    >
                  
                  <template >
                  <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </template>
                  </v-data-table>
              </v-col>
              
                <v-col cols="12">
                <v-toolbar-title>DERIVACIONES {{loanFlow}}</v-toolbar-title>
                
              </v-col>
                   <v-col cols="2">
                <v-toolbar-title>DERIVAR</v-toolbar-title>
                
              </v-col>
            <v-col cols="4" >
              <v-select
                dense
                :items="affiliateState"
                item-text="name"
                item-value="id"
                persistent-hint
              ></v-select>
            </v-col>
                   <v-col cols="2">
                <v-toolbar-title class="text-red">DEVOLVER</v-toolbar-title>
                
              </v-col>
            <v-col cols="4" >
              <v-select
                dense
                :items="affiliateState"
                item-text="name"
                item-value="id"
                persistent-hint
              ></v-select>
            </v-col>         
          </v-row>
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
export default {
  name: "observer-qualification",

  data: () => ({
    affiliateState: [],
    category: [],
    headers: [
        { text: 'Fecha', align: 'left', value: 'city_address_id' },
        { text: 'Hora', align: 'left', value: 'city_address_id' },
        { text: 'Usuario', align: 'left', value: 'zone' },
        { text: 'Accion', align: 'left', value: 'street' }
        ],
        headers1: [
        { text: 'Usuario', align: 'left', value: 'zone' },
        { text: 'Descripcion', align: 'left', value: 'street' }
        ],
    degree: [],
    pension_entity: [],
    dates: {
      dateEntry: {
        formatted: null,
        picker: false
      },
      dateDerelict: {
        formatted: null,
        picker: false
      }
    }
  }),
  
  beforeMount() {
  },
  mounted(){
    this.getFlow(10)
  },
  
  methods: {
  async getFlow(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/flow`)
        this.loanFlow = res.data
        console.log('este es el flow'+this.loanFlow)
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  
  }
  }
</script>