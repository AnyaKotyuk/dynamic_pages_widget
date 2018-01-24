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
            __('Page subpages Widget', 'uni-text'),
            array('description' => __('Show subpages for current page', 'uni-text'),)
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
//        if ( ! empty( $title ) )
//            echo $args['before_title'] . $title . $args['after_title'];

        $page_id = json_decode($instance['page_id'], true);
        $page_sub = json_decode($instance['page_sub'], true);
        $page_title = json_decode($instance['page_title'], true);
        ?>
        <div class="side-menu2-wrapper nofix">
            <h4 class="side-menu2-title"><?php echo $title; ?></h4>
            <h5 class="side-menu2-title2"><?php echo $sub_title; ?></h5>
            <ul class="side-menu2">
            <?php foreach ($page_id as $k=>$item) {
                if (empty($item)) continue; ?>
                <li>
                    <a href="<?php echo get_permalink($item);?>">
                        <div><?php echo (isset($page_title[$k]))?$page_title[$k]:get_the_title($item);?></div>
                        <div><?php echo (isset($page_sub[$k]))?$page_sub[$k]:'';?></div>
                        <div>Read more</div>
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
        dynamic_pages_widget_enqueue_script();
        wp_localize_script('admin-dynamic-pages', 'php', array('ajax_url' => admin_url('admin-ajax.php')));
        $title = (isset($instance['title']))?$instance['title']:__('Widget title', 'uni-text');
        $sub_title = (isset($instance['sub_title']))?$instance['sub_title']:__('Widget subtitle', 'uni-text');
        $pages = (isset($instance['page_id']))?json_decode($instance['page_id'], true):null;
        $this->pages = $pages;
        $this->title_pages = (isset($instance['page_title']))?json_decode($instance['page_title'], true):null;
        $this->sub_pages = (isset($instance['page_sub']))?json_decode($instance['page_sub'], true):null;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'sub_title' ); ?>"><?php _e( 'Sub title:' ); ?></label>
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
            <button class="add_page"><?php _e('Add page', 'uni-text'); ?></button>
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
        ?>
        <hr>
        <p>
            <label for="<?php echo $this->get_field_id( 'page_id' ); echo '['.$n.']'; ?>"><?php _e( 'Page:' ); ?></label>
            <input class="find widefat" id="<?php echo $this->get_field_id( 'page_id' ); echo '['.$n.']'; ?>" name="<?php echo $this->get_field_name( 'page_name' ); echo "[$n]"; ?>" type="text" value="<?php echo esc_attr(get_the_title($val)); ?>" />
            <label class="hidden"><?php _e( 'Page id:' ); ?></label>
            <input class="widefat hidden page-id" id="<?php echo $this->get_field_id( 'page_id' ); echo '['.$n.']'; ?>" name="<?php echo $this->get_field_name( 'page_id' ); echo "[$n]"; ?>" type="text" value="<?php echo esc_attr($val); ?>" />
            <label><?php _e( 'Page name:' ); ?></label>
            <input class="widefat page-title" id="<?php echo $this->get_field_id( 'page_title' ); echo '['.$n.']'; ?>" name="<?php echo $this->get_field_name( 'page_title' ); echo "[$n]"; ?>" type="text" value="<?php echo esc_attr($page_title); ?>" />
             <label><?php _e( 'Page subtext:' ); ?></label>
            <input class="widefat page-sub" id="<?php echo $this->get_field_id( 'page_sub' ); echo '['.$n.']'; ?>" name="<?php echo $this->get_field_name( 'page_sub' ); echo "[$n]"; ?>" type="text" value="<?php echo esc_attr($page_sub); ?>" />
            <span style="color: red; cursor: pointer" class="remove" onclick="jQuery(this).parent().remove(); jQuery('#pages_sidebar input[type=submit]').prop('disable', false)"><?php _e( 'Remove' ); ?></span>
        </p>
        <?php
    }
}