<?php
App::uses('HtmlHelper', 'View/Helper');
class MenuHelper extends HtmlHelper {
    protected $tab = "  ";
    public function list_items($items = array(), $level = 0){
        $output = "";
        if(is_array($items)){
            $tabs = "\n" . str_repeat($this->tab, $level * 2);
            $li_tabs = $tabs . $this->tab;
            foreach($items as $label => $item){
                $target = (isset($item['target'])) ? $item['target'] : '#';
                $icon = (isset($item['icon'])) ? $item['icon'] : '';
                $label = (isset($item['label'])) ? $item['label'] : $label;
                $output .= $li_tabs.'<li';
                if(!empty($item['active'])){
                    $output .= ' class="active"';
                }
                $output .= '>';
                $labelTag = '<span';
                /*
                if(strlen($icon) > 0){
                    $labelTag .= ' style="background-image: url('.$this->webroot . $this->themeWeb . $icon.');"';
                }
                */
                $label = __($label, true);
                $labelTag .= ">$label</span>";
                $output .= $this->link($labelTag, $target, array('escape' => false));
                if(isset($item['items']) && sizeof($item['items']) > 0){
                    $output .= $li_tabs.$this->tab.'<ul>';
                    $output .= $this->list_items($item['items'], $level+1);
                    $output .= $li_tabs.$this->tab.'</ul>'.$li_tabs;
                }
                $output .= '</li>';
            }
        }
        return $output;
    }
}