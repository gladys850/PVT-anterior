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
                          style="margin-right: 0px; margin-left: 550px; margin-top:-11px; "
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
                            <td>{{items.item.user_name}}</td>
                            <td>{{ observation_type.find(o => o.id == items.item.observation_type_id).name }}</td>
                            <td>{{items.item.message}}</td>
                            <td>{{items.item.date|datetime}}</td>
                            <td>
                              <v-tooltip top>
                                <template v-slot:activator="{ on }">
                                  <v-btn
                                  v-if="!(items.item.user_id==$store.getters.id) && !items.item.enabled"
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
                                  v-if="(items.item.user_id==$store.getters.id) && !items.item.enabled"
                                    icon
                                    small
                                    color="error"
                                    v-on="on"
                                    @click.stop="deleteObservation( 
                                      items.item.user_id, 
                                      items.item.observation_type_id, 
                                      items.item.message, 
                                      items.item.date, 
                                      items.item.enabled)"  
                                  >
                                    <v-icon>mdi-delete</v-icon>
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
              <v-tab>HISTORIAL DEL TRÁMITE</v-tab>
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
                            <!--<td>{{items.item.user_id}}</td>
                            <td>{{items.item.record_type_id}}</td>
                            <td>{{items.item.recordable_id}}</td>-->
                            <td>{{items.item.date|datetime}}</td>
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
              @click.stop="saveLoanValidated(loan.id)"
            >Aprobar el Trámite</v-btn>
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
              @click.stop="saveLoanRol(loan.id)"
            >Devolver Trámite</v-btn>
          </v-col>
          <v-spacer></v-spacer>
          <v-col cols="10"></v-col>
        </v-row>
      </v-col>
    </v-row>
    <AddObservation :bus="bus" :loan.sync="loan" />
    <RemoveItem :bus="bus"/>
  </v-container>
</template>

  <script>

import AddObservation from '@/components/workflow/AddObservation'
import RemoveItem from '@/components/shared/RemoveItem'

export default {
  name: "observer-flow",

  components: {
    AddObservation,
    RemoveItem

  },

  data: () => ({
    //valDevolver: null,
    valor:false,
    observation_type:[],
    user: null ,
    bus: new Vue(),
    headers: [
      /*{
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
        text: "Trámite",
        class: ["normal", "white--text"],
        align: "left",
        value: "recordable_id"
      },*/
      {
        text: "Fecha",
        class: ["normal", "white--text"],
        align: "left",
         value: "date"
      }, 
      {
        text: "Acciones realizadas",
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
    bus1: {
      type: Object,
      required: true
    },
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
    this.getFlow(this.loan.id);
    this.bus.$on("saveObservation", observation => {
      console.log("entro al bus" + observation);
      this.observations.unshift(observation);
    });
    this.bus.$on("emitSaveObservation", val => {//al realizar el guardado de la observacion en saveObservation
      console.log('entraaaa 1')
      this.bus1.$emit('emitGetObservation',this.loan.id); //emite a Add.vue para obtener los registros de las observaciones del id de prestamo 
    });
  },
  methods: {
      async saveLoanRol(id) {
      try {
        if( JSON.stringify(this.observations) != '{}'){
          let res = await axios.patch(`loan/${id}`, {
            role_id:this.valArea
          });
          this.toastr.success("Se devolvio satisfacctoriamente el tramite.")      
          this.$router.push('/workflow')
          console.log('se cambio el rol')
        }else{
          this.toastr.error("Debe crear una observacion para devolver el trámite")   
        }

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async saveLoanValidated(id) {
      try {
        let res = await axios.patch(`loan/${id}`, {
             validated:true, 
        });
        console.log('se cambio el rol')
        this.toastr.success("Se reviso satisfacctoriamente el tramite.")      
        this.$router.push('/workflow')
        
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
      async getUser(id) {
      try {
        this.loading = true;
        let res = await axios.get(`user/${id}`);
        this.user = res.data.username;
        console.log(this.user);
      } catch (e) {
        console.log(e);
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
    async deleteObservation(user_id1, observation_type_id1, message1, date1, enabled1) {
      try {       
        let res = await axios.delete(`loan/${this.loan.id}/observation`, {data:{
            user_id: user_id1, 
            observation_type_id: observation_type_id1,
            message:message1,
            date: date1,
            enabled: enabled1 
          }
        });
        console.log("DATOS"+ user_id1, observation_type_id1, message1, date1, enabled1 +" loan " + this.loan.id)     
        this.toastr.success("La observación fue eliminada.")
        this.bus1.$emit('emitGetObservation',this.loan.id);

      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  },
  }
</script>