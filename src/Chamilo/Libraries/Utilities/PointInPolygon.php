<?php
namespace Chamilo\Libraries\Utilities;

/**
 *
 * @package libraries
 * @subpackage polygon This class allows the user to check whether or not a point is located inside or outside the
 *             polygon. Additionally it checks whether or not a point is on a vertex and/or a boundary.
 * @author ASys dataServices (original script)
 * @link http://www.assemblysys.com/dataServices/php_pointinpolygon.php
 * @author Hans De Bisschop (extension and adaptation for Chamilo connect)
 */
class PointInPolygon
{
    /**
     * The different types of polygon points
     */
    const POINT_VERTEX = 1;
    const POINT_BOUNDARY = 2;
    const POINT_INSIDE = 3;
    const POINT_OUTSIDE = 4;
    
    /**
     * The Array-indexes to use for X- and Y-coordinates
     */
    const POLYGON_X_INDEX = 0;
    const POLYGON_Y_INDEX = 1;

    /**
     * The vertices that form the polygon.
     * 
     * @var Array
     */
    private $vertices;

    /**
     * Check whether the point is a vertex or not
     * 
     * @var boolean
     */
    private $vertex_check = true;

    /**
     * PointInPolygon constructor
     * 
     * @param Array $vertices The vertices that form the polygon.
     */
    function __construct($vertices)
    {
        $this->vertices = $vertices;
    }

    /**
     * Check if the point is inside the polygon
     * 
     * @param Array $point The point to check.
     * @param boolean $vertex_check Check whether the point is a vertex or not
     * @return int The point type.
     */
    function is_inside($point, $vertex_check = true)
    {
        $this->vertex_check = $vertex_check;
        $vertices = $this->vertices;
        
        // Check if the point sits exactly on a vertex
        if ($this->vertex_check === true && $this->point_is_on_vertex($point, $vertices) === true)
        {
            return self :: POINT_VERTEX;
        }
        
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);
        
        for ($i = 1; $i < $vertices_count; $i ++)
        {
            $vertex1 = $vertices[$i - 1];
            $vertex2 = $vertices[$i];
            
            // Check if point is on an horizontal polygon boundary
            if ($vertex1[self :: POLYGON_Y_INDEX] == $vertex2[self :: POLYGON_Y_INDEX] &&
                 $vertex1[self :: POLYGON_Y_INDEX] == $point[self :: POLYGON_Y_INDEX] &&
                 $point[self :: POLYGON_X_INDEX] >
                 min($vertex1[self :: POLYGON_X_INDEX], $vertex2[self :: POLYGON_X_INDEX]) && $point[self :: POLYGON_X_INDEX] <
                 max($vertex1[self :: POLYGON_X_INDEX], $vertex2[self :: POLYGON_X_INDEX]))
            {
                return self :: POINT_BOUNDARY;
            }
            if ($point[self :: POLYGON_Y_INDEX] >
                 min($vertex1[self :: POLYGON_Y_INDEX], $vertex2[self :: POLYGON_Y_INDEX]) &&
                 $point[self :: POLYGON_Y_INDEX] <=
                 max($vertex1[self :: POLYGON_Y_INDEX], $vertex2[self :: POLYGON_Y_INDEX]) && $point[self :: POLYGON_X_INDEX] <=
                 max($vertex1[self :: POLYGON_X_INDEX], $vertex2[self :: POLYGON_X_INDEX]) &&
                 $vertex1[self :: POLYGON_Y_INDEX] != $vertex2[self :: POLYGON_Y_INDEX])
            {
                $xinters = ($point[self :: POLYGON_Y_INDEX] - $vertex1[self :: POLYGON_Y_INDEX]) *
                 ($vertex2[self :: POLYGON_X_INDEX] - $vertex1[self :: POLYGON_X_INDEX]) /
                 ($vertex2[self :: POLYGON_Y_INDEX] - $vertex1[self :: POLYGON_Y_INDEX]) +
                 $vertex1[self :: POLYGON_X_INDEX];
            
            // Check if point is on the polygon boundary (other than horizontal)
            if ($xinters == $point[self :: POLYGON_X_INDEX])
            {
                return self :: POINT_BOUNDARY;
            }
            
            if ($vertex1[self :: POLYGON_X_INDEX] == $vertex2[self :: POLYGON_X_INDEX] ||
                 $point[self :: POLYGON_X_INDEX] <= $xinters)
            {
                $intersections ++;
            }
        }
    }
    // If the number of edges we passed through is even, then it's in the polygon.
    if ($intersections % 2 != 0)
    {
        return self :: POINT_INSIDE;
    }
    else
    {
        return self :: POINT_OUTSIDE;
    }
}

/**
 * Check if the point sits exactly on one of the vertices
 * 
 * @param Array $point The point to check.
 * @return boolean True if the point is a vertex, false otherwise.
 */
function point_is_on_vertex($point)
{
    $vertices = $this->vertices;
    
    foreach ($vertices as $vertex)
    {
        if ($point === $vertex)
        {
            return true;
        }
    }
    
    return false;
}
}