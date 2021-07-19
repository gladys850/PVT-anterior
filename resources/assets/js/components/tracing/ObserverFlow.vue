<template>
  <v-container fluid class="pl-0 pt-0">
    <v-row justify="center" class="py-0">
      <v-col cols="12" class="py-0">
        <v-row justify="center" class="py-0">
          <v-col cols="12" class="py-0">
            <p style="color:teal"> <b>OBSERVACIONES DEL TRAMITE</b></p>
                  <v-card flat tile>
                  <v-card-text >
                    <v-col cols="12" class="pl-3">
                      <v-data-table
                        :headers="headersObs"
                        :items="observations"
                        :items-per-page="6"
                        class="elevation-1"
                      >
                         <template v-slot:item="items">
                          <tr>
                            
                            <td>{{items.item.user_name}}</td>
                            <td>{{observation_type.find(o => o.id == items.item.observation_type_id).name }}</td>
                            <td>{{items.item.message}}</td>
                            <td>{{items.item.date|datetime}}</td>
                          </tr>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-card-text>
                </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>

export default {
  name: "observer-flow",
  data: () => ({
    //valor: false,
    observation_type: [],
    bus: new Vue(),
    headersObs: [
      {
        text: "Usuario",
        class: ["normal", "white--text"],
        align: "left",
        value: "user_id"
      },
      {
        text: "Observación",
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
    
    ],
    observations:[],
    record: [],
    //record_payment: []
  }),
  props: {
    
    loan: {
      type: Object,
      required: true
    },
 
  },
beforeMount(){
 this.getObservationType()
   
},
  mounted() {
     this.getObservation(this.loan.id)
  },
  methods: {
  
    async getObservationType() {
      try {
        this.loading = true
        let res = await axios.get(
          `module/${6}/observation_type`
        )
        this.observation_type = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },

    async getObservation(id) {
      try {
        this.loading = true
        let res = await axios.get(`loan/${id}/observation`)
        this.observations = res.data
        for (this.i = 0; this.i < this.observations.length; this.i++) {
          let res1 = await axios.get(`user/${this.observations[this.i].user_id}`
          )
          this.observations[this.i].user_name = res1.data.username
        }
        this.$forceUpdate()
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
  }
}
</script>