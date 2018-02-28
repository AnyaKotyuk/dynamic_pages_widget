<?php
/**
 * Created by PhpStorm.
 * User: Developer-01
 * Date: 24.01.2018
 * Time: 11:33
 */


class dynamic_pages_widget extends WP_Widget {

    private $pages; // page ids
    private $title_pages; // page title texts
    private $sub_pages; // page sub texts

    function __construct()
    {
        parent::__construct(
            'menu_sub_widget',
            __('Dynamic pages Widget', 'uni-text'),
            array('description' => __('Select pages,which you want to show', 'dp'),)
        );
    }

    /**
     * Show widget at frontside
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $sub_title = (isset($instance['sub_title']))?$instance['sub_title']:'';

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        $page_id = json_decode($instance['page_id'], true);
        $page_sub = json_decode($instance['page_sub'], true);
        $page_title = json_decode($instance['page_title'], true);
        ?>
        <div class="">
            <h5 class="side-menu2-title2"><?php echo $sub_title; ?></h5>
            <ul class="side-menu2">
            <?php foreach ($page_id as $k=>$item) {
                if (empty($item)) continue; ?>
                <li>
                    <a href="<?php echo get_permalink($item);?>">
                        <div><?php echo (isset($page_title[$k]))?$page_title[$k]:get_the_title($item);?></div>
                        <div><?php echo (isset($page_sub[$k]))?$page_sub[$k]:'';?></div>
                        <div><?php _e('Read more', 'dp'); ?></div>
                    </a>
                </li>
            <? } ?>
            </ul>
        </div>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Show widget in admin panel
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $title = (isset($instance['title']))?$instance['title']:__('Widget title', 'dp');
        $sub_title = (isset($instance['sub_title']))?$instance['sub_title']:__('Widget subtitle', 'dp');
        $pages = (isset($instance['page_id']))?json_decode($instance['page_id'], true):null;
        $this->pages = $pages;
        $this->title_pages = (isset($instance['page_title']))?json_decode($instance['page_title'], true):null;
        $this->sub_pages = (isset($instance['page_sub']))?json_decode($instance['page_sub'], true):null;

        ?>
        <input type="hidden" class="widget-updated">
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><b><?php _e( 'Title:' ); ?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'sub_title' ); ?>"><b><?php _e( 'Sub title:' ); ?></b></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'sub_title' ); ?>" name="<?php echo $this->get_field_name( 'sub_title' ); ?>" type="text" value="<?php echo esc_attr( $sub_title ); ?>" />
        </p>

        <div class="pages">
            <div class="hidden">
                <?php $this->showPageField(); ?>
            </div>
            <div class="pages-block">
                <?php
                if (!empty($pages)) {
                    foreach ($pages as $k=>$page) {
                        if ($k != 0) $this->showPageField($k);
                    }
                }
                ?>
            </div>
            <button class="add_page button widgets-chooser-cancel"><?php _e('Add page', 'dp'); ?></button>
            <script>
                jQuery(document).ready(function () {
                    <?php if(!empty($pages)) { ?>pagesInWidget = <?php echo count($pages); ?> <?php } ?>
                })
            </script>
        </div>
        <?php
    }

    /**
     * Save widget options
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['sub_title'] = ( ! empty( $new_instance['sub_title'] ) ) ? strip_tags( $new_instance['sub_title'] ) : '';
        $page_id = $new_instance['page_id'];

        $instance['page_id'] = ( ! empty( $page_id ) ) ? json_encode($page_id) : '';
        $instance['page_sub'] = ( ! empty( $page_id ) ) ? json_encode($new_instance['page_sub']) : '';
        $instance['page_title'] = ( ! empty( $page_id ) ) ? json_encode($new_instance['page_title']) : '';

        return $instance;
    }

    /**
     * Show one page element
     *
     * @param int $n
     */
    private function showPageField($n = 0)
    {
        $val = (isset($this->pages[$n]))?$this->pages[$n]:'';
        $page_title = (isset($this->title_pages[$n]))?$this->title_pages[$n]:'';
        $page_sub = (isset($this->sub_pages[$n]))?$this->sub_pages[$n]:'';
        if ($n != 0 && empty($val)) return;

        $inputs = array(
                array('id' => 'page_name', 'label' => 'Start typing page name:', 'val' => get_the_title($val)),
                array('id' => 'page_id', 'label' => 'Page id:', 'val' => $val),
                array('id' => 'page_title', 'label' => 'You can change page title for widget:', 'val' => $page_title),
                array('id' => 'page_sub', 'label' => 'This text will be under page link:', 'val' => $page_sub)
        );
        ?>
        <p>
            <?php
            foreach ($inputs as$k=>$input) {
                $this->showInput($input, $n);
            }
            ?>
           <span class="remove" onclick="jQuery(this).closest('form').find('.widget-updated').trigger('change'); jQuery(this).parent().remove();"><?php _e('Remove', 'dp'); ?></span>
        </p>
        <?php
    }


    /**
     * Show input
     *
     * @param $input
     * @param $n
     */
    private function showInput($input, $n)
    {
        if (empty($input['id'])) return;

        ?>
        <label for="<?php echo $this->get_field_id($input['id']); echo '['.$n.']'; ?>"><?php echo $input['label'] ?></label>
        <input
                class="<?php if($input['id'] == 'page_name') echo 'find'; ?> widefat"
                id="<?php echo $this->get_field_id( $input['id'] ); echo '['.$n.']'; ?>"
                name="<?php echo $this->get_field_name($input['id']); echo "[$n]"; ?>"
                type="text" value="<?php echo esc_attr($input['val']); ?>"
        />
        <?
    }
}


