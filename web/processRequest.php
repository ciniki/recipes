<?php
//
// Description
// -----------
// This function will process a web request for the module.
//
// Arguments
// ---------
// ciniki:
// settings:        The web settings structure.
// business_id:     The ID of the business to get events for.
//
// args:            The possible arguments for posts
//
//
// Returns
// -------
//
function ciniki_recipes_web_processRequest(&$ciniki, $settings, $business_id, $args) {
    
    if( !isset($ciniki['business']['modules']['ciniki.recipes']) ) {
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.recipes.20', 'msg'=>"I'm sorry, the page you requested does not exist."));
    }
    $page = array(
        'title'=>$args['page_title'],
        'breadcrumbs'=>$args['breadcrumbs'],
        'blocks'=>array(),
        );

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
    // Check if a file was specified to be downloaded
    //
    $download_err = '';
    if( isset($args['uri_split'][0]) && $args['uri_split'][0] == 'download'
        && isset($args['uri_split'][1]) && $args['uri_split'][1] != ''
        && isset($args['uri_split'][2]) && $args['uri_split'][2] != '' 
        && preg_match("/^(.*)\.pdf$/", $args['uri_split'][2], $matches)
        ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'web', 'downloadPDF');
        $rc = ciniki_recipes_web_downloadPDF($ciniki, $settings, $business_id, $matches[1], array('layout'=>$args['uri_split'][1]));
        if( $rc['stat'] == 'ok' ) {
            return array('stat'=>'ok', 'download'=>$rc['file']);
        }
        
        //
        // If there was an error locating the files, display generic error
        //
        return array('stat'=>'404', 'err'=>array('code'=>'ciniki.recipes.21', 'msg'=>'The file you requested does not exist.'));
    }

    //
    // Setup titles
    //
    if( $page['title'] == '' ) {
        $page['title'] = 'Recipes';
    }
    if( count($page['breadcrumbs']) == 0 ) {
        $page['breadcrumbs'][] = array('name'=>'Recipes', 'url'=>$args['base_url']);
    }

    $display = '';
    $ciniki['response']['head']['og']['url'] = $args['domain_base_url'];

    //
    // Parse the url to determine what was requested
    //
    
    //
    // Setup the base url as the base url for this page. This may be altered below
    // as the uri_split is processed, but we do not want to alter the original passed in.
    //
    $base_url = $args['base_url'];

    //
    // Check if tag type specified
    //
    if( isset($args['uri_split'][0]) && $args['uri_split'][0] != '' 
        && isset($tag_types[$args['uri_split'][0]]) && $tag_types[$args['uri_split'][0]]['visible'] == 'yes'
        ) {
        $type_permalink = $args['uri_split'][0];
        $tag_type = $tag_types[$type_permalink]['tag_type'];
        $tag_title = $tag_types[$type_permalink]['name'];
        $display = 'type';
        $base_url .= '/' . $type_permalink;

        //
        // Check if recipe was specified
        //
        if( isset($args['uri_split'][1]) && $args['uri_split'][1] != '' 
            && isset($args['uri_split'][2]) && $args['uri_split'][2] != '' ) {
            $tag_permalink = $args['uri_split']['1'];
            $recipe_permalink = $args['uri_split']['2'];
            $display = 'recipe';
            $ciniki['response']['head']['links'][] = array('rel'=>'canonical', 
                'href'=>$args['domain_base_url'] . '/' . $recipe_permalink);
            $ciniki['response']['head']['og']['url'] .= '/' . $recipe_permalink;
            $base_url .= '/' . $tag_permalink . '/' . $recipe_permalink;
            
            //
            // Check for gallery pic request
            //
            if( isset($args['uri_split'][3]) && $args['uri_split'][3] == 'gallery' 
                && isset($args['uri_split'][4]) && $args['uri_split'][4] != '' 
                ) {
                $image_permalink = $args['uri_split'][4];
                $display = 'recipepic';
            }
        } 

        //
        // Check if tag name was specified
        //
        elseif( isset($args['uri_split'][1]) && $args['uri_split'][1] != '' ) {
            $tag_type = $tag_types[$args['uri_split'][0]]['tag_type'];
            $tag_title = $tag_types[$args['uri_split'][0]]['name'];
            $tag_permalink = $args['uri_split']['1'];
            $display = 'tag';
            $ciniki['response']['head']['og']['url'] .= '/' . $type_permalink . '/' . $tag_permalink;
            $base_url .= '/' . $tag_permalink;
        }
        //
        // Setup type og 
        //
        else {
            $ciniki['response']['head']['og']['url'] .= '/' . $type_permalink;
        }
    }

    //
    // Check if recipe url request without tag path
    //
    elseif( isset($args['uri_split'][0]) && $args['uri_split'][0] != '' ) {
        $recipe_permalink = $args['uri_split'][0];
        $display = 'recipe';
        //
        // Check for gallery pic request
        //
        if( isset($args['uri_split'][1]) && $args['uri_split'][1] == 'gallery'
            && isset($args['uri_split'][2]) && $args['uri_split'][2] != '' 
            ) {
            $image_permalink = $args['uri_split'][2];
            $display = 'recipepic';
        }
        $ciniki['response']['head']['og']['url'] .= '/' . $recipe_permalink;
        $base_url .= '/' . $recipe_permalink;
    }

    //
    // Nothing selected, default to first tag type
    //
    else {
        $display = 'type';
        foreach($tag_types as $permalink => $type) {
            if( $type['visible'] == 'yes' ) {
                $tag_type = $type['tag_type'];
                $type_permalink = $permalink;
                $tag_title = $type['name'];
                $ciniki['response']['head']['og']['url'] .= '/' . $permalink;
                $base_url .= '/' . $permalink;
                break;
            }
        }
    }

    //
    // Get the content to display
    //

    if( $display == 'type' ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'web', 'tags');
        $rc = ciniki_recipes_web_tags($ciniki, $settings, $ciniki['request']['business_id'], $tag_type);
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['tags']) ) {
            $tags = $rc['tags'];
        } else {
            $tags = array();
        }
    
        $page['title'] .= ($page['title']!=''?' - ':'') . $tag_types[$type_permalink]['name'];
        $page['breadcrumbs'][] = array('name'=>$tag_types[$type_permalink]['name'], 'url'=>$args['base_url'] . '/' . $type_permalink);

        if( count($tags) > 25 || $tag_type == '20' ) {
            $page['blocks'][] = array('type'=>'tagcloud', 'base_url'=>$base_url, 'tags'=>$tags);
        } elseif( count($tags) > 0 ) {
            $page['blocks'][] = array('type'=>'tagimages', 'base_url'=>$base_url, 'tags'=>$tags);
        } else {
            $page['blocks'][] = array('type'=>'message', 'content'=>"I'm sorry, but we don't have any recipes for that category.");
        }
    }
    elseif( $display == 'tag' ) {
        //
        // Get the items for the specified category
        //
        ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'web', 'recipes');
        $rc = ciniki_recipes_web_recipes($ciniki, $settings, $business_id, array('tag_type'=>$tag_type, 'tag_permalink'=>$tag_permalink));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        $recipes = $rc['recipes'];

        // Get the human tag name for the titles
        $tag_name = $ciniki['request']['uri_split'][1];
        foreach($recipes as $recipe) {
            $tag_name = $recipe['tag_name'];
            break;
        }

//      $page['title'] = ($page['title']!=''?' - ':'') . $tag_types[$type_permalink]['name'] . ' - ' . $tag_name;
        $page['title'] .= ($page['title']!=''?' - ':'') . $tag_name;
        $page['breadcrumbs'][] = array('name'=>$tag_types[$type_permalink]['name'], 'url'=>$args['base_url'] . '/' . $type_permalink);
        $page['breadcrumbs'][] = array('name'=>$tag_name, 'url'=>$args['base_url'] . '/' . $type_permalink . '/' . urlencode($tag_name));

        if( count($recipes) > 0 ) {
            $page['blocks'][] = array('type'=>'imagelist', 'base_url'=>$base_url, 'noimage'=>'yes', 'list'=>$recipes);
        } else {
            $page['blocks'][] = array('type'=>'message', 'content'=>"I'm sorry, but there doesn't seem to be any recipes available.");
        }
    }
    elseif( $display == 'recipe' || $display == 'recipepic' ) {
        //
        // Load the recipe to get all the details, and the list of images.
        // It's one query, and we can find the requested image, and figure out next
        // and prev from the list of images returned
        //
        ciniki_core_loadMethod($ciniki, 'ciniki', 'recipes', 'web', 'recipeDetails');
        $rc = ciniki_recipes_web_recipeDetails($ciniki, $settings, 
            $ciniki['request']['business_id'], $recipe_permalink, (isset($tag_permalink)?$tag_permalink:''));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        $recipe = $rc['recipe'];
        if( isset($rc['tag_name']) ) {
            $tag_name = $rc['tag_name'];
        }
        $ciniki['response']['head']['og']['description'] = strip_tags($recipe['description']);

        //
        // Reset page title to be the recipe name
        //
        $page['title'] .= ($page['title']!=''?' - ':'') . $recipe['name'];
        $ciniki['response']['head']['og']['title'] = strip_tags($recipe['name']);

        if( isset($tag_permalink) && $tag_permalink != '' ) {
            $page['breadcrumbs'][] = array('name'=>$tag_types[$type_permalink]['name'], 'url'=>$args['base_url'] . '/' . $type_permalink);
            $page['breadcrumbs'][] = array('name'=>$tag_name, 'url'=>$args['base_url'] . '/' . $type_permalink . '/' . urlencode($tag_name));
        }
        $page['breadcrumbs'][] = array('name'=>$recipe['name'], 'url'=>$base_url);
        
        if( $display == 'recipepic' ) {
            if( !isset($recipe['images']) || count($recipe['images']) < 1 ) {
                $page['blocks'][] = array('type'=>'message', 'content'=>"I'm sorry, but we can't seem to find the image you requested.");
            } else {
                ciniki_core_loadMethod($ciniki, 'ciniki', 'web', 'private', 'galleryFindNextPrev');
                $rc = ciniki_web_galleryFindNextPrev($ciniki, $recipe['images'], $image_permalink);
                if( $rc['stat'] != 'ok' ) {
                    return $rc;
                }
                if( $rc['img'] == NULL ) {
                    $page['blocks'][] = array('type'=>'message', 'content'=>"I'm sorry, but we can't seem to find the image you requested.");
                } else {
                    $page['breadcrumbs'][] = array('name'=>$rc['img']['title'], 'url'=>$base_url . '/gallery/' . $image_permalink);
                    if( $rc['img']['title'] != '' ) {
                        $page['title'] .= ' - ' . $rc['img']['title'];
                    }
                    $block = array('type'=>'galleryimage', 'primary'=>'yes', 'image'=>$rc['img']);
                    if( $rc['prev'] != null ) {
                        $block['prev'] = array('url'=>$base_url . '/gallery/' . $rc['prev']['permalink'], 'image_id'=>$rc['prev']['image_id']);
                    }
                    if( $rc['next'] != null ) {
                        $block['next'] = array('url'=>$base_url . '/gallery/' . $rc['next']['permalink'], 'image_id'=>$rc['next']['image_id']);
                    }
                    $page['blocks'][] = $block;
                }
            }
        } 

        //
        // Display recipe
        //
        else {  
            //
            // Add primary image
            //
            if( isset($recipe['image_id']) && $recipe['image_id'] > 0 ) {
                $page['blocks'][] = array('type'=>'asideimage', 'primary'=>'yes', 'image_id'=>$recipe['image_id'], 'title'=>$recipe['name'], 'caption'=>'');
            }
            
            //
            // Add description
            //
            if( isset($recipe['description']) && $recipe['description'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'title'=>'', 'content'=>$recipe['description']);
            }

            //
            // Add technical details for recipe
            //
            $details = array();
            if( isset($recipe['num_servings']) && $recipe['num_servings'] != '' ) {
                $details[] = array('name'=>'Servings', 'value'=>$recipe['num_servings']);
            }
            if( isset($recipe['prep_time']) && $recipe['prep_time'] != '' ) {
                $details[] = array('name'=>'Prep Time', 'value'=>$recipe['prep_time'] . " minutes");
            }
            if( isset($recipe['roast_time']) && $recipe['roast_time'] != '' ) {
                $details[] = array('name'=>'Roast Time', 'value'=>$recipe['roast_time'] . " minutes");
            }
            if( isset($recipe['cook_time']) && $recipe['cook_time'] != '' ) {
                $details[] = array('name'=>'Cook Time', 'value'=>$recipe['cook_time'] . " minutes");
            }
            if( count($details) > 0 ) {
                $page['blocks'][] = array('type'=>'details', 'details'=>$details);
            }

            //
            // Add ingredients list
            //
            if( isset($recipe['ingredients']) && $recipe['ingredients'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'title'=>'Ingredients', 'content'=>$recipe['ingredients']);
            }

            //
            // Add instructions
            //
            if( isset($recipe['instructions']) && $recipe['instructions'] != '' ) {
                $page['blocks'][] = array('type'=>'content', 'title'=>'Instructions', 'wide'=>'yes', 'content'=>$recipe['instructions']);
            }

            //
            // Add print options
            //
            $page['blocks'][] = array('type'=>'printoptions', 
                'options'=>array(
                    array('name'=>'Print this recipe', 'url'=>$args['base_url'] . '/download/single/' . $recipe['permalink'] . '.pdf')
                ));
            
            //
            // Add the share information
            //
            if( !isset($settings['page-recipes-share-buttons']) || $settings['page-recipes-share-buttons'] == 'yes' ) {
                $tags = array();
                // FIXME: Get tags for recipe
                $page['blocks'][] = array('type'=>'sharebuttons', 'pagetitle'=>$recipe['name'], 'tags'=>$tags);
            }

            //
            // Display the additional images for the recipe
            //
            if( isset($recipe['images']) && count($recipe['images']) > 0 ) {
                $page['blocks'][] = array('type'=>'gallery', 'title'=>'Additional Images', 'base_url'=>$base_url . '/gallery', 'images'=>$recipe['images']);
            }

            //
            // FIXME: Display the similar recipes
            //
/*          if( isset($recipe['similar']) && count($recipe['similar']) > 0 ) {
                $page_content .= "<br clear='both'/>";
                $page_content .= "<h2 class='entry-title'>Similar Recipes</h2>\n"
                    . "";
                ciniki_core_loadMethod($ciniki, 'ciniki', 'web', 'private', 'processCIList');
                $similar_base_url = $ciniki['request']['base_url'] . "/recipes/";
                $rc = ciniki_web_processCIList($ciniki, $settings, $similar_base_url, array('0'=>array(
                    'name'=>'', 'noimage'=>'/ciniki-web-layouts/default/img/noimage_240.png',
                    'list'=>$recipe['similar'])), array());
                if( $rc['stat'] != 'ok' ) {
                    return $rc;
                }
                $page_content .= "<div class='entry-content'>" . $rc['content'] . "</div>";
                $page_content .= "</article>";
            } */
        }
    }

    //
    // The submenu 
    //
    $page['submenu'] = array();
    foreach($tag_types as $tag_permalink => $tag) {
        if( $tag['visible'] == 'yes' ) {
            $page['submenu'][$tag_permalink] = array('name'=>$tag['name'], 'url'=>$args['base_url'] . '/' . $tag_permalink);
        }
    }



    return array('stat'=>'ok', 'page'=>$page);
}
?>
