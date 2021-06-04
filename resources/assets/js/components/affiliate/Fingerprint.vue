<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12" class="text-center">
        <v-toolbar-title>INFORMACIÓN BIOMÉTRICA</v-toolbar-title>
      </v-col>
      <v-col cols="6" class="text-center">
        <v-row justify="center">
          <v-col cols="12">
            <v-card-text @click="dialog=true" class="text-center">
              <v-btn
              color="primary"
              @click="dialog=true" v-if="editable"
              :disabled="pictureSucess"
            >
              <v-icon left>mdi-camera</v-icon>
              <span v-if="pictureSucess">Fotografia Capturada</span>
              <span v-else>Adicionar Fotografia</span>
            </v-btn>
              <!--<v-btn type="file" color="primary">
                  Adicionar Foto
              </v-btn>-->

            </v-card-text>
            <v-row v-if="profilePictures.length > 0">
              <template v-for="image in profilePictures">
                <v-col cols="12" :key="image.name">
                  <v-avatar
                    slot="offset"
                    class="mx-auto d-block elevation-10"
                    size="250"
                  >
                    <v-img :src="`data:${image.format};base64,${image.content}`"/>
                  </v-avatar>
                </v-col>
              </template>
            </v-row>
            <v-col cols="12" v-else>
              Sin fotografía de perfil
            </v-col>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="6" class="text-center">
        <v-row justify="center">
          <v-col cols="12">
            <v-btn
              color="primary"
              @click.stop="fingerprintCaptureStart()" v-if="editable && permission.secondary"
              :disabled="fingerprintSucess"
            >
              <v-icon left>mdi-fingerprint</v-icon>
              <span v-if="fingerprintSucess">Huella capturada</span>
              <span v-else>Capturar huella</span>
            </v-btn>
          </v-col>
          <v-row v-if="fingerprints.length > 0">
            <template v-for="image in fingerprints">
              <v-col cols="4" :key="image.name">
                <v-img
                  :src="`data:${image.format};base64,${image.content}`"
                  contain
                  aspect-ratio="1"
                ></v-img>
              </v-col>
            </template>
          </v-row>
          <v-row v-else>
            <v-col cols="12">
              Sin registro de huellas
            </v-col>
          </v-row>
        </v-row>
      </v-col>
    </v-row>
    <v-dialog
      v-model="fingerprintCapture"
      persistent
      width="400"
    >
      <v-card
        color="primary"
        class="py-3"
        dark
      >
        <v-card-text>
          <div class="subtitle-1 font-weight-light">Continue el proceso en el equipo biométrico ...</div>
          <v-progress-linear
            indeterminate
            color="white"
            class="mt-4"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="fingerprintSaved"
      width="500"
    >
      <v-card>
        <v-alert :type="fingerprintSucess ? 'success' : 'error'" class="ma-0">
          <div v-if="fingerprintSucess">
            Las huellas se registraron correctamente
          </div>
          <div v-else>
            Error al capturar las huellas, vuelva a realizar el proceso
          </div>
        </v-alert>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            @click.stop="fingerprintSaved = false"
          >
            Cerrar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialog" persistent max-width="1000">
      <v-card>
        <v-toolbar dense flat color="tertiary">
          <v-toolbar-title>REGISTRO DE LA FOTOGRAFIA</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog=false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>
        <camara></camara>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import Cam from '@/components/affiliate/Webcam'
export default {
  name: 'affiliate-fingerprint',
  props: {
    affiliate: {
      type: Object,
      required: true
    },
    editable: {
      type: Boolean,
      required: true
    },
    permission: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    dialog:false,
    cam: null,
    loading: false,
    fingerprintCapture: false,
    pictureSucess: false,
    fingerprintSaved: false,
    fingerprintSucess: null,
    fingerprints: [],
    profilePictures: []
  }),
  components: {
    'camara': Cam
    },
  destroyed() {
    Echo.leave('fingerprint')
  },
  beforeMount() {
    Echo.channel('fingerprint').listen('.saved', (msg) => {
      if (msg.data.affiliate_id == this.affiliate.id && msg.data.user_id == this.$store.getters.id) {
        this.fingerprintCapture = false
        this.fingerprintSaved = true
        this.fingerprintSucess = JSON.parse(msg.data.success)
        if (this.fingerprintSucess) {
          this.getFingerprints()
        }
      }
    })
  },
  mounted() {
    if (this.affiliate.fingerprint_saved) this.getFingerprints()
    if (this.affiliate.picture_saved) this.getProfilePictures()
  },
  methods: {
    async getProfilePictures() {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${this.affiliate.id}/profile_picture`)
        this.profilePictures = res.data
      } catch (e) {
        console.log(e)
      } finally {
        this.loading = false
      }
    },
    async getFingerprints() {
      try {
        this.loading = true
        let res = await axios.get(`affiliate/${this.affiliate.id}/fingerprint_picture`)
        this.fingerprints = res.data
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
        this.toastr.error('Error al comunicarse con el dispositivo de captura de huellas')
        this.fingerprintCapture = false
      }
    }
  }
}
</script>
