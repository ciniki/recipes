<?php
//
// Description
// -----------
// This function will output a pdf document as a series of thumbnails.
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_recipes_templates_single($ciniki, $business_id, $categories, $args) {

    require_once($ciniki['config']['ciniki.core']['lib_dir'] . '/tcpdf/tcpdf.php');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheOriginal');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'businessDetails');

    //
    // Load business details
    //
    $rc = ciniki_businesses_businessDetails($ciniki, $business_id);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['details']) && is_array($rc['details']) ) {   
        $business_details = $rc['details'];
    } else {
        $business_details = array();
    }

    //
    // Load INTL settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $business_id);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_currency_fmt = numfmt_create($rc['settings']['intl-default-locale'], NumberFormatter::CURRENCY);
    $intl_currency = $rc['settings']['intl-default-currency'];

    //
    // Create a custom class for this document
    //
    class MYPDF extends TCPDF {
        public $business_name = '';
        public $title = '';
        public $coverpage = 'no';
        public $toc = 'no';
        public $toc_categories = 'no';
        public $doublesided = 'no';
        public $pagenumbers = 'yes';
        public $footer_height = 0;
        public $header_height = 0;
        public $footer_text = '';

        public function Header() {
            $this->SetFont('helvetica', 'B', 20);
            if( $this->title != '' ) {
                $this->Cell(0, 20, $this->title, 0, false, 'C', 0, '', 0, false, 'M', 'B');
            }
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            // Set font
            if( $this->pagenumbers == 'yes' ) {
                $this->SetY(-18);
                $this->SetFont('helvetica', 'I', 8);
                $this->Cell(0, 10, $this->footer_text . '  --  Page ' . $this->getAliasNumPage().' of '.$this->getAliasNbPages(), 
                    0, false, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

        public function AddMyPage($ciniki, $business_id, $category_title, $title, $recipe) {
            // Add a page
            $this->title = $title;
            $this->AddPage();
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->SetDrawColor(51);
            $this->SetLineWidth(0.15);

            // Add a table of contents bookmarks
            if( $this->toc == 'yes' && $category_title !== NULL ) {
                if( $this->toc_categories == 'yes' && $category_title != '' ) {
                    $this->Bookmark($category_title, 0, 0, '', '');
                }
                if( $this->toc_categories == 'yes' ) {
                    $this->Bookmark($this->title, 1, 0, '', '');
                } else {
                    $this->Bookmark($this->title, 0, 0, '', '');
                }
            }

            //
            // Calculate the size of instructions, full width of page
            //
            $instructions_height = 14; // Start with "Instructions" label height
            $instructions_box_width = $this->getPageWidth() - $this->left_margin - $this->right_margin;
            $instructions_content = preg_split("/\n\s*\n/m", $recipe['instructions']);
            $this->SetFont('', '', '12');
            foreach($instructions_content as $cid => $cnt) {
                $instructions_content[$cid] = strip_tags(preg_replace("/&nbsp;/", ' ', $cnt));
                $instructions_height += $this->getStringHeight($instructions_box_width, $cnt);
                $instructions_height += 3;
            }

            //
            // Calculate size of ingredients and instructions
            //
            $ingredients_box_width = ($this->getPageWidth() - $this->left_margin - $this->right_margin - $this->middle_margin);
            $image = NULL;
            if( $recipe['image_id'] > 0 ) {
                $rc = ciniki_images_loadCacheOriginal($ciniki, $business_id, $recipe['image_id'], 2000, 2000);
                if( $rc['stat'] == 'ok' ) {
                    $image = $rc['image'];
                    $ingredients_box_width = (($this->getPageWidth() - $this->left_margin - $this->right_margin - $this->middle_margin)/2);
                    $img_box_width = (($this->getPageWidth() - $this->left_margin - $this->right_margin - $this->middle_margin)/2);
                }
            }

            $ingredients_height = 0;
            if( $recipe['num_servings'] != '' && $recipe['num_servings'] > 0 ) {
                $ingredients_height += 6;
            }
            if( $recipe['prep_time'] != '' && $recipe['prep_time'] > 0 ) {
                $recipe['prep_time'] .= ' minutes';
                $ingredients_height += 6;
            }
            if( $recipe['roast_time'] != '' && $recipe['roast_time'] > 0 ) {
                $recipe['roast_time'] .= ' minutes';
                $ingredients_height += 6;
            }
            if( $recipe['cook_time'] != '' && $recipe['cook_time'] > 0 ) {
                $recipe['cook_time'] .= ' minutes';
                $ingredients_height += 6;
            }

            $ingredients_height += 14; // Start with "Ingredients" label height
            $ingredients_content = preg_split("/\n\s*\n/m", $recipe['ingredients']);
            $this->SetFont('', '', '12');
            foreach($ingredients_content as $cid => $cnt) {
                $ingredients_content[$cid] = strip_tags(preg_replace("/&nbsp;/", ' ', $cnt));
                $ingredients_height += $this->getStringHeight($ingredients_box_width, strip_tags($cnt));
                $ingredients_height += 3;
            }

            //
            // FIXME: Add checks for page to large and shrink font/picture
            //

            //
            // Add image
            //
            $cur_x = $this->getX();
            $cur_y = $this->getY();
            if( $image != NULL ) {
                $img_box_height = $ingredients_height;
                $this->SetX($this->left_margin + $this->middle_margin + $ingredients_box_width);
                $img = $this->Image('@'.$image, '', '', $img_box_width, $img_box_height, 'JPEG', '', '', false, 300, '', false, false, 
                    array('LTRB'=>array('color'=>array(128,128,128))), 'CT');
            }

            //
            // Add ingredients
            //
            $this->SetX($cur_x);
            $this->SetY($cur_y);
            $this->SetFont('', '', '12');
            if( $recipe['num_servings'] != '' && $recipe['num_servings'] > 0 ) {
                $this->Cell(0, 6, 'Number of Servings: ' . $recipe['num_servings'], 0, 1, 'L');
                $this->Ln(0);
            }
            if( $recipe['prep_time'] != '' && $recipe['prep_time'] > 0 ) {
                $this->Cell(0, 6, 'Prep Time: ' . $recipe['prep_time'], 0, 1, 'L');
                $this->Ln(0);
            }
            if( $recipe['roast_time'] != '' && $recipe['roast_time'] > 0 ) {
                $this->Cell(0, 6, 'Roast Time: ' . $recipe['roast_time'], 0, 1, 'L');
                $this->Ln(0);
            }
            if( $recipe['cook_time'] != '' && $recipe['cook_time'] > 0 ) {
                $this->Cell(0, 6, 'Cook Time: ' . $recipe['cook_time'], 0, 1, 'L');
                $this->Ln(0);
            }
            $this->SetFont('', 'B', '16');
            $this->Cell(0, 10, 'Ingredients', 0, 1, 'L');
            $this->Ln(1);
            $this->SetFont('', '', '12');
            foreach($ingredients_content as $cnt) {
                $this->MultiCell($ingredients_box_width, 5, $cnt, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'T');
                $this->Ln(2);
            }

            if( $this->getY() < $cur_y + $ingredients_height ) {
                $this->SetY($cur_y + $ingredients_height);
            }

            //
            // Add the instructions
            //
            $this->SetFont('', 'B', '16');
            $this->Cell(0, 10, 'Instructions', 0, 1, 'L');
            $this->Ln(1);
            $this->SetFont('', '', '12');
            foreach($instructions_content as $cnt) {
                $this->MultiCell($instructions_box_width, 5, $cnt, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'T');
                $this->Ln(2);
            }
        }
    }

    //
    // Start a new document
    //
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

    $pdf->title = $args['title'];

    // Set PDF basics
    $pdf->SetCreator('Ciniki');
    $pdf->SetAuthor($business_details['name']);
    $pdf->footer_text = $business_details['name'];
    $pdf->SetTitle($args['title']);
    $pdf->SetSubject('');
    $pdf->SetKeywords('');

    if( isset($args['doublesided']) ) {
        $pdf->doublesided = $args['doublesided'];
    }

    // set margins
    $pdf->header_height = 25;
    $pdf->footer_height = 15;
    $pdf->middle_margin = 20;
    $pdf->top_margin = 10;
    $pdf->left_margin = 25;
    $pdf->right_margin = 25;
    $pdf->SetMargins($pdf->left_margin, $pdf->header_height, $pdf->right_margin);
    $pdf->SetHeaderMargin($pdf->top_margin);
    $pdf->SetFooterMargin($pdf->footer_height);

    // Set font
    $pdf->SetFont('times', 'BI', 10);
    $pdf->SetCellPadding(0);

    //
    // Check if coverpage is to be outputed
    //
    if( isset($args['coverpage']) && $args['coverpage'] == 'yes' ) {
        $pdf->coverpage = 'yes';
        $pdf->title = '';
        if( isset($args['title']) && $args['title'] != '' ) {
            $title = $args['title'];
            $pdf->footer_text .= '  --  ' . $args['title'];
        } else {
            $title = "Recipes";
        }
        $pdf->pagenumbers = 'no';
        $pdf->AddPage('P');
        
        if( isset($args['coverpage-image']) && $args['coverpage-image'] > 0 ) {
            $img_box_width = 180;
            $img_box_height = 150;
            $rc = ciniki_images_loadCacheOriginal($ciniki, $business_id, $args['coverpage-image'], 2000, 2000);
            if( $rc['stat'] == 'ok' ) {
                $image = $rc['image'];
                $pdf->SetLineWidth(0.25);
                $pdf->SetDrawColor(50);
                $img = $pdf->Image('@'.$image, '', '', $img_box_width, $img_box_height, 'JPEG', '', '', false, 300, '', false, false, 0, 'CT');
            }
            $pdf->SetY(-50);
        } else {
            $pdf->SetY(-100);
        }


        $pdf->SetFont('times', 'B', '30');
        $pdf->MultiCell(180, 5, $title, 0, 'C', false, 1, '', '', true, 0, false, true, 0, 'T');
        $pdf->endPage();
        if( $pdf->doublesided == 'yes' ) {
            $pdf->AddPage();
            $pdf->Cell(0, 0, '');
            $pdf->endPage();
        }
    }
    $pdf->pagenumbers = 'yes';

    //
    // Add the recipes items
    //
    $page_num = 1;
    $pdf->toc_categories = 'no';
    if( count($categories) > 1 ) {
        $pdf->toc_categories = 'yes';
    }
    if( isset($args['toc']) && $args['toc'] == 'yes' ) {
        $pdf->toc = 'yes';
    }
    if( $pdf->toc == 'yes' && $pdf->doublesided == 'yes' ) {
        $pdf->AddPage();
        $pdf->Cell(0, 0, '');
        $pdf->endPage();
    }

    foreach($categories as $cid => $category) {
        $recipe_num = 1;
        foreach($category['recipes'] as $rid => $recipe) {
            if( isset($args['removetext']) && $args['removetext'] != '' ) {
                $recipe['title'] = preg_replace('/' . $args['removetext'] . '/', '', $recipe['name']);
            }
            $pdf->title = $recipe['name'];

            // 
            // Add the recipe
            //
            $pdf->AddMyPage($ciniki, $business_id, ($recipe_num==1?$category['name']:''), $recipe['name'], $recipe);
            $page_num++;
            $recipe_num++;  
        }
    }

    if( isset($args['toc']) && $args['toc'] == 'yes' ) {
        $pdf->title = 'Table of Contents';
        $pdf->addTOCPage();
        $pdf->Ln(8);
        $pdf->SetFont('', '', 14);
        $pdf->pagenumbers = 'no';
        $pdf->addTOC((($pdf->coverpage=='yes')?($pdf->doublesided=='yes'?3:2):0), 'courier', '.', 'INDEX', 'B');
        $pdf->pagenumbers = 'yes';
        $pdf->endTOCPage();
    }

    return array('stat'=>'ok', 'pdf'=>$pdf);
}
?>
