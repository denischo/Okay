<?php

require_once('api/Okay.php');

class BannerAdmin extends Okay {
    
    public function fetch() {
        $categories = $this->categories->get_categories_tree();
        $brands     = $this->brands->get_brands();
        $pages      = $this->pages->get_pages();
        $products = $this->products->get_products(array('limit' => 100000, 'order' => 'name'));
        $banner = new stdClass;
        if($this->request->method('POST')) {
            $banner->id = $this->request->post('id', 'integer');
            $banner->name = $this->request->post('name');
            $banner->visible = $this->request->post('visible', 'boolean');
            $banner->show_all_pages = (int)$this->request->post('show_all_pages');
            $banner->categories = implode(",",$this->request->post('categories'));
            $banner->brands = implode(",",$this->request->post('brands'));
            $banner->pages = implode(",",$this->request->post('pages'));
            $banner->products = implode(",", $this->request->post('products'));
            
            if(empty($banner->id)) {
                $banner->id = $this->banners->add_banner($banner);
                $banner = $this->banners->get_banner($banner->id);
                $this->design->assign('message_success', 'added');
            } else {
                $this->banners->update_banner($banner->id, $banner);
                $banner = $this->banners->get_banner($banner->id);
                $this->design->assign('message_success', 'updated');
            }
            $banner->category_selected = $this->request->post('categories');
            $banner->brand_selected = $this->request->post('brands');
            $banner->page_selected = $this->request->post('pages');
            $banner->product_selected = $this->request->post('products');
        } else {
            $id = $this->request->get('id', 'integer');
            if(!empty($id)) {
                $banner = $this->banners->get_banner(intval($id));
                $banner->category_selected = explode(",",$banner->categories);//Создаем массив категорий
                $banner->brand_selected = explode(",",$banner->brands);//Создаем массив брендов
                $banner->page_selected = explode(",",$banner->pages);//Создаем массив страниц
                $banner->product_selected = explode(',', $banner->products);
            } else {
            	$banner->visible = 1;
            }
        }
        
        $this->design->assign('banner', $banner);
        $this->design->assign('categories', $categories);
        $this->design->assign('brands',     $brands);
        $this->design->assign('pages',      $pages);
        $this->design->assign('products', $products);
        
        return $this->design->fetch('banner.tpl');
    }
    
}
