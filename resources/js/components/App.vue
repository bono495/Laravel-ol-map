<template>
<div>
    <div ref="map-root" id="map-root"></div>
</div>
</template>

<script>
import View from 'ol/View'
import Map from 'ol/Map'
import TileLayer from 'ol/layer/Tile'
import OSM from 'ol/source/OSM'
import GeoJSON from 'ol/format/GeoJSON'
import VectorLayer from 'ol/layer/Vector'
import VectorSource from 'ol/source/Vector'

import 'ol/ol.css'

// this is a simple triangle over the atlantic ocean
const data = {
    type: 'Feature',
    properties: {},
    geometry: {
        type: 'Polygon',
        coordinates: [
            [
                [
                    -27.0703125,
                    43.58039085560784
                ],
                [
                    -28.125,
                    23.563987128451217
                ],
                [
                    -10.8984375,
                    32.84267363195431
                ],
                [
                    -27.0703125,
                    43.58039085560784
                ]
            ]
        ]
    }
};

export default {
    name: 'AppContainer',
    components: {},
    props: {},

    mounted() {
        const feature = new GeoJSON().readFeature(data, {
            // this is required since GeoJSON uses latitude/longitude,
            // but the map is rendered using “Web Mercator”
            featureProjection: 'EPSG:3857'
        });

        // a new vector layer is created with the feature
        const vectorLayer = new VectorLayer({
            source: new VectorSource({
                features: [feature],
            }),
        })

        new Map({
            target: this.$refs['map-root'],
            layers: [
                new TileLayer({
                    source: new OSM()
                }),
                vectorLayer
            ],

            view: new View({
                zoom: 0,
                center: [0, 0],
                constrainResolution: true
            }),
        })
    },
}
</script>

<style>
#map-root {
    height: 100vh;
}
</style>
