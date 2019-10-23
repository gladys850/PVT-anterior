// fix icon for marker
import { Icon }  from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-geosearch/assets/css/leaflet.css'

// this part resolve an issue where the markers would not appear
delete Icon.Default.prototype._getIconUrl

Icon.Default.mergeOptions({
  iconRetinaUrl: '/img/marker-icon-2x.png',
  iconUrl: '/img/marker-icon.png',
  shadowUrl: '/img/marker-shadow.png'
})