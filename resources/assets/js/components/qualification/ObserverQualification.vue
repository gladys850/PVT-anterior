<template>
  <v-container class="mb-0">
    <v-row justify="center" class="mb-0">
      <v-col cols="12" class="mb-0" >
        <v-row justify="center" class="mb-0">
          <v-col cols="12" class="mb-0">
            <v-tabs
              background-color="primary"
              center-active
              dark
            >
              <v-tab>Observaciones</v-tab>
              <v-tab-item>
                <v-card flat tile>
                  <v-card-text class="py-0">
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
                          style="margin-right: 0px; margin-left: 950px; margin-top:-11px; "
                          @click.stop="bus.$emit('openDialog', { edit:false })"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </template>
                      <div>
                        <span>Agregar Observacion</span>
                      </div>
                    </v-tooltip>
                  </v-card-text>
                </v-card>
                <v-card flat tile>
                  <v-card-text class="py-0">
                    <v-col cols="12" class="py-0 mb-0">
                      <v-data-table
                        :headers="headers1"
                        :items="observations"
                        :items-per-page="4"
                        class="elevation-1"
                      >
                        <template v-slot:item="items">
                          <tr>
                            <td>{{items.item.user_id}}</td>
                            <td>{{ observation_type.find(o => o.id == items.item.observation_type_id).name }}</td>
                            <td>{{items.item.message}}</td>
                            <td>{{items.item.date}}</td>
                            <td>
                              <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                  <v-btn
                                  v-if="!items.item.user_id==123" v-show="!items.item.enabled"
                                    icon
                                    small
                                    color="info"
                                    @click.stop="bus.$emit('openDialog', {...items.item, ...{edit: true}})"
                                    v-on="on"
                                  >
                                    <v-icon >mdi-pencil</v-icon>
                                  </v-btn>
                                </template>
                                <span class="caption">Editar</span>
                              </v-tooltip>
                              <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                  <v-btn
                                  v-if="items.item.user_id==123" v-show="!items.item.enabled"
                                    icon
                                    small
                                    color="error"
                                    @click.stop="bus.$emit('openRemoveDialog', {...`loan/${1}/observation`,...{...items.item,}})"
                                    v-on="on"
                                  >
                                    <v-icon   >mdi-delete</v-icon>
                                  </v-btn>
                                </template>
                                <span class="caption">Eliminar</span>
                              </v-tooltip>
                            </td>
                          </tr>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab>HISTORIAL DEL TRAMITE</v-tab>
              <v-tab-item>
                <v-card flat tile>
                  <v-card-text>
                    <v-col cols="12" class="mb-0">
                      <v-data-table
                        :headers="headers"
                        :items="record"
                        :items-per-page="4"
                        class="elevation-1"
                      >
                        <template v-slot:item="items">
                          <tr>
                            <td>{{items.item.user_id}}</td>
                            <td>{{items.item.record_type_id}}</td>
                            <td>{{items.item.recordable_id}}</td>
                            <td>{{items.item.action}}</td>
                          </tr>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs>
          </v-col>
          <v-col cols="1" class="ma-0 pb-0"></v-col>
          <v-col cols="2" v-show="!valor">
            <v-btn
              small
              color="success"
              class="text-rigth"
              @click.stop="saveLoanValidated()"
            >Aprobar el Tramite</v-btn>
          </v-col>
           <v-col cols="3" class="ma-0 pb-0">
            <v-checkbox
              class="ma-0 pb-0"
              label="Devolver el Tramite"
              v-model="valor"
           ></v-checkbox>
          </v-col>
          <v-col cols="3" v-show="valor">
            <v-select
              label="Escoger Area"
              v-model="valArea"
              :items="listAreas"
              item-text="display_name"
              item-value="id"
            ></v-select>
          </v-col>
          <v-col cols="2" v-show="valor">
            <v-btn
              small
              color="danger"
              @click.stop="saveLoanRol()"
            >Devolver Tramite</v-btn>
          </v-col>
          <v-spacer></v-spacer>
          <v-col cols="10"></v-col>
        </v-row>
      </v-col>
    </v-row>
    <AddObservation :bus="bus" />
    <RemoveItem :bus="bus"/>
  </v-container>
</template>

  <script>

import AddObservation from '@/components/qualification/AddObservation'
import RemoveItem from '@/components/shared/RemoveItem'

export default {
  name: "observer-qualification",

  components: {
    AddObservation,
    RemoveItem

  },

  data: () => ({
    //valDevolver: null,
    valor:false,
    observation_type:[],
    bus: new Vue(),
    headers: [
      {
        text: "Usuario",
        class: ["normal", "white--text"],
        align: "left",
        value: "user_id"
      },
      {
        text: "Record",
        class: ["normal", "white--text"],
        align: "left",
        value: "record_type_id"
      },
      {
        text: "Nombre",
        class: ["normal", "white--text"],
        align: "left",
        value: "recordable_id"
      },
      {
        text: "Accion",
        class: ["normal", "white--text"],
        align: "left",
        value: "accion"
      }
    ],
    headers1: [
      {
        text: "Usuario",
        class: ["normal", "white--text"],
        align: "left",
        value: "user_id"
      },
      {
        text: "Observacion",
        class: ["normal", "white--text"],
        align: "left",
        value: "observation_type_id"
      },
      {
        text: "Mensaje",
        class: ["normal", "white--text"],
        align: "left",
        value: "message"
      },
      {
        text: "Fecha",
        class: ["normal", "white--text"],
        align: "left",
        value: "date"
      },
      {
        text: "Acciones",
        class: ["normal", "white--text"],
        align: "center",
        width: "11%",
        sortable: false
      }
    ],
    listRoles: [],
    flow:[],
    listAreas:[],
    valArea:[]
  }),
  props: {
    observations: {
      type: Array,
      required: true
    },
    record: {
      type: Array,
      required: true
    },
    loan: {
      type: Object,
      required: true
    }
  },
  mounted() {
    this.getUser();
    this.getObservationType();
    this.getModuleRole(6);
    this.getFlow(1);
    this.bus.$on("saveObservation", observation => {
      console.log("entro al bus" + observation);

      this.observations.unshift(observation);
    });
  },
  methods: {
      async saveLoanRol() {
      try {
        let res = await axios.patch(`loan/${1}`, {
          role_id:this.valArea
        });
        this.toastr.success("Se devolvio satisfacctoriamente el tramite.")      
        this.$router.push('/qualification')
        console.log('se cambio el rol')
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async saveLoanValidated() {
      try {
        let res = await axios.patch(`loan/${1}`, {
             validated:true, 
        });
        console.log('se cambio el rol')
        this.toastr.success("Se reviso satisfacctoriamente el tramite.")      
        this.$router.push('/qualification')
        
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getObservationType() {
      try {
        this.loading = true;
        let res = await axios.get(`module/${6}/observation_type`);
        this.observation_type = res.data;
        console.log("estas son las observaciones" + this.observation_type);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getUser() {
      try {
        this.loading = true;
        let res = await axios.get(`user`);
        this.user = res.data;
        console.log("estas son las observaciones" + this.observation_type);
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getModuleRole(id) {
      try {
        this.loading = true
        let res = await axios.get(`module/${id}/role`);
        this.listRoles = res.data
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
    async getFlow(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/flow`)
        this.flow = res.data.previous
        this.filterRoleFlow()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    filterRoleFlow(){
      let areas=[]
      for(let i = 0; i < this.flow.length; i++){
        areas = this.listRoles.find((item) => item.id == this.flow[i])
        this.listAreas.push(areas)
      }
    },
  },
  }
</script>