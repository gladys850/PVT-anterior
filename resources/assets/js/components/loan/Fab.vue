<template>
  <div>
    <v-tooltip top>
      <template v-slot:activator="{ on }">
        <v-btn
          v-on="on"
          color="success"
          dark
          small
          absolute
          bottom
          right
          fab
          @click="sheet = true"
        >
          <v-icon>mdi-send</v-icon>
        </v-btn>
      </template>
      <span>Derivar</span>
    </v-tooltip>
    <v-bottom-sheet
      v-model="sheet"
      inset
      persistent
      scrollable
    >
      <v-sheet
        class="text-center"
        height="350px"
      >
        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn
              v-on="on"
              class="mt-3"
              color="red"
              @click="sheet = false"
              center
              top
              small
              fab
              dark
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
          <span>Cancelar</span>
        </v-tooltip>
        <div class="py-3">Aquí se puede derivar</div>
        <div>
          Loans: {{ selectedLoans.map(o => o.code).join(', ') }}
        </div>
        <div>
          ----Flujo----
        </div>
        <div>
          Para observar: {{ $store.getters.roles.filter(o => flow.previous.includes(o.id)).map(o => o.display_name).join(', ') }}
        </div>
        <div>
          Para derivar: {{ $store.getters.roles.filter(o => flow.next.includes(o.id)).map(o => o.display_name).join(', ') }}
        </div>
      </v-sheet>
    </v-bottom-sheet>
  </div>
</template>

<script>
export default {
  name: 'loan-fab',
  props: {
    bus: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      sheet: false,
      selectedLoans: [],
      flow: {
        previous: [],
        next: []
      }
    }
  },
  watch: {
    selectedLoans(val) {
      if (val.length) {
        this.getFlow()
      }
    }
  },
  mounted() {
    this.bus.$on('selectLoans', (data) => {
      this.selectedLoans = data
    })
  },
  methods: {
    async getFlow() {
      try {
        let res = await axios.get(`loan/${this.selectedLoans[0].id}/flow`)
        this.flow = res.data
      } catch (e) {
        console.log(e)
      }
    }
  }
}
</script>