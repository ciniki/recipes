<?php
//
// Description
// -----------
// This function will return the sub menu items for the dropdown menus.
//
// Arguments
// ---------
// ciniki:
// settings:		The web settings structure.
// business_id:		The ID of the business to get events for.
//
// args:			The possible arguments for posts
//
//
// Returns
// -------
//
function ciniki_recipes_web_subMenuItems(&$ciniki, $settings, $business_id, $args) {
	
	if( !isset($ciniki['business']['modules']['ciniki.recipes']) ) {
		return array('stat'=>'404', 'err'=>array('pkg'=>'ciniki', 'code'=>'2574', 'msg'=>"I'm sorry, the file you requested does not exist."));
	}

	//
	// Setup the various tag types that will turn into menus
	//
	$tag_types = array(
		'meals'=>array('name'=>'Meals & Courses', 'tag_type'=>'10', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x01)>0?'yes':'no'),
		'ingredients'=>array('name'=>'Main Ingredients', 'tag_type'=>'20', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x02)>0?'yes':'no'),
		'cuisines'=>array('name'=>'Cuisines', 'tag_type'=>'30', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x04)>0?'yes':'no'),
		'methods'=>array('name'=>'Methods', 'tag_type'=>'40', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x08)>0?'yes':'no'),
		'occasions'=>array('name'=>'Occasions', 'tag_type'=>'50', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x10)>0?'yes':'no'),
		'diets'=>array('name'=>'Diets', 'tag_type'=>'60', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x20)>0?'yes':'no'),
		'seasons'=>array('name'=>'Seasons', 'tag_type'=>'70', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x40)>0?'yes':'no'),
		'collections'=>array('name'=>'Collections', 'tag_type'=>'80', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x80)>0?'yes':'no'),
		'products'=>array('name'=>'Products', 'tag_type'=>'90', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x0100)>0?'yes':'no'),
		'contributors'=>array('name'=>'Contributors', 'tag_type'=>'100', 'visible'=>($ciniki['business']['modules']['ciniki.recipes']['flags']&0x0200)>0?'yes':'no'),
		);

	//
	// The submenu 
	//
	$submenu = array();
	foreach($tag_types as $tag_permalink => $tag) {
		if( $tag['visible'] == 'yes' ) {
			$submenu[$tag_permalink] = array('title'=>$tag['name'], 'permalink'=>$tag_permalink);
		}
	}

	return array('stat'=>'ok', 'submenu'=>$submenu);
}
?>
