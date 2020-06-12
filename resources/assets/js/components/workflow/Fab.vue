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
    <v-row justify="center">
      <v-dialog
        v-model="sheet" 
        scrollable 
        max-width="300px" 
        inset 
        persistent>
        <v-card>
          <v-card-title>Derivar {{ " ("+selectedLoans.length +") "}} trámites</v-card-title>
          <v-divider></v-divider>
          <v-card-text style="height: 300px;">
            <div >
            <v-select v-if="$store.getters.roles.filter(o => flow.next.includes(o.id)).length > 1"
              v-model="selectedRoleId"
              :items="$store.getters.roles.filter(o => flow.next.includes(o.id))"
              label="Seleccione el área para derivar"
              class="pt-3 my-0"
              item-text="display_name"
              item-value="id"
              dense
            ></v-select>
            <div v-else><h3>Área para derivar: {{String($store.getters.roles.filter(o => flow.next.includes(o.id)).map(o => o.display_name))}}</h3></div>           
            </div>

            <div class="blue--text">Los siguientes trámites serán derivados: </div>     
            <small>{{ selectedLoans.map(o => o.code).join(', ') }}</small>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn color="error" text @click="sheet = false">Cerrar</v-btn>
            <v-btn color="success" text @click="derivationLoans()">Derivar</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'workflow-fab',
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
      },
      selectedRoleId: null,
      idLoans: []
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
    },
    async derivationLoans() {
      let res
      this.idLoans = this.selectedLoans.map(o => o.id)
      try {
        if(this.$store.getters.roles.filter(o => this.flow.next.includes(o.id)).length > 1){
          this.loading = true;
            res = await axios.patch(`loans`, {
              ids: this.idLoans,
              role_id: this.selectedRoleId
            });
            this.sheet = false;
            this.bus.$emit('emitRefreshLoans');
            this.toastr.success("El trámite fue derivado." ) 
        }else{
            this.loading = true;
            res = await axios.patch(`loans`, {
              ids: this.idLoans,
              role_id: parseInt(this.$store.getters.roles.filter(o => this.flow.next.includes(o.id)).map(o => o.id)),
            });
            this.sheet = false;
            this.bus.$emit('emitRefreshLoans');
            this.toastr.success("El trámite fue derivado." ) 
        }
            printJS({
            printable: res.data.attachment.content,
            type: res.data.attachment.type,
            documentTitle: res.data.attachment.file_name,
            base64: true
        })  
     
      } catch (e) {
        console.log(e)
        this.toastr.error("Ocurrió un error en la derivación...")
      }
    }
  }
}
</script>