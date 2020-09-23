<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Element extends Model
{
    use HasFactory;

    private $show_geom = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'year'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getGeomAttribute($value)
    {
        return $this->show_geom ?  $value : '';
    }

    /**
     * Add support for geometry type column in Postgis.
     *
     * @param boolean $excludeDeleted Exclude deleted items
     * @return Builder
     */
    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery()->addSelect(DB::raw('ST_AsGeoJSON(elements.geom) AS geometry'));
    }

    /**
     * Filter Z axis from coordinates
     * @param array $coords Multi-dimensional array of coordinates
     * @return array
     */
    public function filterZaxis($coords, $geometryType)
    {
        foreach($coords as &$shape) {
            foreach($shape as &$point) {
                // If we are dealing with a MultiPolygon, all levels are shifted one because it is Polygons -> coords -> shapes -> points
                if($geometryType == "MultiPolygon"){
                    foreach($point as &$p) {
                        $p = array_filter($p, function($index) {
                            return $index < 2; // Zero based index, third element (Z) removed.
                        }, ARRAY_FILTER_USE_KEY);
                    }
                } else {
                    $point = array_filter($point, function($index) {
                        return $index < 2; // Zero based index, third element (Z) removed.
                    }, ARRAY_FILTER_USE_KEY);
                }
            }
        }

        return $coords;
    }

    /**
     * Convert geo data from angol input field to valid GeoJSON.
     *
     * @param string|object $value Geo data from hidden angol input.
     * @return string
     */
    private function toGeoJSON($value)
    {
        // Decode JSON if needed.
        $geo = is_string($value) ? json_decode($value) : $value;

        if (isset($geo->type) && isset($geo->coordinates)) {
            $geo->crs = [
                "type" => "name",
                "properties" => ["name" => "EPSG:28992"]
            ];

            return json_encode($geo);
        }

        // Filter out Z-Index.
        $geo->latlng = $this->filterZaxis($geo->latlng, $geo->type);

        // Convert to Postgis GeoJSON.
        $geoJson = '{
           "type": "' . $geo->type . '",
           "coordinates": ' . json_encode($geo->latlng) . ',
           "crs": {
               "type": "name",
               "properties": {"name":"EPSG:28992"}
           }
        }';

        return $geoJson;
    }

    /**
     * Set geometry attribute
     *
     * @param string|object $value Geo data from hidden angol input.
     * @return void
     */
    public function setGeomAttribute($value)
    {
        // Convert to GeoJSON
        $geoJson = $this->toGeoJSON($value);

        // Set geometry
        $this->attributes['geom'] = DB::raw("ST_GeomFromGeoJSON('{$geoJson}')");

        // Set color
        if (!empty($geo->color)) {
            $this->attributes['color'] = trim($geo->color);
        }
    }
}
