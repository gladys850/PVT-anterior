<template>
  <v-container fluid class="pl-0 pt-0">
    <v-row justify="center" class="py-0">
      <v-col cols="12" class="py-0">
        <v-row justify="center" class="py-0">
          <v-col cols="12" class="py-0">
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
                            <td>{{items.item.observation_type_name }}</td>
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
    props: {
    loan: {
      type: Object,
      required: true
    },
    observations: {
      type: Array,
      required: true
    },
    observation_type: {
      type: Array,
      required: true
    },
  },
  data: () => ({
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
  }),
}
</script>