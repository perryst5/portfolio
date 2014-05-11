<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/

$GLOBALS['magiczoomplus_module_loaded'] = 'true'; // to fix boxes and pages conflict, I thunk we could find a better way in future
if (defined('HTTP_ADMIN')) {
    define ('MTOOLBOX_ADMIN_FOLDER_magiczoomplus',str_replace('catalog',preg_replace('/.*?([^\/]*)\/$/is','$1',HTTP_ADMIN),DIR_APPLICATION) . 'controller/module/magiczoomplus-opencart-module/');
} else {
    define ('MTOOLBOX_ADMIN_FOLDER_magiczoomplus',DIR_APPLICATION . 'controller/module/magiczoomplus-opencart-module/');
}

function magiczoomplus_LoadScroll($tool) {

	if($tool->params->checkValue('magicscroll', 'yes') && $tool->type == 'standard') {
		require_once (MTOOLBOX_ADMIN_FOLDER_magiczoomplus.'magicscroll.module.core.class.php');
		$scroll = new MagicScrollModuleCoreClass();
		$scroll->params->appendArray($tool->params->getArray());
		$GLOBALS["magictoolbox"]["scroll"] = & $tool;
		if($tool->params->checkValue('template', 'classic')) {
			$scroll->params->set('direction', 'right');
		}
		if($tool->params->checkValue('template', 'selectors-left')) {
			$scroll->params->set('direction', 'bottom');
		}
		return $scroll;
	}

	return false;


}

function magiczoomplus($content, $currentController = false , $type = false, $info = false) {

    if ($currentController->config->get('magiczoomplus_status') != 0) {
        $tool = & magiczoomplus_load_core_class($currentController);

        //set_params_from_config($currentController->config);

        $enabled_on_this_page = false;

        unset($GLOBALS['magictoolbox']['items']);

        $tool->params->set('disable-expand','No');
        $tool->params->set('disable-zoom','No');

        if ($tool->type == 'standard') { //do not apply MSS-like modules to category & product pages
            if ($type && $type == 'category' && !$tool->params->checkValue('use-effect-on-category-page', 'No')) {
                $enabled_on_this_page = true;
                if ($tool->params->checkValue('use-effect-on-category-page','Zoom')) {
                    $tool->params->set('disable-expand','Yes');
                    $tool->params->set('disable-zoom','No');
                } else if ($tool->params->checkValue('use-effect-on-category-page','Expand')) {
                    $tool->params->set('disable-expand','No');
                    $tool->params->set('disable-zoom','Yes');
                }
            }
            if ($type && $type == 'manufacturers' && !$tool->params->checkValue('use-effect-on-manufacturers-page', 'No')) {
                $enabled_on_this_page = true;
                if ($tool->params->checkValue('use-effect-on-manufacturers-page','Zoom')) {
                    $tool->params->set('disable-expand','Yes');
                    $tool->params->set('disable-zoom','No');
                } else if ($tool->params->checkValue('use-effect-on-manufacturers-page','Expand')) {
                    $tool->params->set('disable-expand','No');
                    $tool->params->set('disable-zoom','Yes');
                }
            }
            if ($type && $type == 'search' && !$tool->params->checkValue('use-effect-on-search-page', 'No')) {
                $enabled_on_this_page = true;
                if ($tool->params->checkValue('use-effect-on-search-page','Zoom')) {
                    $tool->params->set('disable-expand','Yes');
                    $tool->params->set('disable-zoom','No');
                } else if ($tool->params->checkValue('use-effect-on-search-page','Expand')) {
                    $tool->params->set('disable-expand','No');
                    $tool->params->set('disable-zoom','Yes');
                }
            }
            if ($type && $type == 'product' && !$tool->params->checkValue('use-effect-on-product-page', 'No')) {
                $enabled_on_this_page = true;
                if ($tool->params->checkValue('use-effect-on-product-page','Zoom')) {
                    $tool->params->set('disable-expand','Yes');
                    $tool->params->set('disable-zoom','No');
                } else if ($tool->params->checkValue('use-effect-on-product-page','Expand')) {
                    $tool->params->set('disable-expand','No');
                    $tool->params->set('disable-zoom','Yes');
                } else if ($tool->params->checkValue('use-effect-on-product-page','Swap images only')) {
                    $tool->params->set('disable-expand','Yes');
                    $tool->params->set('disable-zoom','Yes');
                }
            }
        }

        if ($tool->type == 'circle') { //Apply 360 only to Products Page 
            if ($type && $type == 'product') {
                    $enabled_on_this_page = true;
            }

		} else {

			if ($type && ($type == 'latest_home_category' || $type == 'latest_home' || $type == 'latest_right' || $type == 'latest_left' || $type == 'latest_content_top' || $type == 'latest_content_bottom' || $type == 'latest_column_left' || $type == 'latest_column_right') && !$tool->params->checkValue('use-effect-on-latest-box', 'No')) {
				$enabled_on_this_page = true;
				if ($tool->params->checkValue('use-effect-on-latest-box','Zoom')) {
					$tool->params->set('disable-expand','Yes');
					$tool->params->set('disable-zoom','No');
				} else if ($tool->params->checkValue('use-effect-on-latest-box','Expand')) {
					$tool->params->set('disable-expand','No');
					$tool->params->set('disable-zoom','Yes');
				}
			}
			if ($type && ($type == 'featured_home' || $type == 'featured_right' || $type == 'featured_left' || $type == 'featured_left' || $type == 'featured_content_top' || $type == 'featured_content_bottom' || $type == 'featured_column_left' || $type == 'featured_column_right') && !$tool->params->checkValue('use-effect-on-featured-box', 'No') ) {
				$enabled_on_this_page = true;
				if ($tool->params->checkValue('use-effect-on-featured-box','Zoom')) {
					$tool->params->set('disable-expand','Yes');
					$tool->params->set('disable-zoom','No');
				} else if ($tool->params->checkValue('use-effect-on-featured-box','Expand')) {
					$tool->params->set('disable-expand','No');
					$tool->params->set('disable-zoom','Yes');
				}
			}
			if ($type && ($type == 'special_home' || $type == 'special_right' || $type == 'special_left' || $type == 'special_content_top' || $type == 'special_content_bottom' || $type == 'special_column_left' || $type == 'special_column_right') && !$tool->params->checkValue('use-effect-on-special-box', 'No')) {
				$enabled_on_this_page = true;
				if ($tool->params->checkValue('use-effect-on-special-box','Zoom')) {
					$tool->params->set('disable-expand','Yes');
					$tool->params->set('disable-zoom','No');
				} else if ($tool->params->checkValue('use-effect-on-special-box','Expand')) {
					$tool->params->set('disable-expand','No');
					$tool->params->set('disable-zoom','Yes');
				}
			}
			if ($type && ($type == 'bestseller_home' || $type == 'bestseller_right' || $type == 'bestseller_left' || $type == 'bestseller_content_top' || $type == 'bestseller_content_bottom' || $type == 'bestseller_column_left' || $type == 'bestseller_column_right') && !$tool->params->checkValue('use-effect-on-bestsellers-box', 'No')) {
				$enabled_on_this_page = true;
				if ($tool->params->checkValue('use-effect-on-bestsellers-box','Zoom')) {
					$tool->params->set('disable-expand','Yes');
					$tool->params->set('disable-zoom','No');
				} else if ($tool->params->checkValue('use-effect-on-bestsellers-box','Expand')) {
					$tool->params->set('disable-expand','No');
					$tool->params->set('disable-zoom','Yes');
				}
			}

		}

        if ($enabled_on_this_page) {

	    if ($type && $info) {
		$GLOBALS['magictoolbox']['page_type'] = $type;
		$GLOBALS['magictoolbox']['prods_info'] = $info;
	    } else {
		return $content;
	    }


            
            $oldContent = $content;
            $content = magiczoomplus_parse_contents($content,$currentController);
            if ($oldContent != $content) $content = magiczoomplus_set_headers($content);


            if ($type == 'product' && $tool->type == 'standard' && isset($GLOBALS['magictoolbox']['MagicZoomPlus']['main'])) {
                // template helper class
                require_once (MTOOLBOX_ADMIN_FOLDER_magiczoomplus.'magictoolbox.templatehelper.class.php');
                MagicToolboxTemplateHelperClass::setPath(MTOOLBOX_ADMIN_FOLDER_magiczoomplus.'templates');
                MagicToolboxTemplateHelperClass::setOptions($tool->params);
                $html = MagicToolboxTemplateHelperClass::render(array(
                    'main' => $GLOBALS['magictoolbox']['MagicZoomPlus']['main'],
                    'thumbs' => (count($GLOBALS['magictoolbox']['MagicZoomPlus']['selectors']) > 1) ? $GLOBALS['magictoolbox']['MagicZoomPlus']['selectors'] : array(),
                    'pid' => $GLOBALS['magictoolbox']['prods_info']['product_id'],
                ));

                $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
            }



        }
    }

    return $content;
}

function magiczoomplus_set_headers ($content, $headers = false) {

    if (empty($GLOBALS['magictoolbox']['page_type'])) return $content;

	if(defined('HTTP_ADMIN')) {
		$aFolder = preg_replace('/.*\/([^\/].*)\//is','$1',HTTP_ADMIN);
	} else {
		$aFolder = 'admin';
	}

    $plugin = $GLOBALS["magictoolbox"]["magiczoomplus"];

    if (!$headers) {
        $disableExpand = $plugin->params->getValue('disable-expand');
        $disableZoom = $plugin->params->getValue('disable-zoom');
        $plugin->params->set('disable-expand', 'No');
        $plugin->params->set('disable-zoom', 'No');

        $prefix = '';
        if (preg_match("/components\/com_(ayelshop|aceshop|mijoshop)\/opencart\//ims",DIR_APPLICATION,$matches) || strpos($content,'</head>')) {
            $prefix = '';
            if ($matches) $prefix = 'components/com_'.$matches[1].'/opencart/';
            //$headers = $plugin->headers($prefix.$aFolder.'/controller/module/magiczoomplus-opencart-module',$prefix.$aFolder.'/controller/module/magiczoomplus-opencart-module');
            $headers = $plugin->headers($prefix.'catalog/view/javascript',$prefix.'catalog/view/css');
        }

        $plugin->params->set('disable-expand', $disableExpand);
        $plugin->params->set('disable-zoom', $disableZoom);

    }
    $scroll = magiczoomplus_LoadScroll($plugin);

    if($scroll) {
        static $scrollHeaders = '';
        if(!defined('MagicScrollModuleHeaders')) {
            //$scrollHeaders = $scroll->headers($prefix.$aFolder.'/controller/module/magiczoomplus-opencart-module');
            $scrollHeaders = $scroll->headers($prefix.'catalog/view/javascript',$prefix.'catalog/view/css');
        }
        if (!empty($scrollHeaders)) {
            $headers .= $scrollHeaders;
        }
    }

    if (!$plugin->params->checkValue('use-effect-on-category-page', 'No') || !$plugin->params->checkValue('use-effect-on-manufacturers-page', 'No') || !$plugin->params->checkValue('use-effect-on-search-page', 'No')) {//fix for category && manufacturers view switch
        $headers .= '<script type="text/javascript">
                    $mjs(document).je1(\'domready\', function() {
                      if (typeof display !== \'undefined\') {
                        var olddisplay = display;
                        window.display = function (view) {
                          MagicZoomPlus.stop();
                          olddisplay(view);
                          MagicZoomPlus.start();
                        }
                      }
                    });
                   </script>';
    }
        if (preg_match('/optionimage\.js/is',$content)) {
        $headers .= '<script type="text/javascript">
                        $mjs(document).je1(\'domready\', function() {
                                zoomId = $(\'.MagicToolboxSelectorsContainer\').attr(\'id\').match(/[0-9]+/)[0];
                                $(\'.options .option select\').change(
                                    function() {
                                        var newsrc = $(this).find(\'option:selected\').attr(\'rel\');
                                        var id = $(this).find(\'option:selected\').attr(\'value\');
                                        var optId = $(this).attr(\'name\').match(/[0-9]+/)+"";
                                        
                                        if(newsrc.indexOf(\'no_image\') > 0) {
                                            // do nothing!
                                        } else {
					    MagicZoomPlus.update(\'MagicZoomPlusImage\'+ zoomId, MagicZoomPlusOptiMages[optId][id][\'original\'], MagicZoomPlusOptiMages[optId][id][\'thumb\']);
                                        }
                                        
                                    }
                                );
                        });
                      </script>';
    }


    if ($headers && $content && !isset($GLOBALS['magiczoomplus_headers_set'])) {

        if (preg_match("/components\/com_(ayelshop|aceshop|mijoshop)\/opencart\//ims",DIR_APPLICATION)) {
            $content = $headers.$content;
            $GLOBALS['magiczoomplus_headers_set'] = true;
        } else {
            $content = preg_replace('/\<\/head\>/is',"\n".$headers."\n</head>",$content,-1,$matched);
        }
        //$content = preg_replace('/\<\/head\>/is',"\n".$headers."\n</head>",$content,-1,$matched);

        if ($matched > 0) $GLOBALS['magiczoomplus_headers_set'] = true;
    }
    return $content;
}

function &magiczoomplus_load_core_class($currentController = false) {
    if(!isset($GLOBALS["magictoolbox"])) $GLOBALS["magictoolbox"] = array();
    if(!isset($GLOBALS["magictoolbox"]["magiczoomplus"])) {
        /* load core class */
        require_once (MTOOLBOX_ADMIN_FOLDER_magiczoomplus.'magiczoomplus.module.core.class.php');
        $tool = new MagicZoomPlusModuleCoreClass();
        /* add category for core params */
        $params = $tool->params->getArray();
        foreach($params as $k => $v) {
            $v['category'] = array(
                "name" => 'General options',
                "id" => 'general-options'
            );
            $params[$k] = $v;
        }
        $tool->params->appendArray($params);
        $tool->params->set('disable-expand', 'No');
        $tool->params->set('disable-zoom', 'No');
        $tool->general->params['disable-expand'] = $tool->params->params['disable-expand'];
        $tool->general->params['disable-zoom'] = $tool->params->params['disable-zoom'];

        $GLOBALS["magictoolbox"]["magiczoomplus"] = & $tool;
    }
    if($currentController) {

        $GLOBALS['magictoolbox']['currentController'] = $currentController; //SEO url fixe

        $query = $currentController->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = 'magiczoomplus'");
        foreach($query->rows as $param) {
            $GLOBALS["magictoolbox"]["magiczoomplus"]->params->set($param['key'],$param['value']);
        }

    }
    return $GLOBALS["magictoolbox"]["magiczoomplus"];
}

function magiczoomplus_parse_contents($content,$currentController) {

    $plugin = $GLOBALS['magictoolbox']['magiczoomplus'];
    $type = $GLOBALS['magictoolbox']['page_type'];

     /*OptionsImages fix START*/
    if ($type == 'product') { //use only on product page
      $options = $currentController->model_catalog_product->getProductOptions($GLOBALS['magictoolbox']['prods_info']['product_id']);

      $jsOptions = array();
      foreach ($options as $opt) {
	  $opt_id = $opt['product_option_id'];
	  $opt = $opt['option_value'];
	  
	  if (is_array($opt) && count($opt)) {
	      foreach ($opt as $option) {
		  $option_value = '';
		  if (!empty($option['option_image'])) {
		      $option_value = $option['option_image'];
		  } else if (!empty($option['image'])) {
		      $option_value = $option['image'];
		  }   
		  if (!empty($option_value)) {
		      $jsOptions[$opt_id][$option['product_option_value_id']]['original'] = magiczoomplus_getThumb('image/'.$option_value,'original');
		      $jsOptions[$opt_id][$option['product_option_value_id']]['thumb'] = magiczoomplus_getThumb('image/'.$option_value,'thumb');
		  }
	      }
	  }
      }
      $oldContent = $content;
      //$content = str_replace('</head>','<script type="text/javascript"> MagicZoomPlusOptiMages = '.json_encode($jsOptions).'; </script></head>',$content);
    }
    /*OptionsImages fix END*/
    


    

    //some bugs fix
    $content = str_replace("<!--code start-->",'',$content);
    $content = str_replace("<!--code end-->",'',$content);
    if (empty($GLOBALS['magictoolbox']['prods_info']['image']) && isset($GLOBALS['magictoolbox']['prods_info']['images'][0]['image'])) {
        $GLOBALS['magictoolbox']['prods_info']['image'] = $GLOBALS['magictoolbox']['prods_info']['images'][0]['image'];
    }

    if ($type == 'product') {
        //$content = fixProductCss($content); //fix most css issues on product page
        $enabled = true;
        if ($plugin->type == 'circle') {
            $all_img = $GLOBALS['magictoolbox']['prods_info']['images'];
            if (isset($GLOBALS['magictoolbox']['prods_info']['image']) && !empty($GLOBALS['magictoolbox']['prods_info']['image'])) {
                $all_img[]['image'] = $GLOBALS['magictoolbox']['prods_info']['image'];
            }
            $enabled = $plugin->enabled($all_img,$GLOBALS['magictoolbox']['prods_info']['product_id']);
        }

        if ($enabled) {
            $content = preg_replace("/(<a[^>]*?class[ =\"']*?fancybox[^>]*?>)([^<]*?(?:<div[^>]*?class=\"promotags\"[^>]*?><\/div>.*?)*)<img/ims","<style type=\"text/css\">.promotags{z-index:10000;}.image{position:relative;}</style>$2$1<img",$content);
            $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
            $content = preg_replace_callback("/{$pattern}/is",'magiczoomplus_callback',$content);
            //add main image to additional
            if (!isset($GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'])) return $content;
            $thumb = $GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'];

            if ($plugin->type == 'circle') {
                $content = preg_replace('/<a[^>]*?\#tab_image.*?>.*?<\/a>/is','',$content); // CUT SELECTORS TAB
                $content = preg_replace('/<a[^>]*?\#product_gallery.*?>.*?\/a>/is','',$content); // CUT SELECTORS TAB (shoppica)
                $content = preg_replace('/<div[^>]*?id=\"tab_image\"[^>*?]class=\"tab_page\".*?>.*?<div id="tab_related"/is','<div id="tab_related"',$content); // CUT SELECTORS DIV
                $content = preg_replace('/<div[^>]*?class=\"image-additional\"[^>]*?>.*?<\/div>/is','',$content); // CUT SELECTORS DIV

                /* FIXES BAD OpenCart Sorting*/
                $tArr = array();
		foreach ($GLOBALS['magictoolbox']['items'] as $item) {
		    $tArr[] = preg_replace('/(^.*?)([^\/]*\.(jpg|png|jpeg|gif))/is','$2',$item['img']);
		    $tArrr[] = preg_replace('/(^.*?)([^\/]*\.(jpg|png|jpeg|gif))/is','$1',$item['img']);
		}
		natcasesort($tArr);
		
		foreach ($tArr as $id => $value) {
		    $tArrrr[$id] = $tArrr[$id].$value;
		}
		$tArr = $tArrrr;
		foreach ($tArr as $id => $img) {
		    foreach ($GLOBALS['magictoolbox']['items'] as $it) {
			if ($it['img'] == $img) {
			    $ttArr[$id]['medium'] = $it['medium'];
			    $ttArr[$id]['img'] = $img;
			}
		    }
		}
		$GLOBALS['magictoolbox']['items'] = $ttArr;                
		/* FIXES BAD OpenCart Sorting*/
                
                $content = str_replace ('magiczoomplus_MAIN_IMAGE',$plugin->template($GLOBALS['magictoolbox']['items']),$content); //REPLACE MAIN IMAGE WITH EFFECT
            }

            $content = str_replace('<div class="image-additional">','<div class="image-additional">'.$thumb.' ',$content);
            $content = preg_replace('/<a[^>]*?\#product_gallery.*?>.*?\/a>/is','',$content); // CUT SELECTORS TAB (shoppica)
            $content = preg_replace('/<div[^>]*?id=[\'\"]product_gallery[\'\"].*?>.*?\/div>/is','',$content); // CUT SELECTORS DIV
            $content = preg_replace('/<span[^>]*?>[^<]*?'.$currentController->language->get('text_enlarge').'[^<]*?<\/span>/is','',$content); //REMOVE DEFAULT "Click to Enlarge"
        }


    } else if ($type == 'category' || $type == 'manufacturers' || $type == 'search' || strpos($type,'content_top') || strpos($type,'content_bottom') ||
			  ($type == 'latest_home_category' && $GLOBALS['magictoolbox']['page_type'] == 'latest_home') || 
		      ($type == 'featured_home_category' && $GLOBALS['magictoolbox']['page_type'] == 'featured_home')) {
        //if($type == 'latest_home_category') $GLOBALS['magictoolbox']['page_type'] = 'latest_home';
        preg_match_all('/<table[^>]class=[\"\']list[\'\"]>.*?<\/table>/is',$content,$table_contents);


		if (empty($table_contents[0]) && count($table_contents) < 2) { //FOR NEW OPENCART
			preg_match_all('/<div class="product-list">.*?<div class="pagination">/is',$content,$table_contents);
			if (empty($table_contents[0]) && count($table_contents) < 2) { //FOR OPENCART 1.5.x
				preg_match_all('/<div class="box-product">.*?<\/div>[^<]*<\/div>[^<]*<\/div>/is',$content,$table_contents);
                if (empty($table_contents[0]) && count($table_contents) < 2) {
                    preg_match_all('/<div class="product\-grid">.*?<div class="pagination">/is',$content,$table_contents);
                }
			}
			$content = str_replace('</head>','
						<style type="text/css">
						.product-list > div {
							overflow: hidden !important;}
						</style></head>',$content);
		}


        $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
        if (isset($table_contents[0]) && !is_array($table_contents[0])) {
            $result = preg_replace_callback("/{$pattern}/is",'magiczoomplus_callback_category',$table_contents[0]);
        } else if (isset($table_contents[0][0]) && !is_array($table_contents[0][0])) {
            $result = preg_replace_callback("/{$pattern}/is",'magiczoomplus_callback_category',$table_contents[0][0]);
        }

		if ($plugin->type == 'standard') {
            if (isset($table_contents[0]) && !is_array($table_contents[0])) {
                $content = str_replace($table_contents[0],$result,$content);
            } else if (isset($table_contents[0][0]) && !is_array($table_contents[0][0])) {
                $content = str_replace($table_contents[0][0],$result,$content);
            }
		} else if (isset($GLOBALS['magictoolbox']['items']) && count($GLOBALS['magictoolbox']['items']) >= $plugin->params->getValue('items')) {
			$options['id'] = $type;
			$options['title'] = 'Right';
			
            foreach($GLOBALS['magictoolbox']['items'] as $k => $v) {
                unset($GLOBALS['magictoolbox']['items'][$k]['description']);
            }
			
			
			$plugin->general->params['width'] = $plugin->params->params['width'];
			
			$plugin->params->set('width',$plugin->params->params['home-thumb-max-width']['value']);
			
            $toolHTML = $plugin->template($GLOBALS['magictoolbox']['items'], $options);
			$content = str_replace($table_contents[0], $toolHTML, $content);
		}



    } else if ($type) {
        $pattern = '(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>';
        $result = preg_replace_callback("/{$pattern}/is",'magiczoomplus_callback_category',$content);

        if ($plugin->type == 'standard') {
            $content = str_replace($content,$result,$content);
         } else if (isset($GLOBALS['magictoolbox']['items'])
            ) { //SLIDESHOW
            if (VERSION >= 1.5) {
				$pattern = '(^.*?<div class="box-product">)(.*)';
			} else {
				$pattern = '(^.*?<div[^>]*?\"middle\">)(.*)?(<div[^>]*?\"bottom">.*)';
			}

            if (!strpos($type,'_home') && !strpos($type,'content_')) {

                $thumbs_current = $plugin->params->getValue('thumbnails');
                $plugin->params->set('thumbnails','off');

                $arrows_current = $plugin->params->getValue('arrows');
                $plugin->params->set('arrows','No');


            } 

            $options['id'] = $type;
            foreach($GLOBALS['magictoolbox']['items'] as $k => $v) {
                unset($GLOBALS['magictoolbox']['items'][$k]['description']);
            }

            $toolHTML = $plugin->template($GLOBALS['magictoolbox']['items'], $options);
			if (VERSION >= 1.5) {
				$content = preg_replace("/{$pattern}/is",'$1'.$toolHTML.'</div></div></div>',$content);
			} else {
				$content = preg_replace("/{$pattern}/is",'$1'.$toolHTML.'</div>$3',$content);
			}
        }
    }
if (isset($thumbs_current)) $plugin->params->set('thumbnails',$thumbs_current);
if (isset($arrows_current)) $plugin->params->set('arrows',$arrows_current);
if (isset($direction_current)) $plugin->params->set('direction',$direction_current);
if (isset($jsOptions) && count($jsOptions)  && $content != $oldContent) $content = str_replace('</head>','<script type="text/javascript"> MagicZoomPlusOptiMages = '.json_encode($jsOptions).'; </script></head>',$content);
return $content;
}



function magiczoomplus_callback_category ($matches) {

    if (preg_match("/data\/Stick_Gallery/ims",$matches[0])) return $matches[0];//Product Label module support

    $plugin = $GLOBALS["magictoolbox"]["magiczoomplus"];
    $plugin_enabled = true;
    $result = $matches[0];
    if ($plugin_enabled) {
        $show_message_current = $plugin->params->getValue('show-message');
        $plugin->params->set('show-message','No');
        $caption_source_current = $plugin->params->getValue('caption-source');
        $plugin->params->set('caption-source','Title');
        $shop_dir = str_replace('system/','',DIR_SYSTEM);
        $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);
        $type = $GLOBALS['magictoolbox']['page_type'];
        $link = preg_replace("/^.*?href\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[1]);

        $id = preg_replace('/^.*?product_id=(\d+).*/is','$1',$link);

        if (!strpos($link,'product_id')) { //SEO links fix
            $currentController = $GLOBALS['magictoolbox']['currentController'];
            $config_seo_url_postfix = $currentController->config->get('config_seo_url_postfix');
            $furl = preg_replace('/.*\/([^\?]*).*/is','$1',$link);
            $furl = str_replace($config_seo_url_postfix, '', $furl);
            $query = $currentController->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '".$furl."'");
            $query = $query->rows[0]['query'];
            $id = preg_replace('/^.*?id=(\d+).*/is','$1',$query);
        }
        
        if (!is_numeric($id)) {
	    $id = preg_replace('/^([0-9]{1,})\-[a-z0-9]{1,}/ims','$1',$furl);
	}

        if (!is_numeric($id)) return $matches[0];
        $pid = $id;
        $p_info = magiczoomplus_getProductParams($id,$GLOBALS['magictoolbox']['prods_info']);
        if ($p_info['image'] == '') return $matches[0];
        $id = $id.'_'.$type;

        if ($plugin->params->checkValue('link-to-product-page','No')) $link='';

        $title = $p_info['name'];
        $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
        $description = $p_info['description'];
        $description = htmlspecialchars(htmlspecialchars_decode($description, ENT_QUOTES));

        $group = $type;

        $original = $image_dir.$p_info['image'];
        $img = magiczoomplus_getThumb($original,'original',$pid);

        if ($type != 'category') {
            $position = preg_replace('/.*?_(.*)/is','$1',$type);
        } else {
            $position = $type;
        }
        if ($plugin->type == 'standard') {
            $position = str_replace('column_','',$position);
			$cat_array=array('home','content_bottom','content_top');
            if (in_array($position,$cat_array)) $position = 'category';
            $thumb = magiczoomplus_getThumb($original,$position.'-thumb',$pid);
            $result = $plugin->template(compact('img','thumb','id','title','description','link','group'));
        } else {
	    $position = str_replace('column_','',$position);
	    if ($position == 'content_bottom' || $position == 'content_top') $position = 'category';
            $img = magiczoomplus_getThumb($original,$position.'-thumb',$pid);
            $thumb = magiczoomplus_getThumb($original,'home-selector-thumb',$pid);
            $GLOBALS['magictoolbox']['items'][] = array(
                'img' => $img,
                'thumb' => $thumb,
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'link' => $link,
            );
        }
    }
    $plugin->params->set('show-message',$show_message_current);
    $plugin->params->set('caption-source',$caption_source_current);
    return $result;
}

function magiczoomplus_callback ($matches) {

    if (preg_match("/data\/Stick_Gallery/ims",$matches[0])) return $matches[0];//Product Label module support

    $plugin = $GLOBALS["magictoolbox"]["magiczoomplus"];
    $plugin_enabled = true;
    $result = $matches[0];
    if(!preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*thickbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) && 
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*fancybox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*lightbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*yoxview(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       //!preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*cloud\-zoom(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*cloud\-zoom.*?(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*colorbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*jqzoom(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       //!preg_match("/rel\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*colorbox(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/rel\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*colorbox.*?(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0]) &&
       !preg_match("/rel\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*prettyPhoto\[gallery\](?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) {
        $plugin_enabled = false;
    }
    if ($plugin_enabled) {
        $shop_dir = str_replace('system/','',DIR_SYSTEM);
        $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);

        $title = $GLOBALS['magictoolbox']['prods_info']['name'];
        $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
        $description = $GLOBALS['magictoolbox']['prods_info']['description'];
        $description = htmlspecialchars(htmlspecialchars_decode($description, ENT_QUOTES));

        $img = preg_replace("/^.*?href\s*=\s*[\"\'].*\/(.*?)-\d+x\d+.*[\"\'].*$/is","$1",$matches[1]);
        $img = preg_replace('/([\(\)\-\+])/is','\\\$1',$img); // REALLY, all escaped now =)

        $original_image = false;
        if (isset($GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'])) {
            foreach ($GLOBALS['magictoolbox']['prods_info']['images'] as $image) {
            //if (preg_match('/.*?'.$img.'\.(png|jpg|jpeg|gif)/is',$image['image'])) {
            if (preg_match('/.*?'.$img.'(\-\d+x\d+)?\.(png|jpg|jpeg|gif)/is',$image['image'])) {
                $original_image = $image['image'];
            }
            }
        } else {
            $original_image = $GLOBALS['magictoolbox']['prods_info']['image'];
        }
        if (!$original_image) return $matches[0];

        $id = $GLOBALS['magictoolbox']['prods_info']['product_id'];

        $original_image = $image_dir.$original_image;
        $img = magiczoomplus_getThumb($original_image,'original',$id);
        $selector = magiczoomplus_getThumb($original_image,'selector',$id);
        $medium = magiczoomplus_getThumb($original_image,null,$id);
        $thumb = $selector;

        if ($plugin->type == 'standard') {

            if (!isset($GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'])) {
                $additional_result = $plugin->subTemplate(compact('title','img','medium','thumb','id'));
                $GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'] = '';// $additional_result;

                $thumb = magiczoomplus_getThumb($original_image,null,$id);
                $result = $plugin->template(compact('img','thumb','id','title','description'));

                $GLOBALS['magictoolbox']['MagicZoomPlus']['selectors'][] = $additional_result;
                $GLOBALS['magictoolbox']['MagicZoomPlus']['main'] = $result;

                return 'MAGICTOOLBOX_PLACEHOLDER';

            } else {
                $result = $plugin->subTemplate(compact('title','img','medium','thumb','id'));

                $GLOBALS['magictoolbox']['MagicZoomPlus']['selectors'][] = $result;
                return '';
            }
        } else if ($plugin->type == 'circle') {
            if (!isset ($GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'])) {
                $result = 'magiczoomplus_MAIN_IMAGE';
                $GLOBALS['magictoolbox'][strtoupper('magiczoomplus').'_MAIN_IMAGE_AFFECTED'] = $matches[0];
            } else {
		$GLOBALS['magictoolbox']['items'][] = array('medium' => $medium, 'img' => $thumb);
                $result = $matches[0];
            }
            
        }
    }
return $result;
}

function magiczoomplus_getProductParams ($id, $params = false) {
    if (!$params) $params = $GLOBALS['magictoolbox']['prods_info'];
    foreach ($params as $key=>$product_array) {
        if ($product_array['product_id'] == $id) {
            return $product_array;
        }
    }
}

function magiczoomplus_getThumb($src, $size = null, $pid = null) {
    if($size === null) $size = 'thumb';
    require_once (MTOOLBOX_ADMIN_FOLDER_magiczoomplus.'magictoolbox.imagehelper.class.php');
    
    if (defined('HTTP_IMAGE')) {
        $url = str_replace('image/','',HTTP_IMAGE);
    } else {
        $url = HTTP_SERVER;
    }
    $shop_dir = str_replace('system/','',DIR_SYSTEM);
    $image_dir = str_replace ($shop_dir,'',DIR_IMAGE);

    $imagehelper = new MagicToolboxImageHelperClass($shop_dir, '/'.$image_dir.'magictoolbox_cache', $GLOBALS["magictoolbox"]["magiczoomplus"]->params, null, $url);
    return $imagehelper->create('/' . $src, $size, $pid);
}

function magiczoomplus_set_params_from_config ($config = false) {
    if ($config) {
        $plugin = $GLOBALS["magictoolbox"]["magiczoomplus"];

        foreach ($plugin->params->getNames() as $name) {
            if ($config->get($name)) {
                $plugin->params->set($name,$config->get($name));
            }
        }
        foreach ($plugin->params->getArray() as $param) {
            if (!isset($param['value'])) {
                $plugin->params->set($param['id'],$plugin->params->getValue($param['id']));
            }
        }

        $plugin->general->appendArray($plugin->params->getArray());
    }
}

function magiczoomplus_use_effect_on(&$tool) {
    return !$tool->params->checkValue('use-effect-on-product-page','No') ||
           !$tool->params->checkValue('use-effect-on-category-page','No') ||
           !$tool->params->checkValue('use-effect-on-latest-box','No') ||
           !$tool->params->checkValue('use-effect-on-featured-box','No') ||
           !$tool->params->checkValue('use-effect-on-special-box','No') ||
           !$tool->params->checkValue('use-effect-on-bestsellers-box','No');
}

function magiczoomplus_fixProductCss ($content) {
    $columns = 0;
    $columnLeft = $columnRight = false;
    if (true == strpos($content,'<div id="column-left">')) {
        $columns++;
        $columnLeft = true;
    }
    if (true == strpos($content,'<div id="column-right">')) {
        $columns++;
        $columnRight = true;
    }
    $cssWidth = array('950','770','585');
    $css = '.product-info { overflow:visible !important; }
            #tabs { clear:both; }
            ';
    if ($columns != 0) $css .= '#content { float:left; width:'.$cssWidth[$columns].'px; margin-left:15px !important;  }';
    if ($columns == 2) $css .= '#content { margin-right:15px !important; }';
    if ($columns == 1 && $columnRight) $css .= '#content { margin-right:0px !important; }';
    $content = str_replace('</head>',"\n<style type=\"text/css\">".$css."</style>\n</head>",$content);
    return $content;
}

?>
