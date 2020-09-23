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

import {
    register
} from 'ol/proj/proj4';
import Projection from 'ol/proj/Projection';
import proj4 from 'proj4';

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
    props: {
        features: Object
    },

    mounted() {
        console.log(this.features);

        // a new vector layer is created with the feature
        const vectorLayer = new VectorLayer({
            source: new VectorSource({
                features: new GeoJSON().readFeatures(this.features),
            }),
        });

        const extent = [-285401.92, 22598.08, 595401.9199999999, 903401.9199999999];
        const projection = new Projection({
            code: 'EPSG:28992',
            units: 'm',
            extent
        });
        proj4.defs('EPSG:28992', '+proj=sterea +lat_0=52.15616055555555 +lon_0=5.38763888888889 +k=0.9999079 +x_0=155000 +y_0=463000 +ellps=bessel +towgs84=565.417,50.3319,465.552,-0.398957,0.343988,-1.8774,4.0725 +units=m +no_defs');
        proj4.defs('urn:x-ogc:def:crs:EPSG:28992', proj4.defs('EPSG:28992'));
        proj4.defs('http://www.opengis.net/gml/srs/epsg.xml#28992', proj4.defs('EPSG:28992')); // Used by geoserver
        proj4.defs('EPSG:4326', '+proj=longlat +datum=WGS84 +no_defs');
        register(proj4);

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
                projection,
                center: [197800, 454050],
                zoom: 5
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
