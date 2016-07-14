<?php
//
// Description
// -----------
// The module flags
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_recipes_flags($ciniki, $modules) {
    $flags = array(
        // 0x01
        array('flag'=>array('bit'=>'1', 'name'=>'Meals & Courses')),
        array('flag'=>array('bit'=>'2', 'name'=>'Main Ingredients')),
        array('flag'=>array('bit'=>'3', 'name'=>'Cuisine')),
        array('flag'=>array('bit'=>'4', 'name'=>'Methods')),
        // 0x10
        array('flag'=>array('bit'=>'5', 'name'=>'Occasions')),
        array('flag'=>array('bit'=>'6', 'name'=>'Diets')),
        array('flag'=>array('bit'=>'7', 'name'=>'Seasons')),
        array('flag'=>array('bit'=>'8', 'name'=>'Collections')),
        // 0x0100
        array('flag'=>array('bit'=>'9', 'name'=>'Products')),
        array('flag'=>array('bit'=>'10', 'name'=>'Contributors')),
//      array('flag'=>array('bit'=>'11', 'name'=>'Cuisine')),
//      array('flag'=>array('bit'=>'12', 'name'=>'Methods')),
        // 0x1000
//      array('flag'=>array('bit'=>'13', 'name'=>'Occasions')),
//      array('flag'=>array('bit'=>'14', 'name'=>'Diets')),
//      array('flag'=>array('bit'=>'15', 'name'=>'Seasons')),
//      array('flag'=>array('bit'=>'16', 'name'=>'Collections')),
        );

    return array('stat'=>'ok', 'flags'=>$flags);
}
?>
