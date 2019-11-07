<template>
  <div>
    <l-map
      v-if="!loading"
      style="height: 480px; width: 800px;"
      :zoom="zoom"
      :center="center"
      @update:zoom="zoomUpdated(18)"
      @update:center="centerUpdated"
      @dblclick="addMarker"
    >
      <l-tile-layer :url="url"></l-tile-layer>
      <l-geosearch
        :options="geosearchOptions"
        v-show="address.edit"
      ></l-geosearch>
      <l-control>
        <v-btn
          small
          @click.stop="centerUpdated(marker)"
          v-show="markerExists"
        >
          Centrar
        </v-btn>
      </l-control>
      <l-marker
        ref="marker"
        :lat-lng="marker"
        v-if="markerExists"
      >
        <l-popup v-if="edit">
          <v-btn
            text
            small
            color="error"
            @click.stop="removeMarker()"
          >Eliminar</v-btn>
        </l-popup>
      </l-marker>
    </l-map>
    <Loading v-else/>
  </div>
</template>

<script>
import { OpenStreetMapProvider } from 'leaflet-geosearch'
import LGeosearch from 'vue2-leaflet-geosearch'
import { LMap, LTileLayer, LMarker, LPopup, LControl } from 'vue2-leaflet'
import Loading from '@/components/shared/Loading'

export default {
  name: 'lmap',
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LControl,
    LGeosearch,
    Loading
  },
  props: {
    address: {
      type: Object,
      required: true
    },
    cities: {
      type: Array,
      required: true
    },
    edit: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({
    loading: true,
    url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
    zoom: 18,
    center: [],
    marker: [],
    bounds: null,
    geosearchOptions: {
      provider: new OpenStreetMapProvider(),
      style: 'bar',
      showMarker: false,
      animateZoom: true,
      autoClose: true,
      searchLabel: 'Buscar dirección',
      keepResult: true,
      retainZoomLevel: true
    },
  }),
  mounted() {
    this.setMarker()
    this.changeCity()
  },
  computed: {
    markerExists() {
      if (this.marker.length == 2) {
        return true
      } else {
        return false
      }
    }
  },
  watch: {
    'address.city_address_id': function(oldVal, newVal) {
      if (newVal != oldVal) {
        this.changeCity()
      }
    }
  },
  methods: {
    setMarker() {
      if (!this.address.edit) {
        this.center = [parseFloat(this.address.latitude), parseFloat(this.address.longitude)]
        this.marker = this.center
      }
      this.loading = false
    },
    changeCity() {
      this.loading = true
      if (this.address.city_address_id) {
        const city = this.cities.find(o => o.id == this.address.city_address_id)
        this.center = [city.latitude, city.longitude]
        if (city.latitude && city.longitude && this.address.edit) {
          this.zoom = 14
          this.loading = false
        }
        this.setMarker()
      }
    },
    zoomUpdated(zoom) {
      this.zoom = zoom
    },
    centerUpdated(center) {
      this.center = center
    },
    removeMarker() {
      this.marker = []
    },
    addMarker(event) {
      if (event.containerPoint.y >= 60) {
        this.marker = Object.values(event.latlng)
        this.address.latitude = event.latlng.lat
        this.address.longitude = event.latlng.lng
      }
    }
  }
}
</script>